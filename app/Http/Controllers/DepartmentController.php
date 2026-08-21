<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * FR-01: Department & Specialty Catalogue
     * Browsable, searchable, filterable list of departments.
     */
    public function index(Request $request)
    {
        $departments = Department::query()
            ->active()
            ->search($request->query('q'))
            ->withCount('activeDoctors')
            ->orderBy('name')
            ->paginate(9)
            ->withQueryString();

        if ($request->ajax() || $request->wantsJson()) {
            return view('departments.partials.grid', compact('departments'))->render();
        }

        return view('departments.index', compact('departments'));
    }

    public function show(Department $department)
    {
        abort_unless($department->is_active, 404);

        $doctors = $department->activeDoctors()
            ->with('user')
            ->orderByDesc('avg_rating')
            ->paginate(6);

        return view('departments.show', compact('department', 'doctors'));
    }
}
