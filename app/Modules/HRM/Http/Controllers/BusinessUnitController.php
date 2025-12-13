<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\BusinessUnit;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;

class BusinessUnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.settings.view')->only(['index', 'show']);
        $this->middleware('permission:hrm.settings.create')->only(['create', 'store']);
        $this->middleware('permission:hrm.settings.edit')->only(['edit', 'update']);
        $this->middleware('permission:hrm.settings.delete')->only(['destroy']);
    }

    /**
     * Display a listing of business units
     */
    public function index(Request $request)
    {
        $query = BusinessUnit::with(['head']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
        }

        $businessUnits = $query->orderBy('name')->paginate(20);

        $employees = Employee::active()->orderBy('first_name')->get();

        return view('HRM::pages.business-units.index', compact('businessUnits', 'employees'));
    }

    /**
     * Show the form for creating a new business unit
     */
    public function create()
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        return view('HRM::pages.business-units.create', compact('employees'));
    }

    /**
     * Store a newly created business unit
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:hrm_business_units,code',
            'head_employee_id' => 'nullable|exists:hrm_employees,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // Generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = 'BU-' . strtoupper(substr($validated['name'], 0, 3)) . '-' . str_pad(BusinessUnit::count() + 1, 3, '0', STR_PAD_LEFT);
        }

        $validated['is_active'] = $request->has('is_active');
        $validated['created_by'] = auth()->id();

        BusinessUnit::create($validated);

        return redirect()
            ->route('hrm.business-units.index')
            ->with('success', 'Business Unit created successfully!');
    }

    /**
     * Display the specified business unit
     */
    public function show(BusinessUnit $businessUnit)
    {
        $businessUnit->load(['head', 'employees']);
        
        $stats = [
            'total_employees' => $businessUnit->employees()->count(),
            'active_employees' => $businessUnit->employees()->active()->count(),
        ];

        return view('HRM::pages.business-units.show', compact('businessUnit', 'stats'));
    }

    /**
     * Show the form for editing the specified business unit
     */
    public function edit(BusinessUnit $businessUnit)
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        return view('HRM::pages.business-units.edit', compact('businessUnit', 'employees'));
    }

    /**
     * Update the specified business unit
     */
    public function update(Request $request, BusinessUnit $businessUnit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:hrm_business_units,code,' . $businessUnit->id,
            'head_employee_id' => 'nullable|exists:hrm_employees,id',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['updated_by'] = auth()->id();

        $businessUnit->update($validated);

        return redirect()
            ->route('hrm.business-units.index')
            ->with('success', 'Business Unit updated successfully!');
    }

    /**
     * Remove the specified business unit
     */
    public function destroy(BusinessUnit $businessUnit)
    {
        if ($businessUnit->employees()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Cannot delete business unit with assigned employees!');
        }

        $businessUnit->delete();

        return redirect()
            ->route('hrm.business-units.index')
            ->with('success', 'Business Unit deleted successfully!');
    }
}
