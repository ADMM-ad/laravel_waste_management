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
