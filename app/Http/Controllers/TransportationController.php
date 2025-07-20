<?php

namespace App\Http\Controllers;

use App\Models\Transportation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransportationController extends Controller
{
    /**
     * Lấy danh sách vận chuyển (Public + Admin)
     */
    public function index(Request $request)
    {
        try {
            $query = Transportation::active();

            // Filter theo type
            if ($request->has('type') && !empty($request->type)) {
                $query->byType($request->type);
            }

            // Search theo name, type
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('type', 'like', "%{$search}%");
                });
            }

            // Filter theo giá
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Sắp xếp
            $sortBy = $request->get('sort_by', 'transportation_id');
            $sortOrder = $request->get('sort_order', 'asc');
            $query->orderBy($sortBy, $sortOrder);

            // Include relationship
            if ($request->has('with_album')) {
                $query->with('album');
            }

            $transportations = $query->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $transportations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách phương tiện vận chuyển',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xem chi tiết vận chuyển
     */
    public function show($id)
    {
        try {
            $transportation = Transportation::active()
                ->with('album')
                ->where('transportation_id', $id)
                ->first();

            if (!$transportation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phương tiện vận chuyển'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $transportation
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin phương tiện vận chuyển',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo mới vận chuyển (Admin)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|string|max:100',
                'name' => 'required|string|max:100',
                'price' => 'required|numeric|min:0',
                'album_id' => 'nullable|integer|exists:albums,album_id'
            ], [
                'type.required' => 'Vui lòng nhập loại phương tiện',
                'type.max' => 'Loại phương tiện không được vượt quá 100 ký tự',
                'name.required' => 'Vui lòng nhập tên phương tiện',
                'name.max' => 'Tên phương tiện không được vượt quá 100 ký tự',
                'price.required' => 'Vui lòng nhập giá',
                'price.numeric' => 'Giá phải là số',
                'price.min' => 'Giá phải lớn hơn hoặc bằng 0',
                'album_id.exists' => 'Album không tồn tại'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $transportation = Transportation::create([
                'type' => $request->type,
                'name' => $request->name,
                'price' => $request->price,
                'album_id' => $request->album_id,
                'is_deleted' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo phương tiện vận chuyển thành công',
                'data' => $transportation->load('album')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo phương tiện vận chuyển',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật vận chuyển (Admin)
     */
    public function update(Request $request, $id)
    {
        try {
            $transportation = Transportation::active()->where('transportation_id', $id)->first();

            if (!$transportation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phương tiện vận chuyển'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'type' => 'sometimes|string|max:100',
                'name' => 'sometimes|string|max:100',
                'price' => 'sometimes|numeric|min:0',
                'album_id' => 'nullable|integer|exists:albums,album_id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $transportation->update($request->only(['type', 'name', 'price', 'album_id']));

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật phương tiện vận chuyển thành công',
                'data' => $transportation->fresh()->load('album')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật phương tiện vận chuyển',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa mềm vận chuyển (Admin)
     */
    public function softDelete($id)
    {
        try {
            $transportation = Transportation::active()->where('transportation_id', $id)->first();

            if (!$transportation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phương tiện vận chuyển'
                ], 404);
            }

            $transportation->update(['is_deleted' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => 'Xóa phương tiện vận chuyển thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa phương tiện vận chuyển',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Danh sách đã xóa (Admin)
     */
    public function trashed(Request $request)
    {
        try {
            $transportations = Transportation::inactive()
                ->with('album')
                ->orderBy('transportation_id', 'desc')
                ->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $transportations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách phương tiện đã xóa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Khôi phục vận chuyển (Admin)
     */
    public function restore($id)
    {
        try {
            $transportation = Transportation::inactive()->where('transportation_id', $id)->first();

            if (!$transportation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phương tiện đã xóa'
                ], 404);
            }

            $transportation->update(['is_deleted' => 'active']);

            return response()->json([
                'success' => true,
                'message' => 'Khôi phục phương tiện vận chuyển thành công',
                'data' => $transportation->fresh()->load('album')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể khôi phục phương tiện vận chuyển',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa vĩnh viễn (Admin)
     */
    public function destroy($id)
    {
        try {
            $transportation = Transportation::where('transportation_id', $id)->first();

            if (!$transportation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phương tiện vận chuyển'
                ], 404);
            }

            $transportation->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa vĩnh viễn phương tiện vận chuyển thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa vĩnh viễn phương tiện vận chuyển',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thống kê vận chuyển (Admin)
     */
    public function statistics()
    {
        try {
            $stats = [
                'total_transportations' => Transportation::active()->count(),
                'deleted_transportations' => Transportation::inactive()->count(),
                'by_type' => Transportation::active()
                    ->selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->get(),
                'average_price' => Transportation::active()->avg('price'),
                'highest_price' => Transportation::active()->max('price'),
                'lowest_price' => Transportation::active()->min('price')
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thống kê',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy theo loại (Public)
     */
    public function getByType($type)
    {
        try {
            $transportations = Transportation::active()
                ->byType($type)
                ->with('album')
                ->orderBy('price', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $transportations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách phương tiện theo loại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
