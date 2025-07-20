<?php

namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\Tour;
use App\Models\Hotel;
use App\Models\Transportation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComboController extends Controller
{
    /**
     * Lấy danh sách combo (Public + Admin)
     */
    public function index(Request $request)
    {
        try {
            $query = Combo::active()
                ->with(['tour', 'hotel', 'transportation']);

            // Search
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%{$search}%")
                      ->orWhereHas('tour', function($tq) use ($search) {
                          $tq->where('name', 'like', "%{$search}%");
                      })
                      ->orWhereHas('hotel', function($hq) use ($search) {
                          $hq->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Filter theo giá
            if ($request->has('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }
            if ($request->has('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            // Filter theo tour
            if ($request->has('tour_id')) {
                $query->where('tour_id', $request->tour_id);
            }

            // Sắp xếp
            $sortBy = $request->get('sort_by', 'combo_id');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            $combos = $query->paginate($request->get('per_page', 12));

            return response()->json([
                'success' => true,
                'data' => $combos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách combo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xem chi tiết combo
     */
    public function show($id)
    {
        try {
            $combo = Combo::active()
                ->with(['tour', 'hotel', 'transportation'])
                ->where('combo_id', $id)
                ->first();

            if (!$combo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy combo'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $combo
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy thông tin combo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo mới combo (Admin)
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tour_id' => 'required|integer|exists:tours,tour_id',
                'hotel_id' => 'required|integer|exists:hotels,hotel_id',
                'transportation_id' => 'required|integer|exists:transportations,transportation_id',
                'price' => 'required|numeric|min:0',
                'description' => 'required|string|max:1000',
                'image' => 'nullable|string'
            ], [
                'tour_id.required' => 'Vui lòng chọn tour',
                'tour_id.exists' => 'Tour không tồn tại',
                'hotel_id.required' => 'Vui lòng chọn khách sạn',
                'hotel_id.exists' => 'Khách sạn không tồn tại',
                'transportation_id.required' => 'Vui lòng chọn phương tiện',
                'transportation_id.exists' => 'Phương tiện không tồn tại',
                'price.required' => 'Vui lòng nhập giá combo',
                'price.numeric' => 'Giá phải là số',
                'price.min' => 'Giá phải lớn hơn 0',
                'description.required' => 'Vui lòng nhập mô tả combo'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $combo = Combo::create([
                'tour_id' => $request->tour_id,
                'hotel_id' => $request->hotel_id,
                'transportation_id' => $request->transportation_id,
                'price' => $request->price,
                'description' => $request->description,
                'image' => $request->image,
                'is_deleted' => 'active'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo combo thành công',
                'data' => $combo->load(['tour', 'hotel', 'transportation'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo combo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật combo (Admin)
     */
    public function update(Request $request, $id)
    {
        try {
            $combo = Combo::active()->where('combo_id', $id)->first();

            if (!$combo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy combo'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'tour_id' => 'sometimes|integer|exists:tours,tour_id',
                'hotel_id' => 'sometimes|integer|exists:hotels,hotel_id',
                'transportation_id' => 'sometimes|integer|exists:transportations,transportation_id',
                'price' => 'sometimes|numeric|min:0',
                'description' => 'sometimes|string|max:1000',
                'image' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dữ liệu không hợp lệ',
                    'errors' => $validator->errors()
                ], 422);
            }

            $combo->update($request->only([
                'tour_id', 'hotel_id', 'transportation_id', 
                'price', 'description', 'image'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Cập nhật combo thành công',
                'data' => $combo->fresh()->load(['tour', 'hotel', 'transportation'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể cập nhật combo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa mềm combo (Admin)
     */
    public function softDelete($id)
    {
        try {
            $combo = Combo::active()->where('combo_id', $id)->first();

            if (!$combo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy combo'
                ], 404);
            }

            $combo->update(['is_deleted' => 'inactive']);

            return response()->json([
                'success' => true,
                'message' => 'Xóa combo thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa combo',
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
            $combos = Combo::inactive()
                ->with(['tour', 'hotel', 'transportation'])
                ->orderBy('combo_id', 'desc')
                ->paginate($request->get('per_page', 10));

            return response()->json([
                'success' => true,
                'data' => $combos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy danh sách combo đã xóa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Khôi phục combo (Admin)
     */
    public function restore($id)
    {
        try {
            $combo = Combo::inactive()->where('combo_id', $id)->first();

            if (!$combo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy combo đã xóa'
                ], 404);
            }

            $combo->update(['is_deleted' => 'active']);

            return response()->json([
                'success' => true,
                'message' => 'Khôi phục combo thành công',
                'data' => $combo->fresh()->load(['tour', 'hotel', 'transportation'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể khôi phục combo',
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
            $combo = Combo::where('combo_id', $id)->first();

            if (!$combo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy combo'
                ], 404);
            }

            $combo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Xóa vĩnh viễn combo thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa vĩnh viễn combo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thống kê combo (Admin)
     */
    public function statistics()
    {
        try {
            $stats = [
                'total_combos' => Combo::active()->count(),
                'deleted_combos' => Combo::inactive()->count(),
                'average_price' => Combo::active()->avg('price'),
                'highest_price' => Combo::active()->max('price'),
                'lowest_price' => Combo::active()->min('price'),
                'total_discount_amount' => Combo::active()->get()->sum('discount_amount'),
                'average_discount_percent' => Combo::active()->get()->avg('discount_percent')
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
     * Combo hot (Public)
     */
    public function hotCombos()
    {
        try {
            $combos = Combo::active()
                ->with(['tour', 'hotel', 'transportation'])
                ->orderBy('combo_id', 'desc')
                ->limit(8)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $combos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể lấy combo hot',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
