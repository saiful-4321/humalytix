<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Shift;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.attendance.view')->only(['index']);
        $this->middleware('permission:hrm.attendance.create')->only(['store']);
        $this->middleware('permission:hrm.attendance.edit')->only(['update']);
        $this->middleware('permission:hrm.attendance.delete')->only(['destroy']);
    }

    public function index()
    {
        $shifts = Shift::orderBy('start_time')->get();
        return view('HRM::pages.shifts.index', compact('shifts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:hrm_shifts,code',
            'start_time' => 'required',
            'end_time' => 'required',
            'grace_period_minutes' => 'required|integer|min:0',
            'break_duration_minutes' => 'required|integer|min:0',
            'half_day_hours' => 'required|numeric|min:0',
            'full_day_hours' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();

        Shift::create($validated);

        return redirect()->back()->with('success', 'Shift created successfully.');
    }

    public function update(Request $request, Shift $shift)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:hrm_shifts,code,' . $shift->id,
            'start_time' => 'required',
            'end_time' => 'required',
            'grace_period_minutes' => 'required|integer|min:0',
            'break_duration_minutes' => 'required|integer|min:0',
            'half_day_hours' => 'required|numeric|min:0',
            'full_day_hours' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = auth()->id();
        // Handle checkbox
        $validated['is_active'] = $request->has('is_active');

        $shift->update($validated);

        return redirect()->back()->with('success', 'Shift updated successfully.');
    }

    public function destroy(Shift $shift)
    {
        $shift->delete();
        return redirect()->back()->with('success', 'Shift deleted successfully.');
    }
}
