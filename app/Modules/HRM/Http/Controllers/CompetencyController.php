<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Competency;
use Illuminate\Http\Request;

class CompetencyController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.settings.view')->only(['index']);
        $this->middleware('permission:hrm.settings.create')->only(['store']);
        $this->middleware('permission:hrm.settings.edit')->only(['update']);
        $this->middleware('permission:hrm.settings.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Competency::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $competencies = $query->orderBy('type')->orderBy('name')->paginate(20);

        return view('HRM::pages.performance.competencies.index', compact('competencies'));
    }

    public function show(Competency $competency)
    {
        if (request()->ajax()) {
            return response()->json($competency);
        }
        return response()->json($competency);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:core,functional,leadership',
            'is_active' => 'boolean',
        ]);

        $competency = Competency::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Competency created successfully!',
                'data' => $competency
            ]);
        }

        return redirect()->route('hrm.competencies.index')->with('success', 'Competency created successfully!');
    }

    public function update(Request $request, Competency $competency)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:core,functional,leadership',
            'is_active' => 'boolean',
        ]);

        $competency->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Competency updated successfully!',
                'data' => $competency
            ]);
        }

        return redirect()->route('hrm.competencies.index')->with('success', 'Competency updated successfully!');
    }

    public function destroy(Competency $competency)
    {
        // Check if used in any appraisals
        if ($competency->ratings()->exists()) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete competency as it has been used in appraisals.'
                ], 422);
            }

            return back()->with('error', 'Cannot delete competency as it has been used in appraisals.');
        }

        $competency->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Competency deleted successfully!'
            ]);
        }

        return redirect()->route('hrm.competencies.index')->with('success', 'Competency deleted successfully!');
    }
}
