<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DestinationCategory;

class DestinationCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category_name' => 'Biển',
                'thumbnail' => 'categories/bien.jpg',
            ],
            [
                'category_name' => 'Núi',
                'thumbnail' => 'categories/nui.jpg',
            ],
            [
                'category_name' => 'Thành phố',
                'thumbnail' => 'categories/thanh-pho.jpg',
            ],
            [
                'category_name' => 'Văn hóa',
                'thumbnail' => 'categories/van-hoa.jpg',
            ],
            [
                'category_name' => 'Thiên nhiên',
                'thumbnail' => 'categories/thien-nhien.jpg',
            ],
            [
                'category_name' => 'Thể thao',
                'thumbnail' => 'categories/the-thao.jpg',
            ],
            [
                'category_name' => 'Khám phá',
                'thumbnail' => 'categories/kham-pha.jpg',
            ],
            [
                'category_name' => 'Nghỉ dưỡng',
                'thumbnail' => 'categories/nghi-duong.jpg',
            ],
            [
                'category_name' => 'Ẩm thực',
                'thumbnail' => 'categories/am-thuc.jpg',
            ],
            [
                'category_name' => 'Lịch sử',
                'thumbnail' => 'categories/lich-su.jpg',
            ]
        ];

        foreach ($categories as $category) {
            DestinationCategory::create($category);
        }
    }
}