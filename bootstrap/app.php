<?php

use App\Http\Middleware\EnsureUserHasRole;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\TrackLastLogin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Illuminate\Support\Facades\Route::middleware('web')
                ->group(__DIR__ . '/../routes/auth.php');

            Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'role:admin'])
                ->prefix('admin')
                ->name('admin.')
                ->group(__DIR__ . '/../routes/admin.php');

            Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'role:teacher'])
                ->prefix('teacher')
                ->name('teacher.')
                ->group(__DIR__ . '/../routes/teacher.php');

            Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'role:manager'])
                ->prefix('manager')
                ->name('manager.')
                ->group(__DIR__ . '/../routes/manager.php');

            Illuminate\Support\Facades\Route::middleware(['web', 'auth', 'role:student'])
                ->prefix('dashboard')
                ->name('student.')
                ->group(__DIR__ . '/../routes/student.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware) {

        /*
        |--------------------------------------------------------------------------
        | CSRF Exceptions
        |--------------------------------------------------------------------------
        | SSLCommerz sends POST requests directly to these URLs.
        | They do not contain Laravel's CSRF token.
        */
        $middleware->validateCsrfTokens(except: [
            'payment/sslcommerz/success',
            'payment/sslcommerz/fail',
            'payment/sslcommerz/cancel',
            'payment/sslcommerz/ipn',
        ]);

        $middleware->web(append: [
            TrackLastLogin::class,
        ]);

        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'guest' => RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
