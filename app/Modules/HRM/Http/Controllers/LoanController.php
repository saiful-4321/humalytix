<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\EmployeeLoan;
use App\Modules\HRM\Models\LoanType;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeLoan::with(['employee', 'loanType']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $query->whereHas('employee', function($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('employee_code', 'like', "%{$request->search}%");
            });
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // For offcanvas form
        $employees = Employee::active()->orderBy('first_name')->get();
        $loanTypes = LoanType::active()->get();

        return view('HRM::pages.loans.index', compact('loans', 'employees', 'loanTypes'));
    }

    public function create()
    {
        $employees = Employee::active()->orderBy('first_name')->get();
        $loanTypes = LoanType::active()->get();

        return view('HRM::pages.loans.create', compact('employees', 'loanTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'loan_type_id' => 'required|exists:hrm_loan_types,id',
            'loan_amount' => 'required|numeric|min:1',
            'interest_rate' => 'required|numeric|min:0',
            'interest_type' => 'required|in:flat,reducing',
            'tenure_months' => 'required|integer|min:1',
            'disbursement_date' => 'required|date',
            'purpose' => 'nullable|string',
        ]);

        $loanType = LoanType::find($validated['loan_type_id']);
        
        // Validations
        if ($loanType->max_amount && $validated['loan_amount'] > $loanType->max_amount) {
            return back()->withErrors(['loan_amount' => 'Loan amount exceeds maximum allowed for this type.']);
        }

        if ($loanType->max_tenure_months && $validated['tenure_months'] > $loanType->max_tenure_months) {
            return back()->withErrors(['tenure_months' => 'Tenure exceeds maximum allowed for this type.']);
        }

        // Calculate installment
        $monthlyInstallment = $validated['loan_amount'] / $validated['tenure_months'];
        $totalInterest = 0;

        if ($validated['interest_rate'] > 0) {
            if ($validated['interest_type'] === 'flat') {
                $totalInterest = ($validated['loan_amount'] * $validated['interest_rate'] * $validated['tenure_months']) / (100 * 12);
            } else {
                // Simple reducing balance approximation
                $totalInterest = ($validated['loan_amount'] * $validated['interest_rate'] * $validated['tenure_months']) / (100 * 24);
            }
        }

        $totalPayable = $validated['loan_amount'] + $totalInterest;
        $monthlyInstallment = $totalPayable / $validated['tenure_months'];

        $disbursementDate = Carbon::parse($validated['disbursement_date']);
        $firstInstallmentDate = $disbursementDate->copy()->addMonth();

        $loan = EmployeeLoan::create([
            'employee_id' => $validated['employee_id'],
            'loan_type_id' => $validated['loan_type_id'],
            'loan_amount' => $validated['loan_amount'],
            'interest_rate' => $validated['interest_rate'],
            'interest_type' => $validated['interest_type'],
            'tenure_months' => $validated['tenure_months'],
            'monthly_installment' => $monthlyInstallment,
            'total_payable' => $totalPayable,
            'outstanding_balance' => $totalPayable,
            'disbursement_date' => $disbursementDate,
            'first_installment_date' => $firstInstallmentDate,
            'purpose' => $validated['purpose'],
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('hrm.loans.index')->with('success', 'Loan application created successfully.');
    }

    public function show(EmployeeLoan $loan)
    {
        $loan->load(['employee', 'loanType', 'installments']);
        return view('HRM::pages.loans.show', compact('loan'));
    }

    public function approve(EmployeeLoan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Only pending loans can be approved.');
        }

        $loan->update([
            'status' => 'active',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Generate installments
        $loan->generateInstallments();

        return back()->with('success', 'Loan approved and installments generated.');
    }

    public function reject(EmployeeLoan $loan)
    {
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Only pending loans can be rejected.');
        }

        $loan->update(['status' => 'rejected']);

        return back()->with('success', 'Loan application rejected.');
    }

    public function destroy(EmployeeLoan $loan)
    {
        if ($loan->status === 'active') {
            return back()->with('error', 'Cannot delete active loans.');
        }

        $loan->delete();
        return redirect()->route('hrm.loans.index')->with('success', 'Loan deleted.');
    }
}
