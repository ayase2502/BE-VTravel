<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DestinationController extends Controller
{
    // Liệt kê destinations với filter
    public function index(Request $request)
    {
        try {
            $query = Destination::active()->with('album');

            // Filter theo highlight
            if ($request->has('highlight')) {
                $query->where('highlight', $request->highlight);
            }

            // Search theo tên
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Filter theo location
            if ($request->has('location')) {
                $query->where('location', 'like', '%' . $request->location . '%');
            }

            // Sắp xếp
            $sortBy = $request->get('sort_by', 'destination_id');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Phân trang
            $perPage = $request->get('per_page', 15);
            $destinations = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $destinations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách destinations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Lấy destinations nổi bật
    public function highlighted()
    {
        try {
            $destinations = Destination::active()->highlight()->with('album')->get();
            return response()->json([
                'success' => true,
                'data' => $destinations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy destinations nổi bật',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $destination = Destination::active()->with('album')->find($id);
            if (!$destination) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy địa điểm'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $destination
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy chi tiết destination',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'album_id' => 'nullable|exists:albums,album_id',
                'image' => 'nullable|image|max:2048',
                'highlight' => 'nullable|in:yes,no'
            ], [
                'name.required' => 'Tên điểm đến là bắt buộc',
                'name.max' => 'Tên điểm đến không được vượt quá 255 ký tự',
                'album_id.exists' => 'Album không tồn tại',
                'image.image' => 'File phải là hình ảnh',
                'image.max' => 'Kích thước ảnh không được vượt quá 2MB',
                'highlight.in' => 'Trường highlight phải là yes hoặc no'
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('destinations', 'public');
            }

            $destination = Destination::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'location' => $validated['location'] ?? null,
                'album_id' => $validated['album_id'] ?? null,
                'image' => $imagePath,
                'highlight' => $validated['highlight'] ?? 'no',
                'is_deleted' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo địa điểm thành công',
                'data' => $destination
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo địa điểm',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $destination = Destination::find($id);
            if (!$destination || $destination->is_deleted === 'inactive') {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy địa điểm'
                ], 404);
            }

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'location' => 'nullable|string|max:255',
                'album_id' => 'nullable|exists:albums,album_id',
                'image' => 'nullable|image|max:2048',
                'highlight' => 'nullable|in:yes,no'
            ], [
                'name.max' => 'Tên điểm đến không được vượt quá 255 ký tự',
                'album_id.exists' => 'Album không tồn tại',
                'image.image' => 'File phải là hình ảnh',
                'image.max' => 'Kích thước ảnh không được vượt quá 2MB',
                'highlight.in' => 'Trường highlight phải là yes hoặc no'
            ]);

            if ($request->hasFile('image')) {
                if ($destination->image) {
                    Storage::disk('public')->delete($destination->image);
                }
                $destination->image = $request->file('image')->store('destinations', 'public');
            }

            $destination->fill($request->except('image'))->save();

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật địa điểm thành công',
                'data' => $destination
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi cập nhật địa điểm',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Chuyển đổi trạng thái highlight (dành cho admin/staff)
    public function toggleHighlight($id)
    {
        try {
            $destination = Destination::active()->find($id);
            if (!$destination) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy địa điểm'
                ], 404);
            }

            $destination->highlight = $destination->highlight === 'yes' ? 'no' : 'yes';
            $destination->save();

            return response()->json([
                'success' => true,
                'message' => 'Chuyển đổi trạng thái nổi bật thành công',
                'data' => [
                    'destination_id' => $destination->destination_id,
                    'name' => $destination->name,
                    'highlight' => $destination->highlight
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi chuyển đổi trạng thái nổi bật',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function softDelete($id)
    {
        try {
            $destination = Destination::find($id);
            if (!$destination) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy địa điểm'
                ], 404);
            }

            $destination->is_deleted = $destination->is_deleted === 'active' ? 'inactive' : 'active';
            $destination->save();

            return response()->json([
                'success' => true,
                'message' => 'Đã chuyển trạng thái thành công',
                'data' => $destination
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi chuyển trạng thái',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $destination = Destination::find($id);
            if (!$destination) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy địa điểm'
                ], 404);
            }

            if ($destination->image) {
                Storage::disk('public')->delete($destination->image);
            }
            $destination->delete();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa địa điểm vĩnh viễn'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa địa điểm',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function trashed()
    {
        try {
            $destinations = Destination::where('is_deleted', 'inactive')->with('album')->get();
            return response()->json([
                'success' => true,
                'data' => $destinations
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy destinations đã xóa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Thống kê destinations
    public function statistics()
    {
        try {
            $stats = [
                'total' => Destination::count(),
                'active' => Destination::active()->count(),
                'inactive' => Destination::where('is_deleted', 'inactive')->count(),
                'highlighted' => Destination::active()->highlight()->count(),
                'not_highlighted' => Destination::active()->where('highlight', 'no')->count(),
                'with_album' => Destination::active()->whereNotNull('album_id')->count(),
                'without_album' => Destination::active()->whereNull('album_id')->count(),
                'with_image' => Destination::active()->whereNotNull('image')->count(),
                'without_image' => Destination::active()->whereNull('image')->count()
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thống kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
