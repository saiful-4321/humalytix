<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Skill;
use Illuminate\Http\Request;

class SkillController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.skills.view')->only(['index']);
        $this->middleware('permission:hrm.skills.manage')->only(['store', 'create', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $skills = Skill::orderBy('name')->get();
        return view('HRM::pages.settings.skills.index', compact('skills'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hrm_skills,name',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        Skill::create($validated);

        return redirect()->route('hrm.skills.index')->with('success', 'Skill created successfully.');
    }

    public function update(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:hrm_skills,name,' . $skill->id,
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        $skill->update($validated);

        return redirect()->route('hrm.skills.index')->with('success', 'Skill updated successfully.');
    }

    public function destroy(Skill $skill)
    {
        $skill->delete();
        return redirect()->route('hrm.skills.index')->with('success', 'Skill deleted successfully.');
    }
}
