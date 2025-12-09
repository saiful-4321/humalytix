<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\CostCenter;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.departments.view')->only(['index', 'show']);
        $this->middleware('permission:hrm.departments.create')->only(['create', 'store']);
        $this->middleware('permission:hrm.departments.edit')->only(['edit', 'update']);
        $this->middleware('permission:hrm.departments.delete')->only(['destroy']);
    }

    /**
     * Display a listing of departments
     */
    public function index(Request $request)
    {
        $query = Department::with(['parent', 'headEmployee', 'costCenter']);

        $query = Department::with(['parent', 'head', 'headEmployee', 'costCenter', 'employees']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $departments = $query->orderBy('name')->paginate(20);

        // Data for Create Offcanvas
        $parentDepartments = Department::whereNull('parent_id')->get();
        $employees = \App\Modules\HRM\Models\Employee::active()->get();
        $costCenters = \App\Modules\HRM\Models\CostCenter::active()->get();

        return view('HRM::pages.departments.index', compact('departments', 'parentDepartments', 'employees', 'costCenters'));
    }

    /**
     * Show the form for creating a new department
     */
    public function create()
    {
        $parentDepartments = Department::active()->whereNull('parent_id')->get();
        $employees = Employee::active()->get();
        $costCenters = CostCenter::active()->get();
        
        return view('HRM::pages.departments.create', compact('parentDepartments', 'employees', 'costCenters'));
    }

    /**
     * Store a newly created department
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:hrm_departments,code',
            'parent_id' => 'nullable|exists:hrm_departments,id',
            'head_employee_id' => 'nullable|exists:hrm_employees,id',
            'cost_center_id' => 'nullable|exists:hrm_cost_centers,id',
            'description' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        // Generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = 'DEPT-' . strtoupper(substr($validated['name'], 0, 3)) . '-' . str_pad(Department::count() + 1, 4, '0', STR_PAD_LEFT);
        }

        $validated['is_active'] = $request->has('is_active');

        Department::create($validated);

        return redirect()
            ->route('hrm.departments.index')
            ->with('success', 'Department created successfully!');
    }

    /**
     * Display the specified department
     */
    public function show(Department $department)
    {
        $department->load([
            'parent',
            'children',
            'headEmployee',
            'costCenter',
            'employees',
        ]);

        // Get department statistics
        $stats = [
            'total_employees' => $department->employees()->count(),
            'active_employees' => $department->employees()->active()->count(),
            'sub_departments' => $department->children()->count(),
        ];

        return view('HRM::pages.departments.show', compact('department', 'stats'));
    }

    /**
     * Show the form for editing the specified department
     */
    public function edit(Department $department)
    {
        $parentDepartments = Department::active()
            ->whereNull('parent_id')
            ->where('id', '!=', $department->id)
            ->get();
        $employees = Employee::active()->get();
        $costCenters = CostCenter::active()->get();
        
        return view('HRM::pages.departments.edit', compact('department', 'parentDepartments', 'employees', 'costCenters'));
    }

    /**
     * Update the specified department
     */
    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:hrm_departments,code,' . $department->id,
            'parent_id' => 'nullable|exists:hrm_departments,id',
            'head_employee_id' => 'nullable|exists:hrm_employees,id',
            'cost_center_id' => 'nullable|exists:hrm_cost_centers,id',
            'description' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
        ]);

        // Prevent circular reference
        if ($validated['parent_id'] == $department->id) {
            return redirect()
                ->back()
                ->with('error', 'A department cannot be its own parent!');
        }

        $validated['is_active'] = $request->has('is_active');

        $department->update($validated);

        return redirect()
            ->route('hrm.departments.index')
            ->with('success', 'Department updated successfully!');
    }

    /**
     * Remove the specified department
     */
    public function destroy(Department $department)
    {
        // Check if department has employees
        if ($department->employees()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete department with active employees!');
        }

        // Check if department has sub-departments
        if ($department->children()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete department with sub-departments!');
        }

        $department->delete();

        return redirect()
            ->route('hrm.departments.index')
            ->with('success', 'Department deleted successfully!');
    }

    /**
     * Get organization chart data
     */
    public function orgChart()
    {
        $departments = Department::with(['headEmployee', 'children.headEmployee'])
            ->whereNull('parent_id')
            ->active()
            ->get();

        return view('HRM::pages.departments.org-chart', compact('departments'));
    }
}
