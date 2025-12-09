<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\EmployeeBonus;
use App\Modules\HRM\Models\BonusType;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;

class BonusController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeBonus::with(['employee', 'bonusType']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('year')) {
            $query->where('bonus_year', $request->year);
        }

        $bonuses = $query->orderBy('bonus_date', 'desc')->paginate(20);
        
        // For offcanvas form
        $employees = Employee::active()->orderBy('first_name')->get();
        $bonusTypes = BonusType::active()->get();

        return view('HRM::pages.bonuses.index', compact('bonuses', 'employees', 'bonusTypes'));
    }

    public function create()
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        $bonusTypes = BonusType::active()->get();

        return view('HRM::pages.bonuses.create', compact('employees', 'bonusTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'bonus_type_id' => 'nullable|exists:hrm_bonus_types,id',
            'bonus_name' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'bonus_date' => 'required|date',
            'bonus_month' => 'nullable|integer|min:1|max:12',
            'bonus_year' => 'required|integer',
        ]);

        $bonusType = $validated['bonus_type_id'] ? BonusType::find($validated['bonus_type_id']) : null;

        EmployeeBonus::create([
            'employee_id' => $validated['employee_id'],
            'bonus_type_id' => $validated['bonus_type_id'],
            'bonus_name' => $validated['bonus_name'],
            'amount' => $validated['amount'],
            'bonus_date' => $validated['bonus_date'],
            'bonus_month' => $validated['bonus_month'] ?? date('n', strtotime($validated['bonus_date'])),
            'bonus_year' => $validated['bonus_year'],
            'is_taxable' => $bonusType?->is_taxable ?? true,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('hrm.bonuses.index')->with('success', 'Bonus created.');
    }

    public function approve(EmployeeBonus $bonus)
    {
        $bonus->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Bonus approved.');
    }

    public function reject(EmployeeBonus $bonus)
    {
        $bonus->update(['status' => 'rejected']);
        return back()->with('success', 'Bonus rejected.');
    }

    public function destroy(EmployeeBonus $bonus)
    {
        if ($bonus->status === 'paid') {
            return back()->with('error', 'Cannot delete paid bonus.');
        }

        $bonus->delete();
        return redirect()->route('hrm.bonuses.index')->with('success', 'Bonus deleted.');
    }
}
