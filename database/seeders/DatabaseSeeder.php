<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DestinationCategorySeeder::class,
            DestinationSeeder::class,
            TourSeeder::class,
            GuideSeeder::class,
            HotelSeeder::class,
            BusRouteSeeder::class,
            MotorbikeSeeder::class,
            PaymentMethodSeeder::class,
            CustomTourSeeder::class,
            BookingSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
