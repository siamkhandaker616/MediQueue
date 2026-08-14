<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::withCount(['doctors', 'appointments'])->get();

        return view('admin.departments', compact('departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        Department::create($data + ['slug' => Str::slug($data['name']), 'is_active' => true]);

        return back()->with('status', 'Department added.');
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $data = $this->validated($request);

        $department->update($data + ['slug' => Str::slug($data['name'])]);

        return back()->with('status', 'Department updated.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        abort_if($department->doctors()->exists(), 422, 'Cannot delete a department that has doctors.');

        $department->delete();

        return back()->with('status', 'Department deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'fee_range' => ['required', 'string', 'max:100'],
            'floor_number' => ['nullable', 'integer'],
            'room_number' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
