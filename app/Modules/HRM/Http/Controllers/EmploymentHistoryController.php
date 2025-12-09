<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\EmploymentHistory;
use Illuminate\Http\Request;

class EmploymentHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.employees.edit');
    }

    /**
     * Store employment history
     */
    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'responsibilities' => 'nullable|string',
            'reason_for_leaving' => 'nullable|string',
            'reference_person' => 'nullable|string|max:255',
            'reference_contact' => 'nullable|string|max:100',
        ]);

        $employee->employmentHistories()->create($validated);

        return redirect()
            ->route('hrm.employees.show', $employee)
            ->with('success', 'Employment history added successfully!');
    }

    /**
     * Update employment history
     */
    public function update(Request $request, Employee $employee, EmploymentHistory $history)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'responsibilities' => 'nullable|string',
            'reason_for_leaving' => 'nullable|string',
            'reference_person' => 'nullable|string|max:255',
            'reference_contact' => 'nullable|string|max:100',
        ]);

        $history->update($validated);

        return redirect()
            ->route('hrm.employees.show', $employee)
            ->with('success', 'Employment history updated successfully!');
    }

    /**
     * Delete employment history
     */
    public function destroy(Employee $employee, EmploymentHistory $history)
    {
        $history->delete();

        return redirect()
            ->route('hrm.employees.show', $employee)
            ->with('success', 'Employment history deleted successfully!');
    }
}
