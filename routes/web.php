<?php

use App\Http\Controllers\Frontend\BookController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\CourseController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\PurchaseAuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');

Route::get('/books', [BookController::class, 'index'])->name('books.index');
Route::get('/books/{slug}', [BookController::class, 'show'])->name('books.show');

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
Route::get('/newsletter/unsubscribe/{email}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

/*
|--------------------------------------------------------------------------
| Cart (guest + authenticated, session or database backed)
|--------------------------------------------------------------------------
*/
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/', [CartController::class, 'store'])->name('store');
    Route::delete('/{cartItemId}', [CartController::class, 'destroy'])->name('destroy');
});

/*
|--------------------------------------------------------------------------
| Guest Checkout Identity Gate
|--------------------------------------------------------------------------
| A guest who clicks "Add to Cart" lands here first. 'guest' middleware
| keeps an already-logged-in user from looping back through this flow.
*/
Route::middleware('guest')->prefix('purchase')->name('purchase.')->group(function () {
    Route::get('/identify', [PurchaseAuthController::class, 'showIdentify'])->name('identify');
    Route::post('/identify', [PurchaseAuthController::class, 'checkPhone'])->name('identify.check');

    Route::get('/password', [PurchaseAuthController::class, 'showPassword'])->name('password');
    Route::post('/password', [PurchaseAuthController::class, 'authenticate'])->name('password.check');

    Route::get('/register', [PurchaseAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [PurchaseAuthController::class, 'register'])->name('register.store');
});

/*
|--------------------------------------------------------------------------
| Checkout & Payments (requires authentication)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/coupon', [CheckoutController::class, 'applyCoupon'])->name('coupon');
        Route::delete('/coupon', [CheckoutController::class, 'removeCoupon'])->name('coupon.remove');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
    });

    Route::prefix('payment/sslcommerz')->name('payment.sslcommerz.')->group(function () {
        Route::get('/init/{order:order_number}', [PaymentController::class, 'init'])->name('init');
        Route::post('/success', [PaymentController::class, 'success'])->name('success');
        Route::post('/fail', [PaymentController::class, 'fail'])->name('fail');
        Route::post('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
        Route::post('/ipn', [PaymentController::class, 'ipn'])->name('ipn');
    });

    /*
    |----------------------------------------------------------------
    | Shared profile / account settings (any authenticated role)
    |----------------------------------------------------------------
    */
    Route::prefix('account')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });
});
