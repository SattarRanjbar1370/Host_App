<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ساخت اکانت مدیر ارشد
        User::updateOrCreate(
            ['phone' => '09120000000'],
            [
                'name' => 'مدیر ارشد سیستم',
                'password' => Hash::make('admin123456'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );
    }
}