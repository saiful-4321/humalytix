<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\LetterTemplate;
use App\Modules\HRM\Models\EmployeeLetter;
use App\Modules\HRM\Models\Employee;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LetterController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.employees.edit')->only(['index', 'create', 'store', 'show']);
    }

    public function index()
    {
        $templates = LetterTemplate::active()->get();
        $letters = EmployeeLetter::with(['employee', 'template'])->orderBy('created_at', 'desc')->paginate(20);
        $employees = Employee::active()->get();
        return view('HRM::pages.letters.index', compact('templates', 'letters', 'employees'));
    }

    public function create()
    {
        $employees = Employee::active()->get();
        $templates = LetterTemplate::active()->get();
        return view('HRM::pages.letters.create', compact('employees', 'templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:hrm_employees,id',
            'template_id' => 'required|exists:hrm_letter_templates,id',
            'issued_date' => 'required|date',
            'signatory_id' => 'required|exists:hrm_employees,id',
        ]);

        $employee = Employee::find($validated['employee_id']);
        $template = LetterTemplate::find($validated['template_id']);
        $signatory = Employee::find($validated['signatory_id']);

        // Replace variables
        $content = $template->content;
        $content = str_replace('{employee_name}', $employee->full_name, $content);
        $content = str_replace('{employee_code}', $employee->employee_code, $content);
        $content = str_replace('{designation}', $employee->designation, $content);
        $content = str_replace('{department}', $employee->department->name ?? '', $content);
        $content = str_replace('{date}', date('d M, Y', strtotime($validated['issued_date'])), $content);
        $content = str_replace('{signatory_name}', $signatory->full_name, $content);
        $content = str_replace('{company_name}', 'Your Company Name', $content); // Should come from settings

        EmployeeLetter::create([
            'employee_id' => $validated['employee_id'],
            'template_id' => $validated['template_id'],
            'type' => $template->type,
            'content' => $content,
            'issued_date' => $validated['issued_date'],
            'signatory_id' => $validated['signatory_id'],
            'status' => 'issued',
        ]);

        return redirect()->route('hrm.letters.index')->with('success', 'Letter issued successfully!');
    }

    public function show(EmployeeLetter $letter)
    {
        return view('HRM::pages.letters.show', compact('letter'));
    }

    public function download(EmployeeLetter $letter)
    {
        $pdf = Pdf::loadView('HRM::pages.letters.pdf', compact('letter'));
        return $pdf->download('Letter_' . $letter->employee->employee_code . '.pdf');
    }
}
