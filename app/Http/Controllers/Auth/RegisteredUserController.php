<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\ActivityLog;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\CartService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request, CartService $cartService): RedirectResponse
    {
        $user = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'password' => Hash::make($request->validated('password')),
            'role' => User::ROLE_STUDENT, // Self-registration is always a student account.
        ]);

        event(new Registered($user));

        ActivityLog::record('user_registered', "{$user->name} registered a new account.", $user);

        Auth::login($user);
        $cartService->mergeGuestCartOnLogin($user->id);

        return redirect(RouteServiceProvider::homeFor($user));
    }
}
