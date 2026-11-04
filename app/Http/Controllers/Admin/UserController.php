<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'is_active']);
        $users = $this->userService->getUsers($filters, 15);

        return view('admin.users.index', compact('users', 'filters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'phone_number' => 'nullable|string|max:20',
            'role' => 'required|in:admin,user',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Username atau email wajib diisi.',
            'email.unique' => 'Username atau email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'role.required' => 'Peran (role) pengguna wajib dipilih.',
            'role.in' => 'Peran pengguna harus berupa admin atau user.',
        ]);

        $result = $this->userService->createUser($validated, $request->user());

        if (! $result->isSuccess()) {
            return back()->withInput()->with('error', $result->message);
        }

        return redirect()->route('admin.users.index')->with('success', $result->message);
    }

    public function toggleStatus(int $id, Request $request): RedirectResponse
    {
        $user = User::findOrFail($id);
        $result = $this->userService->toggleStatus($user, $request->user());

        if (! $result->isSuccess()) {
            return back()->with('error', $result->message);
        }

        return back()->with('success', $result->message);
    }

    public function destroy(int $id, Request $request): RedirectResponse
    {
        $user = User::findOrFail($id);
        $result = $this->userService->deleteUser($user, $request->user());

        if (! $result->isSuccess()) {
            return back()->with('error', $result->message);
        }

        return back()->with('success', $result->message);
    }
}
