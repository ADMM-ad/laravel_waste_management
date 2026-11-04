<?php

namespace App\Services;

use App\DTOs\ServiceResult;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Retrieve users with roles.
     */
    public function getUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with(['roles'])->latest('created_at');

        if (! empty($filters['search'])) {
            $search = '%' . trim($filters['search']) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('email', 'like', $search)
                  ->orWhere('phone_number', 'like', $search);
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== 'all') {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        return $query->paginate($perPage);
    }

    /**
     * Toggle user active state.
     */
    public function toggleStatus(User $user, User $currentUser): ServiceResult
    {
        if ($user->id === $currentUser->id) {
            return ServiceResult::error('Anda tidak dapat menonaktifkan akun Anda sendiri.', 422);
        }

        if ($user->hasRole('admin')) {
            return ServiceResult::error('Anda tidak dapat mengubah status akun Administrator.', 403);
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return ServiceResult::success($user, "Pengguna {$user->name} berhasil {$statusStr}.");
    }

    /**
     * Create a new user with assigned role.
     */
    public function createUser(array $data, User $currentUser): ServiceResult
    {
        if (! $currentUser->hasRole('admin')) {
            return ServiceResult::error('Hanya Administrator yang memiliki akses untuk mendaftarkan pengguna baru.', 403);
        }

        try {
            $user = User::create([
                'name' => trim($data['name']),
                'email' => trim($data['email']),
                'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
                'phone_number' => ! empty($data['phone_number']) ? trim($data['phone_number']) : null,
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
            ]);

            $role = in_array($data['role'], ['admin', 'user']) ? $data['role'] : 'user';
            $user->assignRole($role);

            return ServiceResult::success($user, "Pengguna {$user->name} berhasil ditambahkan dengan peran {$role}.", 201);
        } catch (\Throwable $e) {
            return ServiceResult::error('Gagal menambahkan pengguna: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Soft delete user.
     */
    public function deleteUser(User $user, User $currentUser): ServiceResult
    {
        if ($user->id === $currentUser->id) {
            return ServiceResult::error('Anda tidak dapat menghapus akun Anda sendiri.', 422);
        }

        if ($user->hasRole('admin')) {
            return ServiceResult::error('Anda tidak dapat menghapus akun Administrator.', 403);
        }

        $user->delete();
        return ServiceResult::success(null, "Pengguna {$user->name} berhasil dihapus.");
    }
}
