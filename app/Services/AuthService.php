<?php

namespace App\Services;

use App\DTOs\ServiceResult;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Register a new citizen/mobile user.
     */
    public function registerUser(array $validatedData): ServiceResult
    {
        try {
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'phone_number' => $validatedData['phone_number'] ?? null,
                'is_active' => true,
            ]);

            $user->assignRole('user');

            $token = $user->createToken('mobile_access_token')->plainTextToken;

            return ServiceResult::success([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'role' => 'user',
                ],
                'token' => $token,
            ], 'Registrasi akun berhasil', 201);
        } catch (\Throwable $e) {
            return ServiceResult::error('Gagal melakukan registrasi: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Authenticate mobile user and issue Sanctum token.
     */
    public function loginApi(string $email, string $password): ServiceResult
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return ServiceResult::error('Username atau kata sandi tidak cocok.', 401, ['credentials' => ['Username atau kata sandi salah']]);
        }

        if (! $user->is_active) {
            return ServiceResult::error('Akses login ditolak. Akun Anda telah dinonaktifkan.', 403, ['account' => ['Akun tidak aktif']]);
        }

        // Mobile Flutter is strictly for 'user' role only
        if (! $user->hasRole('user')) {
            return ServiceResult::error('Aplikasi mobile khusus untuk akun Warga (User). Administrator silakan login melalui portal Web Admin.', 403, ['role' => ['Hanya akun warga yang dapat login di mobile']]);
        }

        $token = $user->createToken('mobile_access_token')->plainTextToken;

        return ServiceResult::success([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'roles' => $user->getRoleNames(),
            ],
            'token' => $token,
        ], 'Login berhasil');
    }

    /**
     * Authenticate web credentials for web session login (allows admin and user).
     */
    public function loginWeb(string $email, string $password): ServiceResult
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return ServiceResult::error('Username atau kata sandi tidak cocok.', 401, ['credentials' => ['Username atau kata sandi salah']]);
        }

        if (! $user->is_active) {
            return ServiceResult::error('Akun Anda dinonaktifkan. Hubungi superadmin.', 403, ['account' => ['Akun tidak aktif']]);
        }

        // Both 'admin' and 'user' can login to web portal
        if (! $user->hasRole('admin') && ! $user->hasRole('user')) {
            return ServiceResult::error('Akun Anda tidak memiliki izin untuk masuk ke portal web.', 403, ['role' => ['Akses ditolak']]);
        }

        return ServiceResult::success($user, 'Otentikasi berhasil');
    }
}
