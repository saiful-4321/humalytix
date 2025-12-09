<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.branches.view')->only(['index', 'show']);
        $this->middleware('permission:hrm.branches.create')->only(['create', 'store']);
        $this->middleware('permission:hrm.branches.edit')->only(['edit', 'update']);
        $this->middleware('permission:hrm.branches.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Branch::with(['manager']);

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        $branches = $query->orderBy('name')->paginate(15);
        $managers = \App\Modules\HRM\Models\Employee::active()->get();

        return view('HRM::pages.branches.index', compact('branches', 'managers'));
    }

    public function create()
    {
        $managers = \App\Modules\HRM\Models\Employee::active()->get();
        return view('HRM::pages.branches.create', compact('managers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:hrm_branches,code',
            'manager_id' => 'nullable|exists:hrm_employees,id',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = 'BR-' . str_pad(Branch::count() + 1, 4, '0', STR_PAD_LEFT);
        }

        $validated['is_active'] = $request->has('is_active');

        Branch::create($validated);

        return redirect()->route('hrm.branches.index')->with('success', 'Branch created successfully!');
    }

    public function show(Branch $branch)
    {
        $branch->load(['manager', 'employees', 'departments']);

        $stats = [
            'total_employees' => $branch->employees()->count(),
            'active_employees' => $branch->employees()->active()->count(),
            'departments' => $branch->departments()->count(),
        ];

        return view('HRM::pages.branches.show', compact('branch', 'stats'));
    }

    public function edit(Branch $branch)
    {
        $managers = \App\Modules\HRM\Models\Employee::active()->get();
        return view('HRM::pages.branches.edit', compact('branch', 'managers'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:hrm_branches,code,' . $branch->id,
            'manager_id' => 'nullable|exists:hrm_employees,id',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'radius' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $branch->update($validated);

        return redirect()->route('hrm.branches.index')->with('success', 'Branch updated successfully!');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->employees()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete branch with active employees!');
        }

        $branch->delete();

        return redirect()->route('hrm.branches.index')->with('success', 'Branch deleted successfully!');
    }
}
