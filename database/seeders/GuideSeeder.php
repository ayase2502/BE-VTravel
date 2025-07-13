<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Guide;

class GuideSeeder extends Seeder
{
    public function run()
    {
        $guides = [
            [
                'guide_name' => 'Nguyễn Văn Hùng',
                'phone' => '0901234567',
                'email' => 'hung.guide@vtravel.com',
                'experience_years' => 5,
                'specialization' => 'Tour biển',
                'daily_rate' => 500000,
                'is_deleted' => 'active'
            ],
            [
                'guide_name' => 'Trần Thị Lan',
                'phone' => '0902345678',
                'email' => 'lan.guide@vtravel.com',
                'experience_years' => 8,
                'specialization' => 'Tour núi',
                'daily_rate' => 600000,
                'is_deleted' => 'active'
            ],
            [
                'guide_name' => 'Lê Minh Tuấn',
                'phone' => '0903456789',
                'email' => 'tuan.guide@vtravel.com',
                'experience_years' => 3,
                'specialization' => 'Tour văn hóa',
                'daily_rate' => 450000,
                'is_deleted' => 'active'
            ],
            [
                'guide_name' => 'Phạm Thị Hoa',
                'phone' => '0904567890',
                'email' => 'hoa.guide@vtravel.com',
                'experience_years' => 6,
                'specialization' => 'Tour thiên nhiên',
                'daily_rate' => 550000,
                'is_deleted' => 'active'
            ]
        ];

        foreach ($guides as $guide) {
            Guide::create($guide);
        }
    }
}