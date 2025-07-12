<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // Lấy danh sách booking active
    public function index()
    {
        $bookings = Booking::with(['user', 'tour', 'guide', 'hotel', 'busRoute', 'motorbike', 'customTour'])
                        ->where('is_deleted', 'active')
                        ->get();

        return response()->json($bookings);
    }

    // Lấy chi tiết theo id
    public function show($id)
    {
        $booking = Booking::with(['user', 'tour', 'guide', 'hotel', 'busRoute', 'motorbike', 'customTour'])
                        ->find($id);

        if (!$booking || $booking->is_deleted === 'inactive') {
            return response()->json(['message' => 'Booking không tồn tại'], 404);
        }

        return response()->json($booking);
    }

    // Tạo booking mới
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:users,id',
            'tour_id' => 'nullable|integer|exists:tours,tour_id',
            'custom_tour_id' => 'nullable|integer|exists:custom_tours,custom_tour_id',
            'guide_id' => 'nullable|integer|exists:guides,guide_id',
            'hotel_id' => 'nullable|integer|exists:hotels,hotel_id',
            'bus_route_id' => 'nullable|integer|exists:bus_routes,route_id',
            'motorbike_id' => 'nullable|integer|exists:motorbikes,bike_id',
            'quantity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'total_price' => 'required|numeric|min:0',
            'payment_method' => 'nullable|in:COD,bank_transfer,VNPay,MoMo',
            'status' => 'nullable|in:pending,confirmed,cancelled,completed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        $booking = Booking::create($request->all());

        return response()->json(['message' => 'Tạo booking thành công', 'booking' => $booking], 201);
    }

    // Cập nhật
    public function update(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['message' => 'Booking không tồn tại'], 404);
        }

        $validator = Validator::make($request->all(), [
            'tour_id' => 'nullable|integer|exists:tours,tour_id',
            'custom_tour_id' => 'nullable|integer|exists:custom_tours,custom_tour_id',
            'guide_id' => 'nullable|integer|exists:guides,guide_id',
            'hotel_id' => 'nullable|integer|exists:hotels,hotel_id',
            'bus_route_id' => 'nullable|integer|exists:bus_routes,route_id',
            'motorbike_id' => 'nullable|integer|exists:motorbikes,bike_id',
            'quantity' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'total_price' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:COD,bank_transfer,VNPay,MoMo',
            'status' => 'nullable|in:pending,confirmed,cancelled,completed',
            'cancel_reason' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Dữ liệu không hợp lệ', 'errors' => $validator->errors()], 422);
        }

        $booking->update($request->all());

        return response()->json(['message' => 'Cập nhật booking thành công', 'booking' => $booking]);
    }

    // Xóa mềm
    public function softDelete($id)
    {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['message' => 'Booking không tồn tại'], 404);

        $booking->is_deleted = $booking->is_deleted === 'active' ? 'inactive' : 'active';
        $booking->save();

        return response()->json(['message' => 'Cập nhật trạng thái booking', 'booking' => $booking]);
    }

    // Xóa vĩnh viễn
    public function destroy($id)
    {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['message' => 'Booking không tồn tại'], 404);

        $booking->delete();

        return response()->json(['message' => 'Xóa booking thành công']);
    }

    // Lấy danh sách booking đã xóa mềm
    public function trashed()
    {
        $trashed = Booking::with(['user', 'tour'])
                    ->where('is_deleted', 'inactive')
                    ->get();

        return response()->json($trashed);
    }
}
