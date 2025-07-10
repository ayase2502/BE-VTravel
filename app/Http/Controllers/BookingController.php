<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // Danh sách đơn đặt còn hoạt động
    public function index()
    {
        $bookings = Booking::get()->all();
        return response()->json($bookings);
    }

    // Danh sách đã xóa mềm
    public function trashed()
    {
        $bookings = Booking::where('is_deleted', 'inactive')->with('user')->get();
        return response()->json($bookings);
    }

    // Chi tiết booking
    public function show($id)
    {
        $booking = Booking::where('is_deleted', 'active')->with('user')->find($id);
        if (!$booking) return response()->json(['message' => 'Không tìm thấy booking'], 404);
        return response()->json($booking);
    }

    // Tạo mới booking
    public function store(Request $request)
    {
        $request->validate([
            'booking_type' => 'required|in:tour,combo,hotel,transport,motorbike,guide,bus',
            'related_id' => 'required|integer',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'quantity' => 'required|integer|min:1',
            'total_price' => 'required|numeric',
            'payment_method' => 'required|in:COD,bank_transfer,VNPay,MoMo'
        ]);

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'booking_type' => $request->booking_type,
            'related_id' => $request->related_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'quantity' => $request->quantity,
            'total_price' => $request->total_price,
            'payment_method' => $request->payment_method,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Tạo đơn đặt thành công', 'booking' => $booking], 201);
    }

    // Cập nhật booking
    public function update(Request $request, $id)
    {
        $booking = Booking::where('is_deleted', 'active')->find($id);
        if (!$booking) return response()->json(['message' => 'Không tìm thấy booking'], 404);

        $request->validate([
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'quantity' => 'sometimes|integer|min:1',
            'total_price' => 'sometimes|numeric',
            'status' => 'in:pending,confirmed,cancelled,completed',
            'cancel_reason' => 'nullable|string',
        ]);

        $booking->update($request->only([
            'start_date',
            'end_date',
            'quantity',
            'total_price',
            'status',
            'cancel_reason',
        ]));

        return response()->json(['message' => 'Cập nhật thành công', 'booking' => $booking]);
    }

    // Xóa mềm / khôi phục booking
    public function softDelete($id)
    {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['message' => 'Không tìm thấy booking'], 404);

        $booking->is_deleted = $booking->is_deleted === 'active' ? 'inactive' : 'active';
        $booking->save();

        return response()->json(['message' => 'Đã chuyển trạng thái đơn đặt thành công', 'booking' => $booking]);
    }

    // Xóa vĩnh viễn
    public function destroy($id)
    {
        $booking = Booking::find($id);
        if (!$booking) return response()->json(['message' => 'Không tìm thấy booking'], 404);

        $booking->delete();
        return response()->json(['message' => 'Xóa đơn đặt vĩnh viễn thành công']);
    }
}
