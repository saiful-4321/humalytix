<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\SalaryStructure;
use App\Modules\HRM\Models\SalaryComponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryStructureController extends Controller
{
    public function index()
    {
        $structures = SalaryStructure::with('components')->get();
        return view('HRM::pages.settings.salary_structures.index', compact('structures'));
    }

    public function create()
    {
        $earnings = SalaryComponent::where('type', 'earning')->where('is_active', true)->get();
        $deductions = SalaryComponent::where('type', 'deduction')->where('is_active', true)->get();
        return view('HRM::pages.settings.salary_structures.create', compact('earnings', 'deductions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'components' => 'array',
            'components.*.id' => 'exists:hrm_salary_components,id',
        ]);

        DB::transaction(function () use ($request) {
            $structure = SalaryStructure::create([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);

            if ($request->has('components')) {
                foreach ($request->components as $compId => $data) {
                    if (isset($data['enabled'])) {
                        $structure->components()->attach($compId, [
                            'amount' => $data['amount'] ?? null,
                            'percentage' => $data['percentage'] ?? null,
                            // We can add calculation_type override logic here if needed, but keeping it simple for now
                        ]);
                    }
                }
            }
        });

        return redirect()->route('hrm.settings.salary-structures.index')->with('success', 'Salary Structure created successfully.');
    }

    public function edit(SalaryStructure $salaryStructure)
    {
        $salaryStructure->load('components');
        $earnings = SalaryComponent::where('type', 'earning')->where('is_active', true)->get();
        $deductions = SalaryComponent::where('type', 'deduction')->where('is_active', true)->get();
        
        // Prepare pivot data for easier access in view
        $structureComponents = $salaryStructure->components->keyBy('id');

        return view('HRM::pages.settings.salary_structures.edit', compact('salaryStructure', 'earnings', 'deductions', 'structureComponents'));
    }

    public function update(Request $request, SalaryStructure $salaryStructure)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $salaryStructure) {
            $salaryStructure->update([
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Sync components
            $syncData = [];
            if ($request->has('components')) {
                foreach ($request->components as $compId => $data) {
                    if (isset($data['enabled'])) {
                        $syncData[$compId] = [
                            'amount' => $data['amount'] ?? null, 
                            'percentage' => $data['percentage'] ?? null
                        ];
                    }
                }
            }
            $salaryStructure->components()->sync($syncData);
        });

        return redirect()->route('hrm.settings.salary-structures.index')->with('success', 'Salary Structure updated successfully.');
    }

    public function destroy(SalaryStructure $salaryStructure)
    {
        // Check if assigned to users first? 
        $salaryStructure->delete();
        return redirect()->route('hrm.settings.salary-structures.index')->with('success', 'Salary Structure deleted.');
    }
}
