<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'], // điều kiện tồn tại
            [
                'full_name' => 'Administrator',
                'email' => 'admin@gmail.com',
                'phone' => '0900000000',
                'password' => Hash::make('321321'), // đặt lại mật khẩu
                'role' => 'admin',
                'is_verified' => true,
                'is_deleted' => 'active',
            ]
        );
    }
}
