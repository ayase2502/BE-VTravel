<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\AlbumImage;
use Illuminate\Support\Facades\Storage;

class DestinationController extends Controller
{
    public function index()
    {
        $destinations = Destination::where('is_deleted', 'active')->with('album')->get();
        return response()->json($destinations);
    }

    public function show($id)
    {
        $destination = Destination::where('is_deleted', 'active')->with('album')->find($id);
        if (!$destination) return response()->json(['message' => 'Không tìm thấy địa điểm'], 404);
        return response()->json($destination);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'is_highlight' => 'nullable|in:yes,no',
        ]);

        $albumId = null;
        $imagePath = null;

        if ($request->hasFile('image')) {
            $album = Album::create(['title' => 'Album cho ' . $request->name]);
            $albumId = $album->album_id;
            $imagePath = $request->file('image')->store("albums/{$albumId}", 'public');
            AlbumImage::create([
                'album_id' => $albumId,
                'image_url' => $imagePath,
                'caption' => 'Ảnh đại diện',
                'is_deleted' => 'active'
            ]);
        }

        $destination = Destination::create([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'album_id' => $albumId,
            'image' => $imagePath,
            'is_deleted' => 'active',
            'is_highlight' => $request->input('is_highlight', 'no')
        ]);

        return response()->json(['message' => 'Tạo địa điểm thành công', 'destination' => $destination], 201);
    }

    public function update(Request $request, $id)
    {
        $destination = Destination::find($id);
        if (!$destination || $destination->is_deleted === 'inactive') {
            return response()->json(['message' => 'Không tìm thấy địa điểm'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            // 'album_id' => 'nullable|exists:albums,album_id',
            'image' => 'nullable|image|max:2048',
            'is_highlight' => 'nullable|in:yes,no',
        ]);

        if ($request->hasFile('image') && $destination->album_id) {
            $imagePath = $request->file('image')->store("albums/{$destination->album_id}", 'public');
            AlbumImage::create([
                'album_id' => $destination->album_id,
                'image_url' => $imagePath,
                'caption' => 'Cập nhật ảnh',
                'is_deleted' => 'active'
            ]);
        }

        $destination->fill($request->except('image'))->save();

        if ($request->has('is_highlight')) {
            $destination->is_highlight = $request->is_highlight;
            $destination->save();
        }

        return response()->json(['message' => 'Cập nhật địa điểm thành công', 'destination' => $destination]);
    }

    public function softDelete($id)
    {
        $destination = Destination::find($id);
        if (!$destination) return response()->json(['message' => 'Không tìm thấy địa điểm'], 404);

        $destination->is_deleted = $destination->is_deleted === 'active' ? 'inactive' : 'active';
        $destination->save();

        return response()->json(['message' => 'Đã chuyển trạng thái thành công', 'destination' => $destination]);
    }

    public function destroy($id)
    {
        $destination = Destination::find($id);
        if (!$destination) return response()->json(['message' => 'Không tìm thấy địa điểm'], 404);

        if ($destination->image) Storage::disk('public')->delete($destination->image);
        $destination->delete();

        return response()->json(['message' => 'Đã xóa địa điểm vĩnh viễn']);
    }

    public function trashed()
    {
        $destinations = Destination::where('is_deleted', 'inactive')->with('album')->get();
        return response()->json($destinations);
    }

    public function toggleHighlight($id)
    {
        $destination = Destination::find($id);
        if (!$destination || $destination->is_deleted === 'inactive') {
            return response()->json(['message' => 'Không tìm thấy địa điểm'], 404);
        }

        $destination->is_highlight = $destination->is_highlight === 'yes' ? 'no' : 'yes';
        $destination->save();

        return response()->json([
            'message' => 'Cập nhật trạng thái nổi bật thành công',
            'is_highlight' => $destination->is_highlight
        ]);
    }

    public function highlights()
    {
        $destinations = Destination::where([
            ['is_deleted', 'active'],
            ['is_highlight', 'yes']
        ])->with('album')->get();

        return response()->json($destinations);
    }
}
