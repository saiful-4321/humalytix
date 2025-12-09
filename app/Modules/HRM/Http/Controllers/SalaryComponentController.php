<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\SalaryComponent;
use Illuminate\Http\Request;

class SalaryComponentController extends Controller
{
    public function index()
    {
        $components = SalaryComponent::orderBy('type')->orderBy('name')->get();
        return view('HRM::pages.settings.salary_components.index', compact('components'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:earning,deduction',
            'calculation_type' => 'required|in:fixed,percentage,formula',
            'default_amount' => 'nullable|numeric|required_if:calculation_type,fixed',
            'default_percentage' => 'nullable|numeric|required_if:calculation_type,percentage',
            'percentage_basis_id' => 'nullable|exists:hrm_salary_components,id|required_if:calculation_type,percentage',
            'is_taxable' => 'boolean',
            'is_active' => 'boolean',
        ]);

        SalaryComponent::create($validated);

        return redirect()->back()->with('success', 'Salary Component created successfully.');
    }

    public function update(Request $request, SalaryComponent $salaryComponent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:earning,deduction',
            'calculation_type' => 'required|in:fixed,percentage,formula',
            'default_amount' => 'nullable|numeric',
            'default_percentage' => 'nullable|numeric',
            'percentage_basis_id' => 'nullable|exists:hrm_salary_components,id',
            'is_taxable' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $salaryComponent->update($validated);

        return redirect()->back()->with('success', 'Salary Component updated successfully.');
    }

    public function destroy(SalaryComponent $salaryComponent)
    {
        $salaryComponent->delete();
        return redirect()->back()->with('success', 'Salary Component deleted.');
    }
}
