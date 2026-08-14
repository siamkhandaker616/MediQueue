<?php

use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ReviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('role:admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');
    Route::patch('/reviews/{review}/toggle', [ReviewController::class, 'toggle'])->name('reviews.toggle');
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics');
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
    Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
    Route::get('/doctors/{doctor}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
    Route::patch('/doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
    Route::patch('/doctors/{doctor}/toggle', [DoctorController::class, 'toggleActive'])->name('doctors.toggle');
});
