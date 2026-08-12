<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

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

        return view('admin.users.index', ['users' => $users]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Admin creates Teacher, Manager, or Admin accounts.
     * Self-registration is restricted to students only (see RegisteredUserController).
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:admin,teacher,manager'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = now();

        $user = User::create($data);

        if ($user->role === User::ROLE_TEACHER) {
            $user->teacherProfile()->create([]);
        }

        ActivityLog::record('user_created_by_admin', "Admin created {$user->role} account: {$user->name}", $user);

        return redirect()->route('admin.users.index')->with('status', 'Account created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', ['editUser' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['boolean'],
        ]);

        $user->update($data);

        return back()->with('status', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');
        $user->delete();

        return back()->with('status', 'User deleted.');
    }
}
