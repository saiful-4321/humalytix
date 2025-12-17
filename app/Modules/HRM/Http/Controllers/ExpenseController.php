<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Modules\HRM\Models\Expense;
use App\Modules\HRM\Models\ExpenseCategory;
use App\Modules\HRM\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employee = $user->employee;

        // My Expenses
        $myExpenses = Expense::where('employee_id', $employee->id)
            ->latest()
            ->paginate(10, ['*'], 'my_page');

        // Team Expenses (for Approvers)
        $pendingApprovals = collect();
        if ($user->can('hrm.expenses.approve')) {
             // Logic: Managers see expenses of their subordinates OR all if Admin
             // For now, simple logic: show all pending if has permission
             $pendingApprovals = Expense::where('status', 'pending')
                ->where('employee_id', '!=', $employee->id) // Don't approve own
                ->with(['employee', 'category'])
                ->latest()
                ->get();
        }

        $categories = ExpenseCategory::where('is_active', true)->orderBy('name')->get();

        return view('HRM::pages.expenses.index', compact('myExpenses', 'pendingApprovals', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:hrm_expense_categories,id',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
            'receipt' => 'nullable|file|mimes:jpeg,png,pdf,jpg|max:2048',
        ]);

        $employee = auth()->user()->employee;
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee record not found.');
        }

        $path = null;
        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('expenses', 'public');
        }

        Expense::create([
            'employee_id' => $employee->id,
            'expense_category_id' => $validated['expense_category_id'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'description' => $validated['description'],
            'receipt_path' => $path,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Expense claim submitted successfully.');
    }

    public function approve(Expense $expense)
    {
        // Simple permission check
        if (!auth()->user()->can('hrm.expenses.approve')) {
            abort(403);
        }

        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Expense approved for reimbursement.');
    }

    public function reject(Request $request, Expense $expense)
    {
        // Simple permission check
        if (!auth()->user()->can('hrm.expenses.approve')) {
            abort(403);
        }

        $expense->update([
            'status' => 'rejected',
            'rejection_reason' => $request->input('rejection_reason', 'Rejected by approver'),
            'approved_by' => auth()->id(), // tracked as actioner
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Expense rejected.');
    }
}
