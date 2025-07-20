<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusRoute;

class BusRouteSeeder extends Seeder
{
    public function run()
    {
        $routes = [
            [
                'route_name' => 'Hà Nội - Hạ Long',
                'departure_location' => 'Hà Nội',
                'arrival_location' => 'Hạ Long',
                'departure_time' => '08:00:00',
                'arrival_time' => '11:00:00',
                'price' => 150000,
                'total_seats' => 45,
                'available_seats' => 35,
                'is_deleted' => 'active'
            ],
            [
                'route_name' => 'Hà Nội - Sapa',
                'departure_location' => 'Hà Nội',
                'arrival_location' => 'Sapa',
                'departure_time' => '22:00:00',
                'arrival_time' => '06:00:00',
                'price' => 250000,
                'total_seats' => 40,
                'available_seats' => 28,
                'is_deleted' => 'active'
            ],
            [
                'route_name' => 'Đà Nẵng - Hội An',
                'departure_location' => 'Đà Nẵng',
                'arrival_location' => 'Hội An',
                'departure_time' => '09:00:00',
                'arrival_time' => '10:00:00',
                'price' => 50000,
                'total_seats' => 30,
                'available_seats' => 25,
                'is_deleted' => 'active'
            ],
            [
                'route_name' => 'TP.HCM - Đà Lạt',
                'departure_location' => 'TP.HCM',
                'arrival_location' => 'Đà Lạt',
                'departure_time' => '23:00:00',
                'arrival_time' => '06:00:00',
                'price' => 200000,
                'total_seats' => 35,
                'available_seats' => 20,
                'is_deleted' => 'active'
            ]
        ];

        foreach ($routes as $route) {
            BusRoute::create($route);
        }
    }
}