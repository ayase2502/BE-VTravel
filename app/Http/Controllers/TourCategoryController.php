<?php

namespace App\Http\Controllers;

use App\Models\TourCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TourCategoryController extends Controller
{
    // Lấy danh sách tất cả danh mục
    public function index()
    {
        $categories = TourCategory::all()->map(function ($category) {
            $category->thumbnail_url = $category->thumbnail
                ? asset('storage/' . $category->thumbnail)
                : null;
            return $category;
        });

        return response()->json($categories, 200);
    }

    // Tạo danh mục mới
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:100',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('categories', 'public');
        }

        $category = TourCategory::create([
            'category_name' => $request->category_name,
            'thumbnail' => $thumbnailPath,
        ]);

        $category->thumbnail_url = $thumbnailPath ? asset('storage/' . $thumbnailPath) : null;

        return response()->json([
            'message' => 'Tạo danh mục thành công',
            'category' => $category,
        ], 201);
    }

    // Lấy thông tin danh mục theo ID
    public function show($id)
    {
        $category = TourCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Không tìm thấy danh mục'], 404);
        }

        $category->thumbnail_url = $category->thumbnail
            ? asset('storage/' . $category->thumbnail)
            : null;

        return response()->json($category);
    }

    // Cập nhật danh mục
    public function update(Request $request, $id)
    {
        $category = TourCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Không tìm thấy danh mục'], 404);
        }

        $request->validate([
            'category_name' => 'sometimes|string|max:100',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        // Nếu có ảnh mới thì xóa ảnh cũ và lưu ảnh mới
        if ($request->hasFile('thumbnail')) {
            if ($category->thumbnail && Storage::disk('public')->exists($category->thumbnail)) {
                Storage::disk('public')->delete($category->thumbnail);
            }
            $category->thumbnail = $request->file('thumbnail')->store('categories', 'public');
        }

        if ($request->filled('category_name')) {
            $category->category_name = $request->category_name;
        }

        $category->save();

        $category->thumbnail_url = $category->thumbnail
            ? asset('storage/' . $category->thumbnail)
            : null;

        return response()->json([
            'message' => 'Cập nhật danh mục thành công',
            'category' => $category,
        ]);
    }

    // Xóa danh mục
    public function destroy($id)
    {
        $category = TourCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Không tìm thấy danh mục'], 404);
        }

        if ($category->thumbnail && Storage::disk('public')->exists($category->thumbnail)) {
            Storage::disk('public')->delete($category->thumbnail);
        }

        $category->delete();

        return response()->json(['message' => 'Xóa danh mục thành công']);
    }
}