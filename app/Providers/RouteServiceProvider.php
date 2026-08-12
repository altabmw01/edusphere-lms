<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        //
    }

    /**
     * Resolve the correct post-login dashboard route for a given user's role.
     */
    public static function homeFor(User $user): string
    {
        return match ($user->role) {
            User::ROLE_ADMIN => route('admin.dashboard'),
            User::ROLE_TEACHER => route('teacher.dashboard'),
            User::ROLE_MANAGER => route('manager.dashboard'),
            default => route('student.dashboard'),
        };
    }
}
