<?php

namespace App\Http\Controllers;

use App\Models\Tour;
use App\Models\TourCategory;
use App\Models\Album;
use App\Models\AlbumImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TourController extends Controller
{
    // Danh sách tour
    public function index()
    {
        $tours = Tour::with(['album.images', 'category', 'destinations'])->where('is_deleted', 'active')->get();
        return response()->json($tours);
    }

    // Tạo mới tour
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:tour_categories,category_id',
            'tour_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'itinerary' => 'nullable|string',
            'price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'destination' => 'nullable|string',
            'duration' => 'nullable|string',
            'status' => 'in:visible,hidden',
            'image' => 'required|image|',
            'images.*' => 'nullable|image|',
            'destination_ids' => 'nullable|array',
            'destination_ids.*' => 'exists:destinations,destination_id',
        ]);

        // Tạo album cho tour
        $album = Album::create([
            'title' => 'Album cho tour ' . $request->tour_name,
            'is_deleted' => 'active'
        ]);

        // Upload ảnh đại diện
        $imagePath = $request->file('image')->store('tours', 'public');

        // Tạo tour
        $tour = Tour::create([
            'category_id' => $request->category_id,
            'album_id' => $album->album_id,
            'tour_name' => $request->tour_name,
            'description' => $request->description,
            'itinerary' => $request->itinerary,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'destination' => $request->destination,
            'duration' => $request->duration,
            'status' => $request->status ?? 'visible',
            'image' => $imagePath,
            'is_deleted' => 'active'
        ]);

        // Lưu thêm ảnh album nếu có
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $img) {
                $path = $img->store('album_images', 'public');

                AlbumImage::create([
                    'album_id' => $album->album_id,
                    'image_url' => $path,
                    'caption' => null,
                    'is_deleted' => 'active'
                ]);
            }
        }

        // Gắn điểm đến
        $destinationIds = $request->input('destination_ids');

        if (!is_null($destinationIds)) {
            if (is_string($destinationIds)) {
                $destinationIds = array_map('trim', explode(',', $destinationIds));
            }

            if (is_array($destinationIds)) {
                $tour->destinations()->sync($destinationIds);
            }
        }


        return response()->json([
            'message' => 'Tạo tour thành công',
            'tour' => $tour->load(['category', 'album.images', 'destinations'])
        ], 201);
    }

    // Xem chi tiết
    public function show($id)
    {
        $tour = Tour::with(['category', 'album.images', 'destinations'])->findOrFail($id);

        if ($tour->is_deleted === 'inactive') {
            return response()->json(['message' => 'Tour đã bị xoá'], 404);
        }

        return response()->json($tour);
    }

    // Cập nhật
    public function update(Request $request, $id)
    {
        $tour = Tour::findOrFail($id);

        if ($tour->is_deleted === 'inactive') {
            return response()->json(['message' => 'Tour đã bị xoá'], 404);
        }

        $request->validate([
            'category_id' => 'exists:tour_categories,category_id',
            'tour_name' => 'string|max:255',
            'description' => 'nullable|string',
            'itinerary' => 'nullable|string',
            'price' => 'numeric',
            'discount_price' => 'nullable|numeric',
            'destination' => 'nullable|string',
            'duration' => 'nullable|string',
            'status' => 'in:visible,hidden',
            'image' => 'nullable|image|',
            'destination_ids' => 'nullable|array',
            'destination_ids.*' => 'exists:destinations,destination_id',
        ]);

        if ($request->hasFile('image')) {
            if ($tour->image)
                Storage::disk('public')->delete($tour->image);

            $tour->image = $request->file('image')->store('tours', 'public');
        }

        $tour->update($request->except('image'));

        if ($request->has('destination_ids')) {
            $tour->destinations()->sync($request->destination_ids);
        }

        return response()->json([
            'message' => 'Cập nhật tour thành công',
            'tour' => $tour->load(['category', 'album.images', 'destinations'])
        ]);
    }

    // Xóa mềm
    public function softDelete($id)
    {
        $tour = Tour::findOrFail($id);
        $tour->is_deleted = $tour->is_deleted === 'active' ? 'inactive' : 'active';
        $tour->save();

        return response()->json([
            'message' => $tour->is_deleted === 'inactive' ? 'Tour đã được ẩn' : 'Tour đã khôi phục',
            'tour' => $tour
        ]);
    }

    // Xoá vĩnh viễn
    public function destroy($id)
    {
        $tour = Tour::findOrFail($id);

        if ($tour->image) {
            Storage::disk('public')->delete($tour->image);
        }

        $tour->delete();

        return response()->json(['message' => 'Đã xoá tour vĩnh viễn']);
    }

    // Danh sách tour bị xoá mềm
    public function trashed()
    {
        $tours = Tour::with(['album.images', 'category', 'destinations'])->where('is_deleted', 'inactive')->get();
        return response()->json($tours);
    }
}