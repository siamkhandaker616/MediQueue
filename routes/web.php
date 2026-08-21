<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/* -------------------------------------------------------------------------- */
/*                                Public Routes                               */
/* -------------------------------------------------------------------------- */

Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| FR-01 & FR-02: Department Catalogue & Doctor Directory
|--------------------------------------------------------------------------
*/
Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
Route::get('/departments/{department:slug}', [DepartmentController::class, 'show'])->name('departments.show');

Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
Route::get('/doctors/{doctor:slug}', [DoctorController::class, 'show'])->name('doctors.show');

/*
|--------------------------------------------------------------------------
| FR-03 & FR-04: Smart Appointment Wizard & Token Generation (patients only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:patient'])->group(function () {
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
});

// Real-time AJAX time slot availability route (called by Alpine.js wizard)
Route::get('/api/doctors/{doctor}/available-slots', [AppointmentController::class, 'getSlots'])->name('doctors.slots');

/* -------------------------------------------------------------------------- */
/*                             Authenticated Routes                           */
/* -------------------------------------------------------------------------- */

Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        'doctor' => redirect()->route('doctor.dashboard'),
        'admin'  => redirect()->route('admin.dashboard'),
        default  => redirect()->route('patient.dashboard'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/payments/{payment}/receipt', [PaymentReceiptController::class, 'show'])->name('payments.receipt');

    // Profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';