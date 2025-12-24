<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Workflow;
use App\Modules\HRM\Models\WorkflowStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkflowController extends Controller
{
    public function index()
    {
        $workflows = Workflow::latest()->get();
        return view('HRM::pages.workflows.index', compact('workflows'));
    }

    public function create()
    {
        return view('HRM::pages.workflows.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'module_type' => 'required|string',
            'steps' => 'required|array|min:1',
            'steps.*.step_name' => 'required|string',
            'steps.*.approver_type' => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            $workflow = Workflow::create([
                'name' => $request->name,
                'module_type' => $request->module_type,
                'description' => $request->description,
                'is_active' => true,
                'created_by' => auth()->id(),
            ]);

            foreach ($request->steps as $index => $stepData) {
                $workflow->steps()->create([
                    'step_order' => $index + 1,
                    'step_name' => $stepData['step_name'],
                    'approver_type' => $stepData['approver_type'],
                    'approver_id' => $stepData['approver_id'] ?? null,
                    'conditions' => $stepData['conditions'] ?? null,
                    'auto_action' => $stepData['auto_action'] ?? null,
                ]);
            }
        });

        return redirect()->route('hrm.workflows.index')->with('success', 'Workflow created successfully.');
    }

    public function edit(Workflow $workflow)
    {
        return view('HRM::pages.workflows.edit', compact('workflow'));
    }

    public function update(Request $request, Workflow $workflow)
    {
        // For simplicity in this iteration, we might just update basic info
        // Additional logic needed for updating steps safely
        $workflow->update($request->only('name', 'description', 'is_active'));
        
        return redirect()->route('hrm.workflows.index')->with('success', 'Workflow updated.');
    }

    public function destroy(Workflow $workflow)
    {
        $workflow->delete();
        return back()->with('success', 'Workflow deleted.');
    }
}
