<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Asset;
use App\Modules\HRM\Models\AssetAssignment;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.assets.view')->only(['index', 'show']);
        $this->middleware('permission:hrm.assets.create')->only(['create', 'store', 'assign', 'storeAssignment']);
        $this->middleware('permission:hrm.assets.edit')->only(['edit', 'update', 'returnAsset']);
        $this->middleware('permission:hrm.assets.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Asset::with('currentAssignment.employee');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('HRM::pages.assets.index', compact('assets'));
    }

    public function create()
    {
        return view('HRM::pages.assets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:hrm_assets,code',
            'type' => 'required|string',
            'serial_number' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric',
            'condition' => 'required|string',
            'status' => 'required|string',
        ]);

        Asset::create($validated);
        return redirect()->route('hrm.assets.index')->with('success', 'Asset created successfully!');
    }

    public function assign(Asset $asset)
    {
        $employees = Employee::active()->get();
        return view('HRM::pages.assets.assign', compact('asset', 'employees'));
    }

    public function storeAssignment(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'assigned_date' => 'required|date',
            'assigned_condition' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        if ($asset->status == 'assigned') {
            return back()->with('error', 'Asset is already assigned!');
        }

        AssetAssignment::create([
            'asset_id' => $asset->id,
            'employee_id' => $validated['employee_id'],
            'assigned_date' => $validated['assigned_date'],
            'assigned_condition' => $validated['assigned_condition'],
            'notes' => $validated['notes'],
            'assigned_by' => auth()->id(),
        ]);

        $asset->update(['status' => 'assigned']);

        return redirect()->route('hrm.assets.index')->with('success', 'Asset assigned successfully!');
    }

    public function returnAsset(Request $request, Asset $asset)
    {
        $assignment = $asset->currentAssignment;
        if (!$assignment) {
            return back()->with('error', 'Asset is not currently assigned!');
        }

        $validated = $request->validate([
            'return_date' => 'required|date',
            'return_condition' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $assignment->update([
            'return_date' => $validated['return_date'],
            'return_condition' => $validated['return_condition'],
            'notes' => $assignment->notes . "\nReturn Notes: " . $validated['notes'],
        ]);

        $asset->update([
            'status' => 'available',
            'condition' => $validated['return_condition']
        ]);

        return redirect()->route('hrm.assets.index')->with('success', 'Asset returned successfully!');
    }
    public function edit(Asset $asset)
    {
        return view('HRM::pages.assets.edit', compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:hrm_assets,code,'.$asset->id,
            'type' => 'required|string',
            'serial_number' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric',
            'condition' => 'required|string',
            'status' => 'required|string',
        ]);

        $asset->update($validated);
        return redirect()->route('hrm.assets.index')->with('success', 'Asset updated successfully!');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('hrm.assets.index')->with('success', 'Asset deleted successfully!');
    }

}
