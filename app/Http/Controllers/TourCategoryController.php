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
