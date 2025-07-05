<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AlbumImage;
use App\Models\Album;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AlbumImageController extends Controller
{
    // Lấy danh sách hình ảnh của album
    public function index($albumId)
    {
        $album = Album::find($albumId);
        if (!$album) {
            return response()->json(['message' => 'Không tìm thấy album'], 404);
        }

        $images = AlbumImage::where('album_id', $albumId)
            ->where('is_deleted', 'active')
            ->orderBy('image_id', 'desc')
            ->get()
            ->map(function ($image) {
                $image->image_url_full = $image->image_url_full; // Sử dụng accessor
                return $image;
            });

        return response()->json([
            'message' => 'Lấy danh sách hình ảnh thành công',
            'album' => $album,
            'images' => $images
        ]);
    }

    // Thêm hình ảnh vào album
    public function store(Request $request, $albumId)
    {
        $album = Album::where('is_deleted', 'active')->find($albumId);
        if (!$album) {
            return response()->json(['message' => 'Không tìm thấy album'], 404);
        }

        // Sửa validation để linh hoạt hơn
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'images.required' => 'Vui lòng chọn ít nhất 1 hình ảnh',
            'images.array' => 'Images phải là array',
            'images.max' => 'Không được upload quá 10 hình ảnh cùng lúc',
            'images.*.required' => 'File hình ảnh không được để trống',
            'images.*.image' => 'File phải là hình ảnh',
            'images.*.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp',
            'images.*.max' => 'Kích thước hình ảnh không được vượt quá 5MB'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $uploadedImages = [];
        $images = $request->file('images');
        $captions = $request->input('captions', []);

        foreach ($images as $index => $image) {
            try {
                // Tạo tên file unique
                $fileName = time() . '_' . $index . '_' . $image->getClientOriginalName();
                $imagePath = $image->storeAs('albums/' . $albumId, $fileName, 'public');
                
                $albumImage = AlbumImage::create([
                    'album_id' => $albumId,
                    'image_url' => $imagePath,
                    'caption' => $captions[$index] ?? null,
                    'is_deleted' => 'active'
                ]);

                $albumImage->image_url_full = $albumImage->image_url_full;
                $uploadedImages[] = $albumImage;

            } catch (\Exception $e) {
                // Xóa các file đã upload nếu có lỗi
                foreach ($uploadedImages as $uploaded) {
                    if (Storage::disk('public')->exists($uploaded->image_url)) {
                        Storage::disk('public')->delete($uploaded->image_url);
                    }
                    $uploaded->delete();
                }

                return response()->json([
                    'message' => 'Lỗi khi upload hình ảnh',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'message' => 'Thêm hình ảnh thành công',
            'images' => $uploadedImages
        ], 201);
    }

    // Xem chi tiết một hình ảnh
    public function show($albumId, $imageId)
    {
        $image = AlbumImage::where('album_id', $albumId)
            ->where('image_id', $imageId)
            ->where('is_deleted', 'active')
            ->with('album')
            ->first();

        if (!$image) {
            return response()->json(['message' => 'Không tìm thấy hình ảnh'], 404);
        }

        $image->image_url_full = $image->image_url_full;

        return response()->json([
            'message' => 'Lấy thông tin hình ảnh thành công',
            'image' => $image
        ]);
    }

    // Cập nhật thông tin hình ảnh (caption và/hoặc file ảnh)
    public function update(Request $request, $albumId, $imageId)
    {
        $image = AlbumImage::where('album_id', $albumId)
            ->where('image_id', $imageId)
            ->where('is_deleted', 'active')
            ->first();

        if (!$image) {
            return response()->json(['message' => 'Không tìm thấy hình ảnh'], 404);
        }

        // Validation linh hoạt - có thể chỉ caption, chỉ image, hoặc cả hai
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // nullable để không bắt buộc
            'caption' => 'nullable|string|max:255'
        ], [
            'image.image' => 'File phải là hình ảnh',
            'image.mimes' => 'Hình ảnh phải có định dạng: jpeg, png, jpg, gif, webp',
            'image.max' => 'Kích thước hình ảnh không được vượt quá 5MB',
            'caption.max' => 'Mô tả không được vượt quá 255 ký tự'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $updateData = [];

            // Nếu có file ảnh mới, xử lý upload
            if ($request->hasFile('image')) {
                // Xóa file cũ
                if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
                    Storage::disk('public')->delete($image->image_url);
                }

                // Upload file mới
                $newImage = $request->file('image');
                $fileName = time() . '_' . $newImage->getClientOriginalName();
                $imagePath = $newImage->storeAs('albums/' . $albumId, $fileName, 'public');
                
                $updateData['image_url'] = $imagePath;
            }

            // Nếu có caption, cập nhật caption
            if ($request->has('caption')) {
                $updateData['caption'] = $request->input('caption');
            }

            // Kiểm tra có dữ liệu để cập nhật không
            if (empty($updateData)) {
                return response()->json([
                    'message' => 'Không có dữ liệu để cập nhật',
                    'note' => 'Vui lòng gửi ít nhất một trong: image hoặc caption'
                ], 400);
            }

            // Cập nhật database
            $image->update($updateData);
            $image->image_url_full = $image->image_url_full;

            $updatedFields = [];
            if (isset($updateData['image_url'])) $updatedFields[] = 'hình ảnh';
            if (isset($updateData['caption'])) $updatedFields[] = 'mô tả';

            return response()->json([
                'message' => 'Cập nhật ' . implode(' và ', $updatedFields) . ' thành công',
                'image' => $image
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Lỗi khi cập nhật hình ảnh',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Xóa mềm hình ảnh
    public function softDelete($albumId, $imageId)
    {
        $image = AlbumImage::where('album_id', $albumId)
            ->where('image_id', $imageId)
            ->first();

        if (!$image) {
            return response()->json(['message' => 'Không tìm thấy hình ảnh'], 404);
        }

        $image->is_deleted = $image->is_deleted === 'active' ? 'inactive' : 'active';
        $image->save();

        $status = $image->is_deleted === 'active' ? 'khôi phục' : 'ẩn';

        return response()->json([
            'message' => "Đã {$status} hình ảnh thành công",
            'image' => $image
        ]);
    }

    // Xóa vĩnh viễn hình ảnh
    public function destroy($albumId, $imageId)
    {
        $image = AlbumImage::where('album_id', $albumId)
            ->where('image_id', $imageId)
            ->first();

        if (!$image) {
            return response()->json(['message' => 'Không tìm thấy hình ảnh'], 404);
        }

        // Xóa file khỏi storage
        if ($image->image_url && Storage::disk('public')->exists($image->image_url)) {
            Storage::disk('public')->delete($image->image_url);
        }

        $image->delete();

        return response()->json(['message' => 'Xóa hình ảnh vĩnh viễn thành công']);
    }

    // Lấy danh sách hình ảnh đã xóa mềm
    public function trashed($albumId)
    {
        $album = Album::find($albumId);
        if (!$album) {
            return response()->json(['message' => 'Không tìm thấy album'], 404);
        }

        $images = AlbumImage::where('album_id', $albumId)
            ->where('is_deleted', 'inactive')
            ->orderBy('image_id', 'desc')
            ->get()
            ->map(function ($image) {
                $image->image_url_full = $image->image_url_full;
                return $image;
            });

        return response()->json([
            'message' => 'Lấy danh sách hình ảnh đã ẩn thành công',
            'album' => $album,
            'trashed_images' => $images
        ]);
    }

    // Thống kê hình ảnh theo album
    public function statistics($albumId)
    {
        $album = Album::find($albumId);
        if (!$album) {
            return response()->json(['message' => 'Không tìm thấy album'], 404);
        }

        $stats = [
            'album_info' => $album,
            'total_images' => AlbumImage::where('album_id', $albumId)->count(),
            'active_images' => AlbumImage::where('album_id', $albumId)->where('is_deleted', 'active')->count(),
            'inactive_images' => AlbumImage::where('album_id', $albumId)->where('is_deleted', 'inactive')->count(),
        ];

        return response()->json([
            'message' => 'Thống kê hình ảnh album',
            'statistics' => $stats
        ]);
    }
}