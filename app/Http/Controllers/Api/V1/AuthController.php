<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone_number' => 'nullable|string|max:20',
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
            'email' => 'required|email',
            'password' => 'required|string',
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
}
