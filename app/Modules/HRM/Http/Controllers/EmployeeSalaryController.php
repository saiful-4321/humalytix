<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\EmployeeSalary;
use App\Modules\HRM\Models\SalaryStructure;
use Illuminate\Http\Request;

class EmployeeSalaryController extends Controller
{
    public function edit(Employee $employee)
    {
        $structures = SalaryStructure::where('is_active', true)->get();
        // Get the latest active salary
        $salary = $employee->salary;
        
        return view('HRM::pages.employees.salary.edit', compact('employee', 'structures', 'salary'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'salary_structure_id' => 'required|exists:hrm_salary_structures,id',
            'basic_salary' => 'required|numeric|min:0',
            'gross_salary' => 'nullable|numeric|min:0', // Optional if we only focus on Basic
            'effective_date' => 'required|date',
            'is_active' => 'boolean',
        ]);

        // If we are updating, we usually create a NEW record for history tracking (Increment/Decrement)
        // Or update if it's the same month? Let's assume we create new for history if date is different?
        // For simplicity, let's update if exists for same date, else create.
        
        EmployeeSalary::updateOrCreate(
            [
                'employee_id' => $employee->id,
                'effective_date' => $validated['effective_date'], 
            ],
            [
                'salary_structure_id' => $validated['salary_structure_id'],
                'basic_salary' => $validated['basic_salary'],
                'is_active' => $validated['is_active'] ?? true,
            ]
        );

        return redirect()->back()->with('success', 'Employee salary updated successfully.');
    }
    
    // Helper to calculate breakdown preview (AJAX)
    public function calculateBreakdown(Request $request) 
    {
        $structure = SalaryStructure::with('components')->find($request->structure_id);
        $basic = $request->basic_salary;
        
        if (!$structure || !$basic) return response()->json(['html' => '']);
        
        $preview = [];
        $totalEarnings = $basic; // Basic is always an earning
        $totalDeductions = 0;
        
        // Add Basic explicitly
        $preview['earnings'][] = ['name' => 'Basic Salary', 'amount' => $basic];

        foreach($structure->components as $component) {
            $amount = 0;
            // Check pivot override
            $pivotAmount = $component->pivot->amount;
            $pivotPercent = $component->pivot->percentage;
            
            if ($component->calculation_type == 'fixed') {
                $amount = $pivotAmount ?? $component->default_amount ?? 0;
            } elseif ($component->calculation_type == 'percentage') {
                $percent = $pivotPercent ?? $component->default_percentage ?? 0;
                // Currently only supporting % of Basic, but could be extensible
                $amount = ($basic * $percent) / 100;
            }
            
            if ($component->type == 'earning') {
                $totalEarnings += $amount;
                $preview['earnings'][] = ['name' => $component->name, 'amount' => $amount];
            } else {
                $totalDeductions += $amount;
                $preview['deductions'][] = ['name' => $component->name, 'amount' => $amount];
            }
        }
        
        $net = $totalEarnings - $totalDeductions;
        
        return response()->json([
            'preview' => $preview,
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_salary' => $net
        ]);
    }
}
