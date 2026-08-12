<?php

use App\Http\Controllers\Manager\BookController;
use App\Http\Controllers\Manager\CategoryController;
use App\Http\Controllers\Manager\CouponController;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\OrderController;
use App\Http\Controllers\Manager\ReviewController;
use App\Http\Controllers\Manager\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Manager Panel Routes
|--------------------------------------------------------------------------
| Mounted under /manager with 'auth' + 'role:manager' middleware.
| Managers may operate: Categories, Orders, Coupons, Books, Reviews, Users
| (view/moderate only — never Admin/Manager account management).
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

Route::resource('coupons', CouponController::class)->except(['show']);
Route::resource('books', BookController::class)->except(['show']);

Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
Route::get('orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');

Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::put('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
Route::put('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
Route::post('reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');

Route::get('users', [UserController::class, 'index'])->name('users.index');
Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
Route::put('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
