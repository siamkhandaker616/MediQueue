<?php

use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\PrescriptionController;
use App\Http\Controllers\Doctor\QueueController;
use App\Http\Controllers\Doctor\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::middleware('role:doctor')->group(function () {
    Route::get('/dashboard', [QueueController::class, 'index'])->name('dashboard');
    Route::get('/queue', [QueueController::class, 'index'])->name('queue');
    Route::patch('/queue/{appointment}/status', [QueueController::class, 'updateStatus'])->name('queue.status');
    Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
    Route::post('/leave', [ScheduleController::class, 'storeLeave'])->name('leave.store');
    Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/prescriptions/create/{appointment}', [PrescriptionController::class, 'create'])->name('prescriptions.create');
    Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::get('/prescriptions/{prescription}/edit', [PrescriptionController::class, 'edit'])->name('prescriptions.edit');
    Route::patch('/prescriptions/{prescription}', [PrescriptionController::class, 'update'])->name('prescriptions.update');
    Route::get('/prescriptions/{prescription}/pdf', [PrescriptionController::class, 'pdf'])->name('prescriptions.pdf');
    Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show'])->name('prescriptions.show');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
});
