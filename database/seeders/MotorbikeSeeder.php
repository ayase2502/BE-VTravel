<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Motorbike;

class MotorbikeSeeder extends Seeder
{
    public function run()
    {
        $motorbikes = [
            [
                'bike_name' => 'Honda Winner X 150',
                'bike_type' => 'Xe số',
                'license_plate' => '30A1-12345',
                'daily_rate' => 200000,
                'location' => 'Hà Nội',
                'is_available' => true,
                'is_deleted' => 'active'
            ],
            [
                'bike_name' => 'Yamaha Exciter 155',
                'bike_type' => 'Xe số',
                'license_plate' => '30A1-67890',
                'daily_rate' => 220000,
                'location' => 'Hà Nội',
                'is_available' => true,
                'is_deleted' => 'active'
            ],
            [
                'bike_name' => 'Honda Vision 110',
                'bike_type' => 'Xe ga',
                'license_plate' => '43A1-11111',
                'daily_rate' => 150000,
                'location' => 'Đà Nẵng',
                'is_available' => true,
                'is_deleted' => 'active'
            ],
            [
                'bike_name' => 'Yamaha Janus 125',
                'bike_type' => 'Xe ga',
                'license_plate' => '43A1-22222',
                'daily_rate' => 160000,
                'location' => 'Đà Nẵng',
                'is_available' => true,
                'is_deleted' => 'active'
            ],
            [
                'bike_name' => 'Honda Air Blade 125',
                'bike_type' => 'Xe ga',
                'license_plate' => '51A1-33333',
                'daily_rate' => 180000,
                'location' => 'TP.HCM',
                'is_available' => true,
                'is_deleted' => 'active'
            ]
        ];

        foreach ($motorbikes as $motorbike) {
            Motorbike::create($motorbike);
        }
    }
}