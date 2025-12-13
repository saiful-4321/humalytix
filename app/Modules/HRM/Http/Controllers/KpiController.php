<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Kpi;
use App\Modules\HRM\Models\Department;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.performance.view')->only(['index']);
        $this->middleware('permission:hrm.performance.create')->only(['store']);
        $this->middleware('permission:hrm.performance.edit')->only(['update']);
        $this->middleware('permission:hrm.performance.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Kpi::with(['department']);

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $kpis = $query->orderBy('created_at', 'desc')->paginate(20);
        $departments = Department::active()->orderBy('name')->get();

        return view('HRM::pages.performance.kpis.index', compact('kpis', 'departments'));
    }

    public function show(Kpi $kpi)
    {
        $kpi->load('department');
        
        if (request()->ajax()) {
            return response()->json($kpi);
        }
        
        return response()->json($kpi);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:kpi,kra',
            'department_id' => 'nullable|exists:hrm_departments,id',
            'measurement_unit' => 'nullable|string|max:50',
            'target_value' => 'nullable|numeric|min:0',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,annually',
            'weightage' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();

        $kpi = Kpi::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => ucfirst($request->type) . ' created successfully!',
                'data' => $kpi->load('department')
            ]);
        }

        return redirect()->route('hrm.kpis.index')->with('success', ucfirst($request->type) . ' created successfully!');
    }

    public function update(Request $request, Kpi $kpi)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:kpi,kra',
            'department_id' => 'nullable|exists:hrm_departments,id',
            'measurement_unit' => 'nullable|string|max:50',
            'target_value' => 'nullable|numeric|min:0',
            'frequency' => 'required|in:daily,weekly,monthly,quarterly,annually',
            'weightage' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = auth()->id();

        $kpi->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'KPI/KRA updated successfully!',
                'data' => $kpi->load('department')
            ]);
        }

        return redirect()->route('hrm.kpis.index')->with('success', 'KPI/KRA updated successfully!');
    }

    public function destroy(Kpi $kpi)
    {
        $kpi->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'KPI/KRA deleted successfully!'
            ]);
        }

        return redirect()->route('hrm.kpis.index')->with('success', 'KPI/KRA deleted successfully!');
    }
}
