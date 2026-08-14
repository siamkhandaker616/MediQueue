<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * FR-02: Doctor Directory
     * Filterable by department, specialty text search, and minimum rating.
     */
    public function index(Request $request)
    {
        $doctors = Doctor::query()
            ->active()
            ->with(['user', 'department'])
            ->search($request->query('q'))
            ->inDepartment($request->query('department_id'))
            ->minRating($request->query('min_rating'))
            ->orderByDesc('avg_rating')
            ->paginate(9)
            ->withQueryString();

        $departments = Department::active()->orderBy('name')->get(['id', 'name']);

        if ($request->ajax() || $request->wantsJson()) {
            return view('doctors.partials.grid', compact('doctors'))->render();
        }

        return view('doctors.index', compact('doctors', 'departments'));
    }

    /**
     * FR-02: individual doctor profile page.
     */
    public function show(Doctor $doctor)
    {
        abort_unless($doctor->is_active, 404);

        $doctor->load(['user', 'department']);

        return view('doctors.show', compact('doctor'));
    }
}
