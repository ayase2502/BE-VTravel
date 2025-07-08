<?php

namespace App\Http\Controllers;

use App\Models\Guide;
use App\Models\Album;
use App\Models\AlbumImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuideController extends Controller
{
    public function index()
    {
        $guides = Guide::with('album')->get();
        return response()->json($guides);
    }

    public function show($id)
    {
        $guide = Guide::where('is_deleted', 'active')->with('album')->find($id);
        if (!$guide) return response()->json(['message' => 'Không tìm thấy hướng dẫn viên'], 404);
        return response()->json($guide);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'gender' => 'nullable|in:male,female',
            'language' => 'nullable|string|max:50',
            'experience_years' => 'nullable|integer',
            'image' => 'nullable|image|max:2048'
        ]);

        $albumId = null;
        $imagePath = null;

        if ($request->hasFile('image')) {
            $album = Album::create(['title' => 'Album cho HDV ' . $request->name]);
            $albumId = $album->album_id;

            $imagePath = $request->file('image')->store('albums/' . $albumId, 'public');
            AlbumImage::create([
                'album_id' => $albumId,
                'image_url' => $imagePath,
                'caption' => 'Ảnh đại diện',
                'is_deleted' => 'active'
            ]);
        }

        $guide = Guide::create([
            'name' => $request->name,
            'gender' => $request->gender,
            'language' => $request->language,
            'experience_years' => $request->experience_years,
            'album_id' => $albumId,
            'is_deleted' => 'active'
        ]);

        return response()->json(['message' => 'Tạo hướng dẫn viên thành công', 'guide' => $guide], 201);
    }

    public function update(Request $request, $id)
    {
        $guide = Guide::find($id);
        if (!$guide || $guide->is_deleted === 'inactive') {
            return response()->json(['message' => 'Không tìm thấy hướng dẫn viên'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:100',
            'gender' => 'nullable|in:male,female',
            'language' => 'nullable|string|max:50',
            'experience_years' => 'nullable|integer',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($guide->album_id) {
                $imagePath = $request->file('image')->store('albums/' . $guide->album_id, 'public');
                AlbumImage::create([
                    'album_id' => $guide->album_id,
                    'image_url' => $imagePath,
                    'caption' => 'Ảnh cập nhật',
                    'is_deleted' => 'active'
                ]);
            }
        }

        $guide->fill($request->except('image'))->save();

        return response()->json(['message' => 'Cập nhật hướng dẫn viên thành công', 'guide' => $guide]);
    }

    public function toggleDelete($id)
    {
        $guide = Guide::find($id);
        if (!$guide) return response()->json(['message' => 'Không tìm thấy hướng dẫn viên'], 404);

        $guide->is_deleted = $guide->is_deleted === 'active' ? 'inactive' : 'active';
        $guide->save();

        return response()->json(['message' => 'Đã chuyển trạng thái thành công', 'guide' => $guide]);
    }

    public function destroy($id)
    {
        $guide = Guide::find($id);
        if (!$guide) return response()->json(['message' => 'Không tìm thấy hướng dẫn viên'], 404);

        $guide->delete();

        return response()->json(['message' => 'Đã xóa hướng dẫn viên vĩnh viễn']);
    }

    public function trashed()
    {
        $guides = Guide::where('is_deleted', 'inactive')->with('album')->get();
        return response()->json($guides);
    }
}
