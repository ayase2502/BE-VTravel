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
    public function index()
    {
        $tours = Tour::with(['album.images', 'category'])->get();
        return response()->json($tours);
    }

    public function store(Request $request)
    {
       $request->validate([
        'category_id' => 'required|exists:tour_categories,category_id',
        'album_id' => 'required|exists:albums,album_id',
        'tour_name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'itinerary' => 'nullable|string',
        'price' => 'required|numeric',
        'discount_price' => 'nullable|numeric',
        'destination' => 'nullable|string',
        'duration' => 'nullable|string',
        'status' => 'in:visible,hidden',
        'image' => 'nullable|image',
        'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', 
        'captions' => 'nullable|array',
    ]);

    // Upload ảnh đại diện
    $imagePath = null;
    if ($request->hasFile('image')) {
        $imagePath = $request->file('image')->store('tours', 'public');
    }

    $tour = Tour::create([
        'category_id' => $request->category_id,
        'album_id' => $request->album_id,
        'tour_name' => $request->tour_name,
        'description' => $request->description,
        'itinerary' => $request->itinerary,
        'price' => $request->price,
        'discount_price' => $request->discount_price,
        'destination' => $request->destination,
        'duration' => $request->duration,
        'status' => $request->status ?? 'visible',
        'image' => $imagePath,
    ]);

    // Upload nhiều ảnh album (nếu có)
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $img) {
            $path = $img->store('album_images', 'public');
            AlbumImage::create([
                'album_id' => $request->album_id,
                'image_url' => $path,
                'caption' => $request->captions[$index] ?? null,
            ]);
        }
    }

        return response()->json([
            'message' => 'Tạo tour thành công',
            'tour' => $tour
        ], 201);
    }

    public function show($id)
    {
        $tour = Tour::with(['category', 'album'])->findOrFail($id);
        return response()->json($tour);
    }

    public function update(Request $request, $id)
    {
        $tour = Tour::findOrFail($id);

        $request->validate([
            'category_id' => 'exists:tour_categories,category_id',
            'album_id' => 'exists:albums,album_id',
            'tour_name' => 'string|max:255',
            'description' => 'nullable|string',
            'itinerary' => 'nullable|string',
            'price' => 'numeric',
            'discount_price' => 'nullable|numeric',
            'destination' => 'nullable|string',
            'duration' => 'nullable|string',
            'status' => 'in:visible,hidden',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($tour->image) Storage::disk('public')->delete($tour->image);
            $tour->image = $request->file('image')->store('tours', 'public');
        }

        $tour->update($request->except('image'));

        return response()->json([
            'message' => 'Cập nhật tour thành công',
            'tour' => $tour
        ]);
    }

    public function destroy($id)
    {
        $tour = Tour::findOrFail($id);

        if ($tour->image) Storage::disk('public')->delete($tour->image);

        $tour->delete();

        return response()->json(['message' => 'Tour deleted successfully']);
    }
}
