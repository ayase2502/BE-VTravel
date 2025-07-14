<?php

namespace App\Http\Controllers;

use App\Models\{Destination, DestinationSection, Album, AlbumImage};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class DestinationController extends Controller
{
    private function decodeSections($sections)
    {
        return $sections->map(function ($section) {
            if (in_array($section->type, ['gallery', 'regionalDelicacies', 'highlight']) && is_string($section->content)) {
                $section->content = json_decode($section->content, true);
            }
            return $section;
        });
    }

    public function index()
    {
        $destinations = Destination::where('is_deleted', 'active')
            ->with('sections')
            ->get()
            ->map(function ($dest) {
                $dest->img_banner_url = $dest->img_banner ? asset('storage/' . $dest->img_banner) : null;
                $dest->sections = $this->decodeSections($dest->sections);
                return $dest;
            });

        return response()->json($destinations);
    }

    public function show($id)
    {
        $destination = Destination::where('is_deleted', 'active')
            ->with('sections')
            ->find($id);

        if (!$destination) {
            return response()->json(['message' => 'Không tìm thấy điểm đến'], 404);
        }

        $destination->img_banner_url = $destination->img_banner ? asset('storage/' . $destination->img_banner) : null;
        $destination->sections = $this->decodeSections($destination->sections);

        return response()->json($destination);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|numeric',
            'description' => 'nullable|string',
            'area' => 'nullable|string|max:100',
            'imgBanner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'sections' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        $sections = json_decode($request->input('sections'), true);
        if (!is_array($sections)) {
            return response()->json(['message' => 'Sections phải là mảng hợp lệ'], 422);
        }

        $albumId = null;
        $imagePath = null;

        if ($request->hasFile('imgBanner')) {
            $image = $request->file('imgBanner');
            $album = Album::create([
                'title' => $request->name . ' - Album',
                'is_deleted' => 'active'
            ]);
            $albumId = $album->album_id;

            $fileName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs("albums/{$albumId}", $fileName, 'public');

            AlbumImage::create([
                'album_id' => $albumId,
                'image_url' => $imagePath,
                'caption' => 'Ảnh banner điểm đến',
                'is_deleted' => 'active'
            ]);
        }

        $destination = Destination::create([
            'name' => $request->name,
            'album_id' => $albumId,
            'category_id' => $request->category_id,
            'description' => $request->description,
            'area' => $request->area,
            'img_banner' => $imagePath,
            'is_deleted' => 'active'
        ]);

        foreach ($sections as $section) {
            DestinationSection::create([
                'destination_id' => $destination->destination_id,
                'type' => $section['type'],
                'title' => $section['title'] ?? null,
                'content' => in_array($section['type'], ['gallery', 'regionalDelicacies', 'highlight'])
                    ? json_encode($section['content']) : ($section['content'] ?? null),
            ]);
        }

        $destination = Destination::with('sections')->find($destination->destination_id);
        $destination->img_banner_url = $destination->img_banner ? asset('storage/' . $destination->img_banner) : null;
        $destination->sections = $this->decodeSections($destination->sections);

        return response()->json(['message' => 'Tạo điểm đến thành công', 'destination' => $destination], 201);
    }

    public function update(Request $request, $id)
    {
        $destination = Destination::find($id);
        if (!$destination) return response()->json(['message' => 'Không tìm thấy điểm đến'], 404);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'area' => 'nullable|string|max:100',
            'imgBanner' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('imgBanner')) {
            if ($destination->img_banner && Storage::disk('public')->exists($destination->img_banner)) {
                Storage::disk('public')->delete($destination->img_banner);
            }
            $destination->img_banner = $request->file('imgBanner')->store('destinations/banners', 'public');
        }

        $destination->update($request->only(['name', 'description', 'area']));

        return response()->json(['message' => 'Cập nhật điểm đến thành công', 'destination' => $destination]);
    }

    public function softDelete($id)
    {
        $destination = Destination::find($id);
        if (!$destination) return response()->json(['message' => 'Không tìm thấy điểm đến'], 404);

        $destination->is_deleted = $destination->is_deleted === 'active' ? 'inactive' : 'active';
        $destination->save();

        return response()->json(['message' => 'Cập nhật trạng thái thành công', 'destination' => $destination]);
    }

    public function destroy($id)
    {
        $destination = Destination::find($id);
        if (!$destination) return response()->json(['message' => 'Không tìm thấy điểm đến'], 404);

        $destination->sections()->delete();
        $destination->delete();

        return response()->json(['message' => 'Xóa điểm đến thành công']);
    }

    public function trashed()
    {
        $destinations = Destination::where('is_deleted', 'inactive')->with('sections')->get();

        return response()->json($destinations);
    }
}
