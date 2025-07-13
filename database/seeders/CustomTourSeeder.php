<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CustomTour;

class CustomTourSeeder extends Seeder
{
    public function run()
    {
        $customTours = [
            [
                'user_id' => 3,
                'total_price' => 8000000,
                'status' => 'confirmed',
                'is_deleted' => 'active'
            ],
            [
                'user_id' => 4,
                'total_price' => 6500000,
                'status' => 'draft',
                'is_deleted' => 'active'
            ],
            [
                'user_id' => 3,
                'total_price' => 12000000,
                'status' => 'completed',
                'is_deleted' => 'active'
            ]
        ];

        foreach ($customTours as $tour) {
            CustomTour::create($tour);
        }
    }
}