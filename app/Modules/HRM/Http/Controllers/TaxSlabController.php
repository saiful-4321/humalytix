<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\TaxSlab;
use Illuminate\Http\Request;

class TaxSlabController extends Controller
{
    public function index()
    {
        $taxSlabs = TaxSlab::orderBy('gender')->orderBy('min_income')->get();
        return view('HRM::pages.settings.tax_slabs.index', compact('taxSlabs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'min_income' => 'required|numeric|min:0',
            'max_income' => 'nullable|numeric|gt:min_income',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'deduction_amount' => 'nullable|numeric|min:0',
            'gender' => 'required|in:all,male,female,other',
            'is_active' => 'boolean',
        ]);

        TaxSlab::create($validated);

        return redirect()->back()->with('success', 'Tax Slab created successfully.');
    }

    public function update(Request $request, TaxSlab $taxSlab)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'min_income' => 'required|numeric|min:0',
            'max_income' => 'nullable|numeric|gt:min_income',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'deduction_amount' => 'nullable|numeric|min:0',
            'gender' => 'required|in:all,male,female,other',
            'is_active' => 'boolean',
        ]);

        $taxSlab->update($validated);

        return redirect()->back()->with('success', 'Tax Slab updated successfully.');
    }

    public function destroy(TaxSlab $taxSlab)
    {
        $taxSlab->delete();
        return redirect()->back()->with('success', 'Tax Slab deleted.');
    }
}
