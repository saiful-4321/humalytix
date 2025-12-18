<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Contract;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()->can('hrm.contracts.view_all')) abort(403);
        
        $query = Contract::with('employee')->latest();

        // Filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }
        if ($request->has('employee_id') && $request->employee_id != '') {
            $query->where('employee_id', $request->employee_id);
        }

        $contracts = $query->paginate(10);
        $employees = Employee::active()->select('id', 'first_name', 'last_name', 'employee_code')->get();
        
        return view('HRM::pages.compliance.contracts.index', compact('contracts', 'employees'));
    }

    public function myContracts()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        if (!$employee) abort(403, 'Employee profile not found.');

        $contracts = Contract::where('employee_id', $employee->id)
            ->whereIn('status', ['sent', 'signed', 'expired'])
            ->latest()
            ->get();

        return view('HRM::pages.compliance.contracts.my_contracts', compact('contracts'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('hrm.contracts.create')) abort(403);
        $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'title' => 'required|string',
            'file' => 'required|file|mimes:pdf|max:10240',
            'start_date' => 'required|date',
            'type' => 'required|string',
        ]);

        $path = $request->file('file')->store('contracts', 'public');

        Contract::create([
            'employee_id' => $request->employee_id,
            'title' => $request->title,
            'file_path' => $path,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'type' => $request->type,
            'status' => 'sent', // Auto send on create for now
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Contract created and sent to employee.');
    }

    public function sign(Request $request, Contract $contract)
    {
        // Ensure user owns this contract
        $employee = Employee::where('user_id', auth()->id())->firstOrFail();
        if ($contract->employee_id !== $employee->id) abort(403);
        if ($contract->status !== 'sent') abort(400, 'Contract cannot be signed.');

        $request->validate([
            'signature_image' => 'required|string', // Base64
        ]);

        $contract->signatures()->create([
            'user_id' => auth()->id(),
            'signature_image' => $request->signature_image,
            'ip_address' => $request->ip(),
            'signed_at' => now(),
        ]);

        $contract->update(['status' => 'signed']);

        return back()->with('success', 'Contract signed successfully.');
    }
}
