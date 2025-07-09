<?php

namespace App\Http\Controllers;

use App\Models\Motorbike;
use App\Models\Album;
use App\Models\AlbumImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MotorbikeController extends Controller
{
    public function index()
    {
        $motorbikes = Motorbike::with('album')->get();
        return response()->json($motorbikes);
    }

    public function show($id)
    {
        $motorbike = Motorbike::with('album')->find($id);
        if (!$motorbike) {
            return response()->json(['message' => 'Không tìm thấy xe máy'], 404);
        }
        return response()->json($motorbike);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bike_type' => 'required|string|max:100',
            'price_per_day' => 'required|numeric|min:0',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        $albumId = null;

        if ($request->hasFile('image')) {
            $album = Album::create(['title' => 'Album xe máy ' . $request->bike_type]);
            $albumId = $album->album_id;

            $imagePath = $request->file('image')->store("albums/{$albumId}", 'public');
            AlbumImage::create([
                'album_id' => $albumId,
                'image_url' => $imagePath,
                'caption' => 'Ảnh đại diện',
                'is_deleted' => 'active'
            ]);
        }

        $motorbike = Motorbike::create([
            'bike_type' => $request->bike_type,
            'price_per_day' => $request->price_per_day,
            'location' => $request->location,
            'album_id' => $albumId,
            'is_deleted' => 'active',
        ]);

        return response()->json(['message' => 'Tạo xe máy thành công', 'motorbike' => $motorbike], 201);
    }

    public function update(Request $request, $id)
    {
        $motorbike = Motorbike::find($id);
        if (!$motorbike) {
            return response()->json(['message' => 'Không tìm thấy xe máy'], 404);
        }

        $request->validate([
            'bike_type' => 'sometimes|string|max:100',
            'price_per_day' => 'sometimes|numeric|min:0',
            'location' => 'sometimes|string|max:255',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image') && $motorbike->album_id) {
            $imagePath = $request->file('image')->store("albums/{$motorbike->album_id}", 'public');
            AlbumImage::create([
                'album_id' => $motorbike->album_id,
                'image_url' => $imagePath,
                'caption' => 'Cập nhật ảnh',
                'is_deleted' => 'active'
            ]);
        }

        $motorbike->fill($request->except('image'))->save();

        return response()->json(['message' => 'Cập nhật xe máy thành công', 'motorbike' => $motorbike]);
    }

    public function softDelete($id)
    {
        $motorbike = Motorbike::find($id);
        if (!$motorbike) {
            return response()->json(['message' => 'Không tìm thấy xe máy'], 404);
        }

        $motorbike->is_deleted = $motorbike->is_deleted === 'active' ? 'inactive' : 'active';
        $motorbike->save();

        return response()->json(['message' => 'Chuyển trạng thái thành công', 'motorbike' => $motorbike]);
    }

    public function destroy($id)
    {
        $motorbike = Motorbike::find($id);
        if (!$motorbike) {
            return response()->json(['message' => 'Không tìm thấy xe máy'], 404);
        }

        $motorbike->delete();

        return response()->json(['message' => 'Xóa xe máy thành công']);
    }

    public function trashed()
    {
        $trashed = Motorbike::where('is_deleted', 'inactive')->with('album')->get();
        return response()->json($trashed);
    }
}
