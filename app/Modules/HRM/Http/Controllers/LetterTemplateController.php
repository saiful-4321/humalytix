<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\LetterTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LetterTemplateController extends Controller
{
    public function index()
    {
        $templates = LetterTemplate::orderBy('created_at', 'desc')->paginate(10);
        return view('HRM::pages.settings.letter_templates.index', compact('templates'));
    }

    public function create()
    {
        return view('HRM::pages.settings.letter_templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:appointment,termination,increment,transfer,confirmation,other',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['variables'] = $this->extractVariables($validated['content']); // Helper to find {vars}

        LetterTemplate::create($validated);

        return redirect()->route('hrm.settings.letter-templates.index')->with('success', 'Letter Template created successfully.');
    }

    public function edit(LetterTemplate $letterTemplate)
    {
        return view('HRM::pages.settings.letter_templates.edit', compact('letterTemplate'));
    }

    public function update(Request $request, LetterTemplate $letterTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:appointment,termination,increment,transfer,confirmation,other',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['variables'] = $this->extractVariables($validated['content']);

        $letterTemplate->update($validated);

        return redirect()->route('hrm.settings.letter-templates.index')->with('success', 'Letter Template updated successfully.');
    }

    public function destroy(LetterTemplate $letterTemplate)
    {
        $letterTemplate->delete();
        return redirect()->route('hrm.settings.letter-templates.index')->with('success', 'Letter Template deleted successfully.');
    }

    private function extractVariables($content)
    {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $content, $matches);
        return array_unique($matches[1]);
    }
}
