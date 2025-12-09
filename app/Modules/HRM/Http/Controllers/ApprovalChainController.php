<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\ApprovalChain;
use App\Modules\HRM\Models\ApprovalChainLevel;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;

class ApprovalChainController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.leaves.view')->only(['index']);
        $this->middleware('permission:hrm.leaves.create')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $chains = ApprovalChain::with('levels')->get();
        // Pass employees for the Create Offcanvas
        $employees = Employee::active()->select('id', 'user_id', 'first_name', 'last_name', 'designation')->get();
        return view('HRM::pages.settings.approval_chains.index', compact('chains', 'employees'));
    }

    public function create()
    {
        $employees = Employee::active()->get();
        return view('HRM::pages.settings.approval_chains.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'levels' => 'required|array|min:1',
            'levels.*.type' => 'required|in:reporting_manager,designation,specific_user',
            'levels.*.value' => 'nullable|string',
        ]);

        $chain = ApprovalChain::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => auth()->id(),
        ]);

        foreach ($request->levels as $index => $level) {
            ApprovalChainLevel::create([
                'approval_chain_id' => $chain->id,
                'level' => $index + 1,
                'approver_type' => $level['type'],
                'approver_value' => $level['value'] ?? null,
            ]);
        }

        return redirect()->route('hrm.settings.approval-chains.index')->with('success', 'Approval Chain created successfully!');
    }

    public function edit(ApprovalChain $approvalChain)
    {
        $approvalChain->load('levels');
        $employees = Employee::active()->get();
        return view('HRM::pages.settings.approval_chains.edit', compact('approvalChain', 'employees'));
    }

    public function update(Request $request, ApprovalChain $approvalChain)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'levels' => 'required|array|min:1',
        ]);

        $approvalChain->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        // Sync levels: Delete old, add new
        $approvalChain->levels()->delete();

        foreach ($request->levels as $index => $level) {
            ApprovalChainLevel::create([
                'approval_chain_id' => $approvalChain->id,
                'level' => $index + 1,
                'approver_type' => $level['type'],
                'approver_value' => $level['value'] ?? null,
            ]);
        }

        return redirect()->route('hrm.settings.approval-chains.index')->with('success', 'Approval Chain updated successfully!');
    }

    public function destroy(ApprovalChain $approvalChain)
    {
        if ($approvalChain->levels()->count() > 0) {
           $approvalChain->levels()->delete();
        }
        $approvalChain->delete();
        return back()->with('success', 'Chain deleted.');
    }
}
