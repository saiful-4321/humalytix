<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\EmployeeSkill;
use App\Modules\HRM\Models\Skill;
use Illuminate\Http\Request;

class EmployeeSkillController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.employees.edit');
    }

    /**
     * Add skill to employee
     */
    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'skill_id' => 'required|exists:hrm_skills,id',
            'proficiency_level' => 'required|in:beginner,intermediate,advanced,expert',
            'years_of_experience' => 'nullable|integer|min:0',
            'last_used' => 'nullable|date',
            'certification' => 'nullable|string|max:255',
        ]);

        // Check if skill already exists for this employee
        $exists = $employee->skills()->where('skill_id', $validated['skill_id'])->exists();
        
        if ($exists) {
            return redirect()
                ->route('hrm.employees.show', $employee)
                ->with('error', 'This skill is already added to the employee!');
        }

        $employee->skills()->create($validated);

        return redirect()
            ->route('hrm.employees.show', $employee)
            ->with('success', 'Skill added successfully!');
    }

    /**
     * Update employee skill
     */
    public function update(Request $request, Employee $employee, EmployeeSkill $skill)
    {
        $validated = $request->validate([
            'proficiency_level' => 'required|in:beginner,intermediate,advanced,expert',
            'years_of_experience' => 'nullable|integer|min:0',
            'last_used' => 'nullable|date',
            'certification' => 'nullable|string|max:255',
        ]);

        $skill->update($validated);

        return redirect()
            ->route('hrm.employees.show', $employee)
            ->with('success', 'Skill updated successfully!');
    }

    /**
     * Remove skill from employee
     */
    public function destroy(Employee $employee, EmployeeSkill $skill)
    {
        $skill->delete();

        return redirect()
            ->route('hrm.employees.show', $employee)
            ->with('success', 'Skill removed successfully!');
    }
}
