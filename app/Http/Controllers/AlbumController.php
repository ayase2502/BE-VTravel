<?php

namespace App\Http\Controllers;

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Thêm dòng này

class AlbumController extends Controller
{
    // Lấy danh sách album (chỉ active)
    public function index()
    {
        $albums = Album::where('is_deleted', 'active')
                      ->with(['images' => function($query) {
                          $query->take(1); // Lấy 1 hình đại diện
                      }])
                      ->get()
                      ->map(function ($album) {
                          // Thêm URL hình đại diện
                          $firstImage = $album->images->first();
                          $album->cover_image = $firstImage ? asset('storage/' . $firstImage->image_url) : null;
                          $album->images_count = $album->images()->count();
                          unset($album->images); // Xóa relation images để response gọn
                          return $album;
                      });

        return response()->json([
            'message' => 'Lấy danh sách album thành công',
            'albums' => $albums
        ]);
    }

    // Lấy thông tin album theo id với hình ảnh
    public function show($id)
    {
        $album = Album::where('is_deleted', 'active')
                     ->with(['images' => function($query) {
                         $query->where('is_deleted', 'active');
                     }])
                     ->find($id);

        if (!$album) {
            return response()->json(['message' => 'Không tìm thấy album'], 404);
        }

        // Thêm full URL cho images
        $album->images->map(function ($image) {
            $image->image_url_full = asset('storage/' . $image->image_url);
            return $image;
        });

        return response()->json([
            'message' => 'Lấy thông tin album thành công',
            'album' => $album
        ]);
    }

    // Tạo album mới
    public function store(Request $request)
    {
        // Kiểm tra quyền (chỉ admin/staff được tạo)
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'staff'])) {
            return response()->json(['message' => 'Bạn không có quyền tạo album'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:albums,title,NULL,album_id,is_deleted,active'
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề album',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'title.unique' => 'Tiêu đề album đã tồn tại'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $album = Album::create([
            'title' => $request->title,
            'is_deleted' => 'active'
        ]);

        return response()->json([
            'message' => 'Tạo album thành công',
            'album' => $album
        ], 201);
    }

    // Cập nhật album
    public function update(Request $request, $id)
    {
        $album = Album::where('is_deleted', 'active')->find($id);
        
        if (!$album) {
            return response()->json(['message' => 'Không tìm thấy album'], 404);
        }

        // Kiểm tra quyền
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'staff'])) {
            return response()->json(['message' => 'Bạn không có quyền cập nhật'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255|unique:albums,title,' . $id . ',album_id,is_deleted,active'
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề album',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'title.unique' => 'Tiêu đề album đã tồn tại'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $album->update([
            'title' => $request->title
        ]);

        return response()->json([
            'message' => 'Cập nhật album thành công',
            'album' => $album
        ]);
    }

    // Xóa mềm album (ẩn/hiện)
    public function softDelete($id)
    {
        $album = Album::find($id);
        
        if (!$album) {
            return response()->json(['message' => 'Không tìm thấy album'], 404);
        }

        // Kiểm tra quyền
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'staff'])) {
            return response()->json(['message' => 'Bạn không có quyền thực hiện'], 403);
        }

        // Đổi trạng thái
        $album->is_deleted = $album->is_deleted === 'active' ? 'inactive' : 'active';
        $album->save();

        $status = $album->is_deleted === 'active' ? 'khôi phục' : 'ẩn';

        return response()->json([
            'message' => "Đã {$status} album thành công",
            'album' => $album
        ]);
    }

    // Xóa vĩnh viễn album
    public function destroy($id)
    {
        $album = Album::find($id);
        
        if (!$album) {
            return response()->json(['message' => 'Không tìm thấy album'], 404);
        }

        // Chỉ admin được xóa vĩnh viễn
        $user = Auth::user();
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Chỉ admin mới được xóa vĩnh viễn'], 403);
        }

        // Xóa tất cả hình ảnh trong album trước
        $images = $album->allImages;
        foreach ($images as $image) {
            if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
                Storage::disk('public')->delete($image->image_url);
            }
            $image->delete();
        }

        $album->delete();

        return response()->json(['message' => 'Xóa album vĩnh viễn thành công']);
    }

    // Danh sách album đã ẩn
    public function trashed()
    {
        // Kiểm tra quyền
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'staff'])) {
            return response()->json(['message' => 'Bạn không có quyền xem'], 403);
        }

        $albums = Album::where('is_deleted', 'inactive')
                      ->with(['images' => function($query) {
                          $query->take(1);
                      }])
                      ->get()
                      ->map(function ($album) {
                          $firstImage = $album->images->first();
                          $album->cover_image = $firstImage ? asset('storage/' . $firstImage->image_url) : null;
                          $album->images_count = $album->allImages()->count();
                          unset($album->images);
                          return $album;
                      });

        return response()->json([
            'message' => 'Lấy danh sách album đã ẩn thành công',
            'albums' => $albums
        ]);
    }

    // Thống kê album
    public function statistics()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'staff'])) {
            return response()->json(['message' => 'Bạn không có quyền xem thống kê'], 403);
        }

        $stats = [
            'total_albums' => Album::count(),
            'active_albums' => Album::where('is_deleted', 'active')->count(),
            'inactive_albums' => Album::where('is_deleted', 'inactive')->count(),
            'total_images' => \App\Models\AlbumImage::count(),
            'active_images' => \App\Models\AlbumImage::where('is_deleted', 'active')->count()
        ];

        return response()->json([
            'message' => 'Thống kê album',
            'statistics' => $stats
        ]);
    }
}