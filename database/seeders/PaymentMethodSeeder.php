<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        $paymentMethods = [
            [
                'method_name' => 'Tiền mặt (COD)',
                'description' => 'Thanh toán khi nhận hàng',
                'is_active' => 'active',
                'is_deleted' => 'active'
            ],
            [
                'method_name' => 'Chuyển khoản ngân hàng',
                'description' => 'Chuyển khoản qua ngân hàng',
                'is_active' => 'active',
                'is_deleted' => 'active'
            ],
            [
                'method_name' => 'VNPay',
                'description' => 'Thanh toán qua VNPay',
                'is_active' => 'active',
                'is_deleted' => 'active'
            ],
            [
                'method_name' => 'MoMo',
                'description' => 'Thanh toán qua ví MoMo',
                'is_active' => 'active',
                'is_deleted' => 'active'
            ]
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }
    }
}