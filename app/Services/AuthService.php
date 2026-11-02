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
            return ServiceResult::error('Kredensial email atau password tidak sesuai.', 401, ['credentials' => ['Email atau password salah']]);
        }

        if (! $user->is_active) {
            return ServiceResult::error('Akses login ditolak. Akun Anda telah dinonaktifkan.', 403, ['account' => ['Akun tidak aktif']]);
        }

        if (! $user->hasRole('user') && ! $user->hasRole('admin')) {
            return ServiceResult::error('Akses tidak diizinkan untuk peran ini.', 403);
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
     * Authenticate web admin credentials for web session login.
     */
    public function loginWeb(string $email, string $password): ServiceResult
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            return ServiceResult::error('Email atau kata sandi tidak cocok.', 401, ['credentials' => ['Email atau kata sandi salah']]);
        }

        if (! $user->is_active) {
            return ServiceResult::error('Akun Anda dinonaktifkan. Hubungi superadmin.', 403, ['account' => ['Akun tidak aktif']]);
        }

        if (! $user->hasRole('admin')) {
            return ServiceResult::error('Hanya administrator yang diizinkan masuk ke portal admin.', 403, ['role' => ['Akses terbatas untuk admin']]);
        }

        return ServiceResult::success($user, 'Otentikasi admin berhasil');
    }
}
