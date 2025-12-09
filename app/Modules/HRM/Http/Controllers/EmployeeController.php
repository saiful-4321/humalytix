<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\Department;
use App\Modules\HRM\Models\Branch;
use App\Modules\HRM\Models\BusinessUnit;
use App\Modules\HRM\Http\Requests\EmployeeRequest;
use App\Modules\HRM\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Barryvdh\DomPDF\Facade\Pdf;

class EmployeeController extends Controller
{
    protected $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
        
        // Apply permissions
        $this->middleware('permission:hrm.employees.view')->only(['index', 'show']);
        $this->middleware('permission:hrm.employees.create')->only(['create', 'store']);
        $this->middleware('permission:hrm.employees.edit')->only(['edit', 'update']);
        $this->middleware('permission:hrm.employees.delete')->only(['destroy']);
        $this->middleware('permission:hrm.employees.export')->only(['export', 'sample']);
        $this->middleware('permission:hrm.employees.import')->only(['import']);
    }

    /**
     * Display a listing of employees
     */
    public function index(Request $request)
    {
        $query = Employee::with(['department', 'branch', 'reportingManager']);

        // Apply filters
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $employees = $query->paginate($request->get('per_page', 15));

        // Get filter options
        $departments = Department::active()->orderBy('name')->get();
        $branches = Branch::active()->orderBy('name')->get();

        return view('HRM::pages.employees.index', compact('employees', 'departments', 'branches'));
    }

    /**
     * Show the form for creating a new employee
     */
    public function create()
    {
        $departments = Department::active()->orderBy('name')->get();
        $branches = Branch::active()->orderBy('name')->get();
        $businessUnits = BusinessUnit::active()->orderBy('name')->get();
        $managers = Employee::active()->orderBy('first_name')->get();

        return view('HRM::pages.employees.create', compact(
            'departments',
            'branches',
            'businessUnits',
            'managers'
        ));
    }

    /**
     * Store a newly created employee
     */
    public function store(EmployeeRequest $request)
    {
        try {
            $employee = $this->employeeService->createEmployee($request->validated());

            return redirect()
                ->route('hrm.employees.show', $employee)
                ->with('success', 'Employee created successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create employee: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified employee
     */
    public function show(Employee $employee)
    {
        $employee->load([
            'department',
            'branch',
            'businessUnit',
            'reportingManager',
            'subordinates',
            'familyMembers',
            'documents.documentType',
            'employmentHistories',
            'skills.skill',
            'lifecycles',
            'letters',
        ]);

        $hierarchy = $this->employeeService->getEmployeeHierarchy($employee);
        
        // Get document types and skills for modals
        $documentTypes = \App\Modules\HRM\Models\DocumentType::active()->orderBy('name')->get();
        $allSkills = \App\Modules\HRM\Models\Skill::active()->orderBy('name')->get();

        return view('HRM::pages.employees.show', compact('employee', 'hierarchy', 'documentTypes', 'allSkills'));
    }

    /**
     * Show the form for editing the specified employee
     */
    public function edit(Employee $employee)
    {
        $departments = Department::active()->orderBy('name')->get();
        $branches = Branch::active()->orderBy('name')->get();
        $businessUnits = BusinessUnit::active()->orderBy('name')->get();
        $managers = Employee::active()
            ->where('id', '!=', $employee->id)
            ->orderBy('first_name')
            ->get();

        return view('HRM::pages.employees.edit', compact(
            'employee',
            'departments',
            'branches',
            'businessUnits',
            'managers'
        ));
    }

    /**
     * Update the specified employee
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        try {
            $this->employeeService->updateEmployee($employee, $request->validated());

            return redirect()
                ->route('hrm.employees.show', $employee)
                ->with('success', 'Employee updated successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update employee: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified employee
     */
    public function destroy(Employee $employee)
    {
        try {
            $this->employeeService->deleteEmployee($employee);

            return redirect()
                ->route('hrm.employees.index')
                ->with('success', 'Employee deleted successfully!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete employee: ' . $e->getMessage());
        }
    }

    /**
     * Export employees
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'xlsx');
        $filters = $request->only(['department_id', 'branch_id', 'status']);

        $employees = $this->employeeService->exportEmployees($filters);

        if ($format === 'pdf') {
            return $this->exportPDF($employees);
        }

        return $this->exportExcel($employees);
    }

    /**
     * Export to Excel
     */
    private function exportExcel($employees)
    {
        $data = $employees->map(function ($employee) {
            return [
                'Employee Code' => $employee->employee_code,
                'Full Name' => $employee->full_name,
                'Email' => $employee->email,
                'Phone' => $employee->phone,
                'Department' => $employee->department->name ?? '',
                'Branch' => $employee->branch->name ?? '',
                'Designation' => $employee->designation,
                'Employment Type' => ucfirst(str_replace('_', ' ', $employee->employment_type)),
                'Joining Date' => $employee->joining_date?->format('Y-m-d'),
                'Status' => ucfirst($employee->status),
                'Basic Salary' => $employee->basic_salary,
            ];
        });

        return (new FastExcel($data))->download('employees_' . date('Y-m-d') . '.xlsx');
    }

    /**
     * Export to PDF
     */
    private function exportPDF($employees)
    {
        $pdf = PDF::loadView('HRM::pages.employees.pdf', compact('employees'));
        return $pdf->download('employees_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Download sample import file
     */
    public function sample()
    {
        $data = [[
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+880-1711-111111',
            'date_of_birth' => '1990-01-15',
            'gender' => 'male',
            'department_code' => 'DEPT-IT',
            'branch_code' => 'BR-HO',
            'designation' => 'Software Engineer',
            'employment_type' => 'full_time',
            'joining_date' => '2024-01-01',
            'status' => 'active',
            'basic_salary' => '50000',
        ]];

        return (new FastExcel($data))->download('employee_import_sample.xlsx');
    }

    /**
     * Import employees
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $employees = (new FastExcel)->import($file);

            $employeeData = [];
            foreach ($employees as $row) {
                // Find department and branch by code
                $department = Department::where('code', $row['department_code'])->first();
                $branch = Branch::where('code', $row['branch_code'])->first();

                if (!$department || !$branch) {
                    continue;
                }

                $employeeData[] = [
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'] ?? null,
                    'date_of_birth' => $row['date_of_birth'] ?? null,
                    'gender' => $row['gender'] ?? null,
                    'department_id' => $department->id,
                    'branch_id' => $branch->id,
                    'designation' => $row['designation'],
                    'employment_type' => $row['employment_type'],
                    'joining_date' => $row['joining_date'],
                    'status' => $row['status'] ?? 'active',
                    'basic_salary' => $row['basic_salary'] ?? null,
                    'created_by' => auth()->id(),
                ];
            }

            $result = $this->employeeService->bulkImport($employeeData);

            return redirect()
                ->route('hrm.employees.index')
                ->with('success', "Successfully imported {$result['imported']} employees. Failed: {$result['failed']}");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
