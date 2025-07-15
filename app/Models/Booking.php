<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $primaryKey = 'booking_id';
    protected $table = 'bookings';

    protected $fillable = [
        'user_id',
        'tour_id',
        'guide_id',
        'hotel_id',
        'bus_route_id',
        'motorbike_id',
        'custom_tour_id',
        'quantity',
        'start_date',
        'end_date',
        'total_price',
        'payment_method',
        'status',
        'cancel_reason',
        'is_deleted'
    ];

    // Quan hệ: Người đặt
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Quan hệ: Tour được đặt
    public function tour()
    {
        return $this->belongsTo(Tour::class, 'tour_id');
    }

    // Quan hệ: Hướng dẫn viên
    public function guide()
    {
        return $this->belongsTo(Guide::class, 'guide_id');
    }

    // Quan hệ: Khách sạn
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    // Quan hệ: Tuyến xe buýt
    public function busRoute()
    {
        return $this->belongsTo(BusRoute::class, 'bus_route_id');
    }

    // Quan hệ: Xe máy
    public function motorbike()
    {
        return $this->belongsTo(Motorbike::class, 'motorbike_id');
    }

    // Quan hệ: Custom tour
    public function customTour()
    {
        return $this->belongsTo(CustomTour::class, 'custom_tour_id');
    }
}
