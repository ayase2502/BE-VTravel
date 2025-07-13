<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run()
    {
        $bookings = [
            [
                'user_id' => 3,
                'tour_id' => 1,
                'guide_id' => 1,
                'hotel_id' => 1,
                'quantity' => 2,
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(9),
                'total_price' => 5000000,
                'payment_method' => 'VNPay',
                'status' => 'confirmed',
                'is_deleted' => 'active'
            ],
            [
                'user_id' => 4,
                'tour_id' => 2,
                'guide_id' => 2,
                'hotel_id' => 2,
                'quantity' => 1,
                'start_date' => Carbon::now()->addDays(14),
                'end_date' => Carbon::now()->addDays(17),
                'total_price' => 3500000,
                'payment_method' => 'MoMo',
                'status' => 'pending',
                'is_deleted' => 'active'
            ],
            [
                'user_id' => 3,
                'custom_tour_id' => 1,
                'quantity' => 3,
                'start_date' => Carbon::now()->addDays(21),
                'end_date' => Carbon::now()->addDays(25),
                'total_price' => 8000000,
                'payment_method' => 'bank_transfer',
                'status' => 'confirmed',
                'is_deleted' => 'active'
            ]
        ];

        foreach ($bookings as $booking) {
            Booking::create($booking);
        }
    }
}