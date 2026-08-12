<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\BatchController;
use App\Http\Controllers\Admin\BatchLevelController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CurriculumController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
| Mounted under /admin with 'auth' + 'role:admin' middleware.
| The Admin role has unrestricted access to every module.
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('users', UserController::class);
Route::resource('courses', CourseController::class)->except(['show']);

Route::prefix('courses/{course}/curriculum')->name('courses.curriculum.')->group(function () {
    Route::get('/', [CurriculumController::class, 'edit'])->name('edit');
    Route::post('/sections', [CurriculumController::class, 'storeSection'])->name('sections.store');
    Route::delete('/sections/{section}', [CurriculumController::class, 'destroySection'])->name('sections.destroy');
    Route::post('/sections/{section}/lessons', [CurriculumController::class, 'storeLesson'])->name('lessons.store');
    Route::delete('/lessons/{lesson}', [CurriculumController::class, 'destroyLesson'])->name('lessons.destroy');
});
Route::resource('books', BookController::class)->except(['show']);

Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

Route::resource('coupons', CouponController::class)->except(['show']);

Route::get('batch-levels', [BatchLevelController::class, 'index'])->name('batch-levels.index');
Route::post('batch-levels', [BatchLevelController::class, 'store'])->name('batch-levels.store');
Route::put('batch-levels/{batchLevel}', [BatchLevelController::class, 'update'])->name('batch-levels.update');
Route::delete('batch-levels/{batchLevel}', [BatchLevelController::class, 'destroy'])->name('batch-levels.destroy');

Route::resource('batches', BatchController::class)->except(['show']);
Route::get('batches/{batch}/assign-students', [BatchController::class, 'assignStudents'])->name('batches.assign-students');
Route::post('batches/{batch}/assign-students', [BatchController::class, 'storeAssignStudents'])->name('batches.assign-students.store');

Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
Route::get('orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');

Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::put('reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
Route::put('reviews/{review}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
Route::post('reviews/{review}/reply', [ReviewController::class, 'reply'])->name('reviews.reply');

Route::get('faqs', [FaqController::class, 'index'])->name('faqs.index');
Route::post('faqs', [FaqController::class, 'store'])->name('faqs.store');
Route::put('faqs/{faq}', [FaqController::class, 'update'])->name('faqs.update');
Route::delete('faqs/{faq}', [FaqController::class, 'destroy'])->name('faqs.destroy');

Route::get('testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
Route::post('testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');
Route::delete('testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');

Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
Route::get('messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('messages.show');
Route::delete('messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
Route::get('reports/orders/export/excel', [ReportController::class, 'exportOrdersExcel'])->name('reports.orders.excel');
Route::get('reports/orders/export/pdf', [ReportController::class, 'exportOrdersPdf'])->name('reports.orders.pdf');

Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');

Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
