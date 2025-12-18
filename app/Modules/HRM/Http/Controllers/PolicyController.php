<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PolicyController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('hrm.policies.view')) abort(403);
        $policies = Policy::latest()->get();
        return view('HRM::pages.compliance.policies.index', compact('policies'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('hrm.policies.create')) abort(403);
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf|max:10240', // 10MB
            'effective_date' => 'nullable|date',
            'version' => 'nullable|string',
        ]);

        $path = $request->file('file')->store('policies', 'public');

        Policy::create([
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $path,
            'version' => $request->version ?? '1.0',
            'effective_date' => $request->effective_date,
            'status' => 'published',
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Policy uploaded successfully.');
    }

    public function destroy(Policy $policy)
    {
        if (!auth()->user()->can('hrm.policies.delete')) abort(403);
        Storage::disk('public')->delete($policy->file_path);
        $policy->delete();
        return back()->with('success', 'Policy deleted.');
    }
}
