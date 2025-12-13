<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\Employee;
use App\Modules\HRM\Models\EmployeeDocument;
use App\Modules\HRM\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.documents.upload')->only(['store']);
        $this->middleware('permission:hrm.documents.verify')->only(['verify']);
        $this->middleware('permission:hrm.documents.delete')->only(['destroy']);
    }

    /**
     * Upload a new document
     */
    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'document_type_id' => 'required|exists:hrm_document_types,id',
            'document_number' => 'nullable|string|max:100',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after:issue_date',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
            'notes' => 'nullable|string',
        ]);

        // Upload file
        if ($request->hasFile('file')) {
            $validated['file_path'] = $request->file('file')->store('employees/documents', 'public');
        }

        $validated['uploaded_by'] = auth()->id();

        $employee->documents()->create($validated);

        return redirect()
            ->route('hrm.employees.show', $employee)
            ->with('success', 'Document uploaded successfully!');
    }

    /**
     * Verify document
     */
    public function verify(Employee $employee, EmployeeDocument $document)
    {
        $document->update([
            'verification_status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return redirect()
            ->route('hrm.employees.show', $employee)
            ->with('success', 'Document verified successfully!');
    }

    /**
     * Delete document
     */
    public function destroy(Employee $employee, EmployeeDocument $document)
    {
        // Delete file from storage
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

        return redirect()
            ->back()
            ->with('success', 'Document deleted successfully!');
    }

    /**
     * Download document
     */
    public function download(Employee $employee, EmployeeDocument $document)
    {
        if (!$document->file_path || !Storage::disk('public')->exists($document->file_path)) {
            return redirect()
                ->back()
                ->with('error', 'Document file not found!');
        }

        return Storage::disk('public')->download($document->file_path);
    }
}
