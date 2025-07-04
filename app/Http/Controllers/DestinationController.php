<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
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
            'album_id' => 'nullable|exists:albums,album_id',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('destinations', 'public');
        }

        $destination = Destination::create([
            'name' => $request->name,
            'description' => $request->description,
            'location' => $request->location,
            'album_id' => $request->album_id,
            'image' => $imagePath,
            'is_deleted' => 'active'
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
            'album_id' => 'nullable|exists:albums,album_id',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($destination->image) Storage::disk('public')->delete($destination->image);
            $destination->image = $request->file('image')->store('destinations', 'public');
        }

        $destination->fill($request->except('image'))->save();

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
}
