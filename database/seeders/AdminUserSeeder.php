<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Account
        $admin = User::firstOrCreate(
            ['email' => 'admin@banksampah.id'],
            [
                'name' => 'Administrator Bank Sampah',
                'password' => Hash::make('password123'),
                'phone_number' => '081234567890',
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        // 2. Sample Citizen / Mobile User Account
        $citizen = User::firstOrCreate(
            ['email' => 'warga@banksampah.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password123'),
                'phone_number' => '089876543210',
                'is_active' => true,
            ]
        );
        $citizen->syncRoles(['user']);
    }
}
