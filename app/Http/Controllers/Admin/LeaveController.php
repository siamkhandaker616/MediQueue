<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoctorLeave;
use Illuminate\Http\RedirectResponse;

class LeaveController extends Controller
{
    public function approve(DoctorLeave $leave): RedirectResponse
    {
        if ($leave->status === DoctorLeave::STATUS_PENDING) {
            $leave->update(['status' => DoctorLeave::STATUS_APPROVED]);
        }

        return back()->with('status', 'Leave approved.');
    }

    public function reject(DoctorLeave $leave): RedirectResponse
    {
        if ($leave->status === DoctorLeave::STATUS_PENDING) {
            $leave->update(['status' => DoctorLeave::STATUS_REJECTED]);
        }

        return back()->with('status', 'Leave rejected.');
    }
}