<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\LeaveType;
use App\Modules\HRM\Models\ApprovalChain;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.leaves.view')->only(['index']);
        $this->middleware('permission:hrm.leaves.create')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $leaveTypes = LeaveType::with('approvalChain')->orderBy('name')->paginate(20);
        $approvalChains = ApprovalChain::select('id', 'name')->withCount('levels')->get(); // For Create Offcanvas
        return view('HRM::pages.settings.leave_types.index', compact('leaveTypes', 'approvalChains'));
    }

    public function create()
    {
        $approvalChains = ApprovalChain::all();
        return view('HRM::pages.settings.leave_types.create', compact('approvalChains'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:hrm_leave_types,code',
            'days_per_year' => 'required|integer|min:0',
            'is_paid' => 'boolean',
            'is_unlimited' => 'boolean',
            'carry_forward_limit' => 'nullable|integer',
            'approval_chain_id' => 'nullable|exists:hrm_approval_chains,id',
            'description' => 'nullable|string',
            'requires_attachment' => 'boolean',
        ]);

        $validated['is_paid'] = $request->has('is_paid');
        $validated['is_unlimited'] = $request->has('is_unlimited');
        $validated['requires_attachment'] = $request->has('requires_attachment');
        $validated['is_active'] = true;
        $validated['created_by'] = auth()->id();

        LeaveType::create($validated);

        return redirect()->route('hrm.settings.leave-types.index')->with('success', 'Leave Policy created successfully!');
    }

    public function edit(LeaveType $leaveType)
    {
        $approvalChains = ApprovalChain::all();
        return view('HRM::pages.settings.leave_types.edit', compact('leaveType', 'approvalChains'));
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:hrm_leave_types,code,' . $leaveType->id,
            'days_per_year' => 'required|integer|min:0',
            'is_paid' => 'boolean',
            'is_unlimited' => 'boolean',
            'carry_forward_limit' => 'nullable|integer',
            'approval_chain_id' => 'nullable|exists:hrm_approval_chains,id',
            'description' => 'nullable|string',
            'requires_attachment' => 'boolean',
        ]);

        $validated['is_paid'] = $request->has('is_paid');
        $validated['is_unlimited'] = $request->has('is_unlimited');
        $validated['requires_attachment'] = $request->has('requires_attachment');

        $leaveType->update($validated);

        return redirect()->route('hrm.settings.leave-types.index')->with('success', 'Leave Policy updated successfully!');
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();
        return back()->with('success', 'Leave Policy deleted.');
    }
}
