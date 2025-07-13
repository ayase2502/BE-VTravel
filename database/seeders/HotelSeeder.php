<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;

class HotelSeeder extends Seeder
{
    public function run()
    {
        $hotels = [
            [
                'hotel_name' => 'Khách sạn Hạ Long Bay',
                'location' => 'Hạ Long, Quảng Ninh',
                'description' => 'Khách sạn 4 sao view vịnh Hạ Long',
                'price_per_night' => 1500000,
                'total_rooms' => 100,
                'available_rooms' => 80,
                'is_deleted' => 'active'
            ],
            [
                'hotel_name' => 'Sapa Mountain Resort',
                'location' => 'Sapa, Lào Cai',
                'description' => 'Resort nghỉ dưỡng giữa núi rừng Sapa',
                'price_per_night' => 2000000,
                'total_rooms' => 60,
                'available_rooms' => 45,
                'is_deleted' => 'active'
            ],
            [
                'hotel_name' => 'Hội An Ancient Hotel',
                'location' => 'Hội An, Quảng Nam',
                'description' => 'Khách sạn phong cách cổ điển Hội An',
                'price_per_night' => 1200000,
                'total_rooms' => 80,
                'available_rooms' => 65,
                'is_deleted' => 'active'
            ],
            [
                'hotel_name' => 'Phú Quốc Beach Resort',
                'location' => 'Phú Quốc, Kiên Giang',
                'description' => 'Resort 5 sao bên bãi biển Phú Quốc',
                'price_per_night' => 3000000,
                'total_rooms' => 150,
                'available_rooms' => 120,
                'is_deleted' => 'active'
            ]
        ];

        foreach ($hotels as $hotel) {
            Hotel::create($hotel);
        }
    }
}