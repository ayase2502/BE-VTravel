<?php
namespace App\Http\Controllers;

use App\Models\BusRoute;
use App\Models\Album;
use App\Models\AlbumImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusRouteController extends Controller
{
    public function index()
    {
        return response()->json(BusRoute::with('album')->get());
    }

    public function show($id)
    {
        $route = BusRoute::with('album')->find($id);
        return $route
            ? response()->json($route)
            : response()->json(['message' => 'Không tìm thấy tuyến xe'], 404);
    }

    public function store(Request $request)
    {
        $request->validate([
            'route_name' => 'required|string|max:255',
            'vehicle_type' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'seats' => 'required|integer|min:1',
            'image' => 'nullable|image|max:2048'
        ]);

        $albumId = null;

        if ($request->hasFile('image')) {
            $album = Album::create(['title' => 'Album cho tuyến ' . $request->route_name]);
            $albumId = $album->album_id;
            $imagePath = $request->file('image')->store("albums/{$albumId}", 'public');
            AlbumImage::create([
                'album_id' => $albumId,
                'image_url' => $imagePath,
                'caption' => 'Ảnh đại diện',
                'is_deleted' => 'active'
            ]);
        }

        $route = BusRoute::create([
            'route_name' => $request->route_name,
            'vehicle_type' => $request->vehicle_type,
            'price' => $request->price,
            'seats' => $request->seats,
            'album_id' => $albumId,
            'is_deleted' => 'active',
        ]);

        return response()->json(['message' => 'Tạo tuyến xe thành công', 'route' => $route], 201);
    }

    public function update(Request $request, $id)
    {
        $route = BusRoute::find($id);
        if (!$route) return response()->json(['message' => 'Không tìm thấy tuyến xe'], 404);

        $request->validate([
            'route_name' => 'sometimes|string|max:255',
            'vehicle_type' => 'sometimes|string|max:100',
            'price' => 'sometimes|numeric|min:0',
            'seats' => 'sometimes|integer|min:1',
            'image' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('image') && $route->album_id) {
            $imagePath = $request->file('image')->store("albums/{$route->album_id}", 'public');
            AlbumImage::create([
                'album_id' => $route->album_id,
                'image_url' => $imagePath,
                'caption' => 'Cập nhật ảnh',
                'is_deleted' => 'active'
            ]);
        }

        $route->fill($request->except('image'))->save();

        return response()->json(['message' => 'Cập nhật tuyến xe thành công', 'route' => $route]);
    }

    public function softDelete($id)
    {
        $route = BusRoute::find($id);
        if (!$route) return response()->json(['message' => 'Không tìm thấy tuyến xe'], 404);

        $route->is_deleted = $route->is_deleted === 'active' ? 'inactive' : 'active';
        $route->save();

        return response()->json(['message' => 'Chuyển trạng thái thành công', 'route' => $route]);
    }

    public function destroy($id)
    {
        $route = BusRoute::find($id);
        if (!$route) return response()->json(['message' => 'Không tìm thấy tuyến xe'], 404);
        $route->delete();

        return response()->json(['message' => 'Đã xóa tuyến xe']);
    }

    public function trashed()
    {
        return response()->json(BusRoute::where('is_deleted', 'inactive')->with('album')->get());
    }
}
