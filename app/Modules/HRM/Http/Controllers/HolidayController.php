<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
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
        $query = Holiday::orderBy('start_date', 'asc');

        if ($request->filled('year')) {
            $query->whereYear('start_date', $request->year);
        } else {
            $query->whereYear('start_date', date('Y'));
        }

        $holidays = $query->get();
        return view('HRM::pages.settings.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:public,company,weekend',
            'description' => 'nullable|string',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['is_active'] = $request->has('is_active');

        Holiday::create($validated);

        return redirect()->back()->with('success', 'Holiday created successfully.');
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:public,company,weekend',
            'description' => 'nullable|string',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = auth()->id();
        $validated['is_recurring'] = $request->has('is_recurring');
        $validated['is_active'] = $request->has('is_active');

        $holiday->update($validated);

        return redirect()->back()->with('success', 'Holiday updated successfully.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return redirect()->back()->with('success', 'Holiday deleted successfully.');
    }
}
