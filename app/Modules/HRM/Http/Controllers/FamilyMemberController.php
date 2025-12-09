<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\FamilyMember;
use Illuminate\Http\Request;

class FamilyMemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.employees.edit');
    }

    /**
     * Store a new family member
     */
    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'contact_number' => 'nullable|string|max:20',
            'occupation' => 'nullable|string|max:255',
            'is_dependent' => 'nullable|boolean',
            'is_emergency_contact' => 'nullable|boolean',
        ]);

        $employee->familyMembers()->create($validated);

        return redirect()
            ->route('hrm.employees.show', $employee)
            ->with('success', 'Family member added successfully!');
    }

    /**
     * Update family member
     */
    public function update(Request $request, Employee $employee, FamilyMember $familyMember)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'relationship' => 'required|string|max:100',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'contact_number' => 'nullable|string|max:20',
            'occupation' => 'nullable|string|max:255',
            'is_dependent' => 'nullable|boolean',
            'is_emergency_contact' => 'nullable|boolean',
        ]);

        $familyMember->update($validated);

        return redirect()
            ->route('hrm.employees.show', $employee)
            ->with('success', 'Family member updated successfully!');
    }

    /**
     * Delete family member
     */
    public function destroy(Employee $employee, FamilyMember $familyMember)
    {
        $familyMember->delete();

        return redirect()
            ->route('hrm.employees.show', $employee)
            ->with('success', 'Family member deleted successfully!');
    }
}
