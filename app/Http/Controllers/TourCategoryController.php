<?php

namespace App\Http\Controllers;

use App\Models\TourCategory;
use Illuminate\Http\Request;

class TourCategoryController extends Controller
{
    // Lấy danh sách tất cả danh mục
    public function index()
    {
        return response()->json(TourCategory::all(), 200);
    }
    
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

        return response()->json([
            'message' => 'Tạo danh mục thành công',
            'category' => $category,
        ], 201);
    }


    // Lấy thông tin danh mục theo id
    public function show($id)
    {
        $category = TourCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Không tìm thấy danh mục'], 404);
        }

        return response()->json($category);
    }
}
