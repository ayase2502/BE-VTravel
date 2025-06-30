<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
    // Lấy danh sách tất cả album
    public function index()
    {
        return response()->json(Album::all(), 200);
    }

    // Lấy thông tin album theo id
    public function show($id)
    {
        $album = Album::find($id);

        if (!$album) {
            return response()->json(['message' => 'Không tìm thấy album'], 404);
        }

        return response()->json($album);
    }
}