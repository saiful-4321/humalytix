<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Modules\HRM\Models\Certification;
use App\Modules\HRM\Models\Employee;

class CertificationController extends Controller
{
    public function index()
    {
        if (!auth()->user()->can('hrm.certifications.view')) {
            abort(403);
        }

        $certifications = Certification::with('employee')->orderBy('expiry_date', 'asc')->get();
        // Employees for dropdown
        $employees = Employee::active()->select('id', 'first_name', 'last_name', 'email')->get();

        return view('HRM::pages.certifications.index', compact('certifications', 'employees'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->can('hrm.certifications.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'name' => 'required|string|max:255',
            'issuing_organization' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'credential_id' => 'nullable|string',
            'credential_url' => 'nullable|url',
        ]);

        $validated['created_by'] = auth()->id();
        
        // Handle "does_never_expire" checkbox if present in request (though validated doesn't catch it strictly if separate)
        $validated['does_never_expire'] = $request->has('does_never_expire');

        Certification::create($validated);

        return redirect()->back()->with('success', 'Certification recorded successfully.');
    }

    public function destroy(Certification $certification)
    {
        if (!auth()->user()->can('hrm.certifications.delete')) {
            abort(403);
        }

        $certification->delete();

        return redirect()->back()->with('success', 'Certification deleted successfully.');
    }
}
