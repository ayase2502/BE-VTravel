<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Destination;

class DestinationSeeder extends Seeder
{
    public function run()
    {
        $destinations = [
            [
                'destination_name' => 'Hạ Long',
                'location' => 'Quảng Ninh',
                'description' => 'Vịnh Hạ Long nổi tiếng thế giới với hàng nghìn hòn đảo đá vôi',
                'category_id' => 1,
                'is_deleted' => 'active'
            ],
            [
                'destination_name' => 'Sapa',
                'location' => 'Lào Cai',
                'description' => 'Thị trấn miền núi với ruộng bậc thang tuyệt đẹp',
                'category_id' => 2,
                'is_deleted' => 'active'
            ],
            [
                'destination_name' => 'Hội An',
                'location' => 'Quảng Nam',
                'description' => 'Phố cổ Hội An với kiến trúc cổ kính',
                'category_id' => 4,
                'is_deleted' => 'active'
            ],
            [
                'destination_name' => 'Phú Quốc',
                'location' => 'Kiên Giang',
                'description' => 'Đảo ngọc Phú Quốc với bãi biển đẹp nhất Việt Nam',
                'category_id' => 1,
                'is_deleted' => 'active'
            ],
            [
                'destination_name' => 'Đà Lạt',
                'location' => 'Lâm Đồng',
                'description' => 'Thành phố ngàn hoa với khí hậu mát mẻ',
                'category_id' => 3,
                'is_deleted' => 'active'
            ],
            [
                'destination_name' => 'Nha Trang',
                'location' => 'Khánh Hòa',
                'description' => 'Thành phố biển với nhiều hoạt động thể thao nước',
                'category_id' => 1,
                'is_deleted' => 'active'
            ],
            [
                'destination_name' => 'Huế',
                'location' => 'Thừa Thiên Huế',
                'description' => 'Cố đô Huế với nhiều di tích lịch sử',
                'category_id' => 4,
                'is_deleted' => 'active'
            ],
            [
                'destination_name' => 'Mù Cang Chải',
                'location' => 'Yên Bái',
                'description' => 'Ruộng bậc thang đẹp nhất Việt Nam',
                'category_id' => 2,
                'is_deleted' => 'active'
            ],
            [
                'destination_name' => 'Ninh Bình',
                'location' => 'Ninh Bình',
                'description' => 'Tràng An - Di sản thiên nhiên thế giới',
                'category_id' => 5,
                'is_deleted' => 'active'
            ],
            [
                'destination_name' => 'Cần Thơ',
                'location' => 'Cần Thơ',
                'description' => 'Thủ phủ miền Tây với chợ nổi Cái Răng',
                'category_id' => 9,
                'is_deleted' => 'active'
            ]
        ];

        foreach ($destinations as $destination) {
            try {
                Destination::create($destination);
                echo "Created: " . $destination['destination_name'] . "\n";
            } catch (\Exception $e) {
                echo "Error creating " . $destination['destination_name'] . ": " . $e->getMessage() . "\n";
            }
        }
    }
}