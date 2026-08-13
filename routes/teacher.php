<?php

use App\Http\Controllers\Teacher\BatchClassController;
use App\Http\Controllers\Teacher\BatchController;
use App\Http\Controllers\Teacher\DashboardController;
use App\Http\Controllers\Teacher\ProfileController;
use App\Http\Controllers\Teacher\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Teacher Panel Routes
|--------------------------------------------------------------------------
| Mounted under /teacher with 'auth' + 'role:teacher' middleware
| (see bootstrap/app.php).
|
| Teachers have NO relationship to courses/books directly — courses are
| entirely admin-managed (including curriculum). A teacher's only connection
| to course/book content is through the Batches assigned to them: they
| manage class links for their batches, and see students through those
| batches only.
*/

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/batches', [BatchController::class, 'index'])->name('batches.index');
Route::get('/batches/{batch}', [BatchController::class, 'show'])->name('batches.show');
Route::post('/batches/{batch}/classes', [BatchClassController::class, 'store'])->name('batches.classes.store');
Route::put('/classes/{class}', [BatchClassController::class, 'update'])->name('classes.update');
Route::delete('/classes/{class}', [BatchClassController::class, 'destroy'])->name('classes.destroy');

Route::get('/students', [StudentController::class, 'index'])->name('students.index');

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
