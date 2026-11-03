<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Citizen register endpoint.
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone_number' => 'nullable|string|max:20',
        ], [
            'email.required' => 'Username wajib diisi.',
            'email.unique' => 'Username ini sudah digunakan, silakan pilih yang lain.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validasi registrasi gagal.', null, 422, $validator->errors()->toArray());
        }

        $result = $this->authService->registerUser($validator->validated());
        return $this->serviceResponse($result);
    }

    /**
     * Citizen login endpoint.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validasi login gagal.', null, 422, $validator->errors()->toArray());
        }

        $result = $this->authService->loginApi($request->email, $request->password);
        return $this->serviceResponse($result);
    }

    /**
     * Revoke current Sanctum token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->jsonResponse(true, 'Logout berhasil, token telah dicabut.', null, 200);
    }

    /**
     * Get authenticated user profile.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        return $this->jsonResponse(true, 'Profil pengguna berhasil diambil.', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'is_active' => $user->is_active,
            'roles' => $user->getRoleNames(),
        ]);
    }

    /**
     * Update authenticated citizen profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validasi update profil gagal.', null, 422, $validator->errors()->toArray());
        }

        $user->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ]);

        return $this->jsonResponse(true, 'Profil berhasil diperbarui.', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'is_active' => $user->is_active,
            'roles' => $user->getRoleNames(),
        ]);
    }

    /**
     * Change citizen password.
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse(false, 'Validasi kata sandi gagal.', null, 422, $validator->errors()->toArray());
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->jsonResponse(false, 'Kata sandi saat ini tidak sesuai.', null, 422, [
                'current_password' => ['Kata sandi saat ini tidak cocok dengan data kami.']
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return $this->jsonResponse(true, 'Kata sandi berhasil diperbarui.', null, 200);
    }
}
