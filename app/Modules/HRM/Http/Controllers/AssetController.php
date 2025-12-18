<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Asset;
use App\Modules\HRM\Models\AssetCategory;
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
        $query = Asset::with(['currentAssignment.employee', 'category']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category_id')) {
            $query->where('asset_category_id', $request->category_id);
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = AssetCategory::where('is_active', true)->orderBy('name')->get();
        $employees = Employee::active()->select('id', 'first_name', 'last_name', 'employee_code')->get();

        return view('HRM::pages.assets.index', compact('assets', 'categories', 'employees'));
    }

    public function create()
    {
        $categories = AssetCategory::where('is_active', true)->orderBy('name')->get();
        return view('HRM::pages.assets.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:hrm_assets,code',
            'asset_category_id' => 'required|exists:hrm_asset_categories,id',
            'serial_number' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'salvage_value' => 'nullable|numeric|min:0',
            'condition' => 'required|string',
            'status' => 'required|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        
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
        $categories = AssetCategory::where('is_active', true)->orderBy('name')->get();
        return view('HRM::pages.assets.edit', compact('asset', 'categories'));
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:hrm_assets,code,'.$asset->id,
            'asset_category_id' => 'required|exists:hrm_asset_categories,id',
            'serial_number' => 'nullable|string',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'salvage_value' => 'nullable|numeric|min:0',
            'condition' => 'required|string',
            'status' => 'required|string',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
        ]);
        
        $validated['updated_by'] = auth()->id();

        $asset->update($validated);
        return redirect()->route('hrm.assets.index')->with('success', 'Asset updated successfully!');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('hrm.assets.index')->with('success', 'Asset deleted successfully!');
    }

    public function myAssets()
    {
        $employee = Employee::where('user_id', auth()->id())->first();

        if (!$employee) {
            $assets = collect(); // Empty collection if not an employee
        } else {
            $assets = AssetAssignment::with(['asset.category'])
                ->where('employee_id', $employee->id)
                ->whereNull('return_date')
                ->latest()
                ->get()
                ->pluck('asset');
        }

        return view('HRM::pages.assets.my_assets', compact('assets'));
    }
}
