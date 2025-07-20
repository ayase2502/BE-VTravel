<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'full_name' => 'Admin VTravel',
                'email' => 'admin@vtravel.com',
                'phone' => '0123456789',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_verified' => true,
                'is_deleted' => 'active'
            ],
            [
                'full_name' => 'Nhân viên 1',
                'email' => 'staff@vtravel.com',
                'phone' => '0987654321',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'is_verified' => true,
                'is_deleted' => 'active'
            ],
            [
                'full_name' => 'Nguyễn Văn A',
                'email' => 'customer1@gmail.com',
                'phone' => '0111111111',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_verified' => true,
                'is_deleted' => 'active'
            ],
            [
                'full_name' => 'Trần Thị B',
                'email' => 'customer2@gmail.com',
                'phone' => '0222222222',
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_verified' => true,
                'is_deleted' => 'active'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
