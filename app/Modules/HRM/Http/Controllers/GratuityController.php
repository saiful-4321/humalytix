<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\EmployeeGratuity;
use App\Modules\HRM\Models\GratuityConfig;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GratuityController extends Controller
{
    public function index()
    {
        $gratuities = EmployeeGratuity::with(['employee'])->latest()->paginate(20);
        $config = GratuityConfig::firstOrCreate(
            ['is_active' => true],
            [
                'name' => 'Default Gratuity Policy',
                'min_service_years' => 5,
                'multiplier' => 1.0,
                'max_gratuity_amount' => null,
                'formula_description' => 'Latest Basic Salary * Years of Service * Multiplier',
            ]
        );
        $employees = Employee::active()->orderBy('first_name')->get();
        return view('HRM::pages.gratuity.index', compact('gratuities', 'config', 'employees'));
    }
    
    public function calculator()
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        $config = GratuityConfig::firstOrCreate(
            ['is_active' => true],
            [
                'name' => 'Default Gratuity Policy',
                'min_service_years' => 5,
                'multiplier' => 1.0,
                'max_gratuity_amount' => null,
                'formula_description' => 'Latest Basic Salary * Years of Service * Multiplier',
            ]
        );
        
        return view('HRM::pages.gratuity.calculator', compact('employees', 'config'));
    }
    
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'calculation_date' => 'required|date',
        ]);
        
        $employee = Employee::findOrFail($validated['employee_id']);
        $calculationDate = Carbon::parse($validated['calculation_date']);
        $joiningDate = $employee->joining_date;
        
        if (!$joiningDate) {
            return back()->with('error', 'Employee joining date not found.');
        }
        
        // Calculate service years
        $serviceYears = $joiningDate->diffInYears($calculationDate);
        $serviceDays = $joiningDate->diffInDays($calculationDate);
        $serviceYearsDecimal = $serviceDays / 365;
        
        $config = GratuityConfig::where('is_active', true)->first();
        
        if (!$config) {
            return back()->with('error', 'No active gratuity configuration found.');
        }
        
        // Check minimum service requirement
        if ($serviceYearsDecimal < $config->min_service_years) {
            return back()->with('error', "Employee must complete {$config->min_service_years} years of service to qualify for gratuity.");
        }
        
        // Get last basic salary
        $lastBasic = $employee->salary?->basic_salary ?? $employee->basic_salary ?? 0;
        
        if ($lastBasic == 0) {
            return back()->with('error', 'Employee basic salary not found.');
        }
        
        // Calculate gratuity
        // Formula: (Last Basic Salary × Years of Service × Multiplier)
        $calculatedAmount = ($lastBasic * $serviceYearsDecimal * $config->multiplier);
        
        // Apply cap if set
        if ($config->max_gratuity_amount && $calculatedAmount > $config->max_gratuity_amount) {
            $calculatedAmount = $config->max_gratuity_amount;
        }
        
        // Check if already exists
        $existing = EmployeeGratuity::where('employee_id', $employee->id)
            ->where('calculation_date', $calculationDate)
            ->first();
        
        if ($existing) {
            return back()->with('warning', 'Gratuity already calculated for this employee on this date.');
        }
        
        // Create gratuity record
        EmployeeGratuity::create([
            'employee_id' => $employee->id,
            'gratuity_config_id' => $config->id,
            'calculation_date' => $calculationDate,
            'service_years' => round($serviceYearsDecimal, 2),
            'last_basic_salary' => $lastBasic,
            'calculated_amount' => round($calculatedAmount, 2),
            'approved_amount' => round($calculatedAmount, 2),
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);
        
        return redirect()->route('hrm.gratuity.index')
            ->with('success', "Gratuity calculated: $" . number_format($calculatedAmount, 2));
    }
    
    public function approve(EmployeeGratuity $gratuity)
    {
        $gratuity->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        
        return back()->with('success', 'Gratuity approved.');
    }
    
    public function pay(EmployeeGratuity $gratuity, Request $request)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
        ]);
        
        $gratuity->update([
            'status' => 'paid',
            'payment_date' => $validated['payment_date'],
        ]);
        
        return back()->with('success', 'Gratuity marked as paid.');
    }

    public function config()
    {
        $config = GratuityConfig::firstOrCreate(
            ['is_active' => true],
            [
                'name' => 'Default Gratuity Policy',
                'min_service_years' => 5,
                'multiplier' => 1.0, // 1 basic salary per year
                'calculation_formula' => 'basic * service_years * multiplier',
                'formula_description' => 'Latest Basic Salary * Years of Service * Multiplier',
            ]
        );
        return view('HRM::pages.gratuity.config', compact('config'));
    }

    public function storeConfig(Request $request)
    {
        $validated = $request->validate([
            'min_service_years' => 'required|numeric|min:0',
            'multiplier' => 'required|numeric|min:0',
            'max_gratuity_amount' => 'nullable|numeric|min:0',
            'formula_description' => 'nullable|string',
        ]);
        
        $config = GratuityConfig::orderBy('id', 'desc')->first();
        
        if ($config) {
            $config->update($validated);
        } else {
            GratuityConfig::create(array_merge($validated, [
                'name' => 'Default Gratuity Policy',
                'is_active' => true
            ]));
        }

        return back()->with('success', 'Gratuity configuration updated successfully.');
    }
}
