<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Modules\HRM\Models\ExpenseCategory;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::latest()->get();
        return view('HRM::pages.settings.expenses.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        ExpenseCategory::create($validated);

        return redirect()->back()->with('success', 'Expense category created successfully.');
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Handle checkbox unchecked
        if(!$request->has('is_active')) {
            $validated['is_active'] = false;
        }

        $expenseCategory->update($validated);

        return redirect()->back()->with('success', 'Expense category updated successfully.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();
        return redirect()->back()->with('success', 'Expense category deleted successfully.');
    }
}
