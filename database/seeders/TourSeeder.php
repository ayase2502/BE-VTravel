<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tour;

class TourSeeder extends Seeder
{
    public function run()
    {
        $tours = [
            [
                'tour_name' => 'Tour Hạ Long 2N1Đ',
                'description' => 'Tour khám phá vịnh Hạ Long 2 ngày 1 đêm',
                'price' => 2500000,
                'duration' => 2,
                'max_people' => 20,
                'destination_id' => 1,
                'is_deleted' => 'active'
            ],
            [
                'tour_name' => 'Tour Sapa 3N2Đ',
                'description' => 'Tour trekking Sapa 3 ngày 2 đêm',
                'price' => 3500000,
                'duration' => 3,
                'max_people' => 15,
                'destination_id' => 2,
                'is_deleted' => 'active'
            ],
            [
                'tour_name' => 'Tour Hội An 2N1Đ',
                'description' => 'Tour khám phá phố cổ Hội An',
                'price' => 2000000,
                'duration' => 2,
                'max_people' => 25,
                'destination_id' => 3,
                'is_deleted' => 'active'
            ],
            [
                'tour_name' => 'Tour Phú Quốc 4N3Đ',
                'description' => 'Tour nghỉ dưỡng Phú Quốc',
                'price' => 5000000,
                'duration' => 4,
                'max_people' => 30,
                'destination_id' => 4,
                'is_deleted' => 'active'
            ],
            [
                'tour_name' => 'Tour Đà Lạt 3N2Đ',
                'description' => 'Tour khám phá thành phố ngàn hoa',
                'price' => 3000000,
                'duration' => 3,
                'max_people' => 20,
                'destination_id' => 5,
                'is_deleted' => 'active'
            ]
        ];

        foreach ($tours as $tour) {
            Tour::create($tour);
        }
    }
}