<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Managers may view all users and toggle activation status for
 * students and teachers, but cannot create or edit Admin/Manager
 * accounts. Account creation for staff roles remains Admin-only.
 */
class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('role'), fn ($q) => $q->role($request->string('role')))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%' . $request->string('search') . '%'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('manager.users.index', ['users' => $users]);
    }

    public function show(User $user): View
    {
        return view('manager.users.show', ['viewedUser' => $user->load(['orders', 'enrollments.course', 'bookPurchases.book'])]);
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        abort_if(in_array($user->role, [User::ROLE_ADMIN, User::ROLE_MANAGER]), 403, 'Managers cannot modify staff accounts.');

        $user->update(['is_active' => ! $user->is_active]);

        ActivityLog::record(
            $user->is_active ? 'user_activated' : 'user_deactivated',
            "Manager toggled status for {$user->name}",
            $user
        );

        return back()->with('status', 'User status updated.');
    }
}
