<?php

use App\Http\Controllers\Student\CertificateController;
use App\Http\Controllers\Student\ClassController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\MyBookController;
use App\Http\Controllers\Student\MyCourseController;
use App\Http\Controllers\Student\OrderController;
use App\Http\Controllers\Student\ReviewController;
use App\Http\Controllers\Student\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Student Panel Routes
|--------------------------------------------------------------------------
| Mounted under /dashboard with 'auth' + 'role:student' middleware.
| Covers: purchased courses/books, learning progress, wishlist,
| order history, certificates, and review submission.
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/my-courses', [MyCourseController::class, 'index'])->name('my-courses.index');
Route::get('/my-courses/{course:slug}', [MyCourseController::class, 'show'])->name('my-courses.show');
Route::get('/my-courses/{course:slug}/class', [ClassController::class, 'showCourseClass'])->name('my-courses.class');
Route::post('/lessons/{lesson}/complete', [MyCourseController::class, 'completeLesson'])->name('lessons.complete');

Route::get('/my-books', [MyBookController::class, 'index'])->name('my-books.index');
Route::get('/my-books/{book}/download', [MyBookController::class, 'download'])->name('my-books.download');
Route::get('/my-books/{book}/class', [ClassController::class, 'showBookClass'])->name('my-books.class');

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
Route::post('/wishlist/courses/{course}', [WishlistController::class, 'storeCourse'])->name('wishlist.courses.store');
Route::post('/wishlist/books/{book}', [WishlistController::class, 'storeBook'])->name('wishlist.books.store');
Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::put('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');

Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
Route::get('/certificates/{certificate}/download', [CertificateController::class, 'download'])->name('certificates.download');

Route::post('/reviews/courses/{course}', [ReviewController::class, 'storeCourse'])->name('reviews.courses.store');
Route::post('/reviews/books/{book}', [ReviewController::class, 'storeBook'])->name('reviews.books.store');
