<?php
namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Album;
use App\Models\AlbumImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::with('album')->get();
        return response()->json($hotels);
    }

    public function show($id)
    {
        $hotel = Hotel::with('album')->find($id);
        if (!$hotel) return response()->json(['message' => 'Không tìm thấy khách sạn'], 404);
        return response()->json($hotel);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'room_type' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $album = Album::create(['title' => 'Album khách sạn ' . $request->name]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('albums/' . $album->album_id, 'public');
            AlbumImage::create([
                'album_id' => $album->album_id,
                'image_url' => $imagePath,
                'caption' => 'Ảnh đại diện',
                'is_deleted' => 'active'
            ]);
        }

        $hotel = Hotel::create([
            'name' => $request->name,
            'location' => $request->location,
            'room_type' => $request->room_type,
            'price' => $request->price,
            'description' => $request->description,
            'image' => $imagePath,
            'album_id' => $album->album_id,
            'is_deleted' => 'active'
        ]);

        return response()->json(['message' => 'Tạo khách sạn thành công', 'hotel' => $hotel], 201);
    }

    public function update(Request $request, $id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) return response()->json(['message' => 'Không tìm thấy khách sạn'], 404);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'room_type' => 'nullable|string|max:100',
            'price' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('albums/' . $hotel->album_id, 'public');
            $hotel->image = $imagePath;

            AlbumImage::create([
                'album_id' => $hotel->album_id,
                'image_url' => $imagePath,
                'caption' => 'Ảnh cập nhật',
                'is_deleted' => 'active'
            ]);
        }

        $hotel->fill($request->except('image'))->save();

        return response()->json(['message' => 'Cập nhật khách sạn thành công', 'hotel' => $hotel]);
    }

    public function softDelete($id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) return response()->json(['message' => 'Không tìm thấy khách sạn'], 404);

        $hotel->is_deleted = $hotel->is_deleted === 'active' ? 'inactive' : 'active';
        $hotel->save();

        return response()->json(['message' => 'Đã chuyển trạng thái khách sạn thành công', 'hotel' => $hotel]);
    }

    public function destroy($id)
    {
        $hotel = Hotel::find($id);
        if (!$hotel) return response()->json(['message' => 'Không tìm thấy khách sạn'], 404);

        $hotel->delete();
        return response()->json(['message' => 'Đã xóa khách sạn vĩnh viễn']);
    }

    public function trashed()
    {
        $hotels = Hotel::where('is_deleted', 'inactive')->with('album')->get();
        return response()->json($hotels);
    }
}