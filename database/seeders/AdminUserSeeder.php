<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin Account (Username: admin, Password: 12345678)
        $admin = User::updateOrCreate(
            ['email' => 'admin'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('12345678'),
                'phone_number' => '081234567890',
                'is_active' => true,
            ]
        );
        $admin->syncRoles(['admin']);

        // 2. Regular User Account (Username: user, Password: 12345678)
        $user = User::updateOrCreate(
            ['email' => 'user'],
            [
                'name' => 'Pengguna Warga',
                'password' => Hash::make('12345678'),
                'phone_number' => '089876543210',
                'is_active' => true,
            ]
        );
        $user->syncRoles(['user']);
    }
}
