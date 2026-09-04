<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Patient\MedicalProfileController;
use App\Http\Controllers\Patient\MedicalReportController;
use App\Http\Controllers\Patient\MedicationTrackerController;
use App\Http\Controllers\Patient\PrescriptionController;
use App\Http\Controllers\Patient\ReviewController;
use App\Http\Controllers\Patient\VisitHistoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SSLCommerzPaymentController;
use Illuminate\Support\Facades\Route;

/* -------------------------------------------------------------------------- */
/*                                Public Routes                               */
/* -------------------------------------------------------------------------- */

Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| FR-01 & FR-02: Departments & Doctors
|--------------------------------------------------------------------------
*/
Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
Route::get('/departments/{department:slug}', [DepartmentController::class, 'show'])->name('departments.show');
Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
Route::get('/doctors/{doctor:slug}', [DoctorController::class, 'show'])->name('doctors.show');

/*
|--------------------------------------------------------------------------
| FR-03, FR-04 & FR-06: Appointments & Rescheduling
|--------------------------------------------------------------------------
*/
Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');

// FR-06 Rescheduling & Cancellation
Route::get('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
Route::patch('/appointments/{appointment}/reschedule', [AppointmentController::class, 'updateSchedule'])->name('appointments.updateSchedule');
Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

// Real-time AJAX time slot availability route
Route::get('/api/doctors/{doctor}/available-slots', [AppointmentController::class, 'getSlots'])->name('doctors.slots');

/*
|--------------------------------------------------------------------------
| FR-07 & FR-08: Payments & SSLCommerz Gateway
|--------------------------------------------------------------------------
*/
Route::get('/appointments/{appointment}/payment', [PaymentController::class, 'checkout'])->name('payments.checkout');
Route::post('/appointments/{appointment}/payment', [PaymentController::class, 'process'])->name('payments.process');
Route::get('/payments/{payment}/receipt', [PaymentController::class, 'receipt'])->name('payments.receipt');
Route::get('/payments/{payment}/receipt/pdf', [PaymentReceiptController::class, 'show'])->name('payments.receipt.pdf');

// SSLCommerz Official Gateway Endpoints & Callbacks
Route::post('/sslcommerz/pay/{appointment}', [SSLCommerzPaymentController::class, 'pay'])->name('sslcommerz.pay');
Route::any('/sslcommerz/success', [SSLCommerzPaymentController::class, 'success'])->name('sslcommerz.success');
Route::any('/sslcommerz/fail', [SSLCommerzPaymentController::class, 'fail'])->name('sslcommerz.fail');
Route::any('/sslcommerz/cancel', [SSLCommerzPaymentController::class, 'cancel'])->name('sslcommerz.cancel');
Route::post('/sslcommerz/ipn', [SSLCommerzPaymentController::class, 'ipn'])->name('sslcommerz.ipn');

/* -------------------------------------------------------------------------- */
/*                        Patient Portal Authenticated Routes                 */
/* -------------------------------------------------------------------------- */

Route::get('/dashboard', function () {
    $user = auth()->user();

    return match ($user->role) {
        'doctor' => redirect()->route('doctor.dashboard'),
        'admin'  => redirect()->route('admin.dashboard'),
        default  => redirect()->route('patient.history'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    // FR-11: Visit History
    Route::get('/patient/history', [VisitHistoryController::class, 'index'])->name('patient.history');
    Route::get('/appointments', [VisitHistoryController::class, 'index'])->name('appointments.index');

    // FR-12: Medical Reports
    Route::get('/patient/reports', [MedicalReportController::class, 'index'])->name('patient.reports.index');
    Route::post('/patient/reports', [MedicalReportController::class, 'store'])->name('patient.reports.store');
    Route::delete('/patient/reports/{report}', [MedicalReportController::class, 'destroy'])->name('patient.reports.destroy');

    // FR-13: Allergy & Medical Profile
    Route::get('/patient/medical-profile', [MedicalProfileController::class, 'edit'])->name('patient.medical-profile.edit');
    Route::post('/patient/medical-profile', [MedicalProfileController::class, 'update'])->name('patient.medical-profile.update');

    // FR-18: Digital Prescriptions
    Route::get('/patient/prescriptions', [PrescriptionController::class, 'index'])->name('patient.prescriptions.index');
    Route::get('/patient/prescriptions/{prescription}', [PrescriptionController::class, 'show'])->name('patient.prescriptions.show');

    // FR-16: Medication Tracker
    Route::get('/patient/medications', [MedicationTrackerController::class, 'index'])->name('patient.medications.index');
    Route::post('/patient/medications/log', [MedicationTrackerController::class, 'log'])->name('patient.medications.log');
    Route::get('/patient/medications/history', [MedicationTrackerController::class, 'history'])->name('patient.medications.history');

    // FR-19: Doctor Reviews
    Route::get('/patient/appointments/{appointment}/review', [ReviewController::class, 'create'])->name('patient.reviews.create');
    Route::post('/patient/appointments/{appointment}/review', [ReviewController::class, 'store'])->name('patient.reviews.store');

    // Profile settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';