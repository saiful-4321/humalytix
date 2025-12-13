<?php

namespace App\Modules\HRM\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\HRM\Models\DocumentType;
use App\Modules\HRM\Http\Requests\DocumentTypeRequest;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:hrm.settings.view')->only(['index']);
        $this->middleware('permission:hrm.settings.create')->only(['create', 'store']);
        $this->middleware('permission:hrm.settings.edit')->only(['edit', 'update']);
        $this->middleware('permission:hrm.settings.delete')->only(['destroy']);
    }

    public function index()
    {
        $documentTypes = DocumentType::orderBy('category')->orderBy('order')->paginate(15);
        return view('HRM::settings.document_types.index', compact('documentTypes'));
    }

    public function create()
    {
        return view('HRM::settings.document_types.create');
    }

    public function store(DocumentTypeRequest $request)
    {
        DocumentType::create($request->validated());

        return redirect()->route('hrm.document-types.index')
            ->with('success', 'Document Type created successfully.');
    }

    public function edit(DocumentType $documentType)
    {
        return view('HRM::settings.document_types.edit', compact('documentType'));
    }

    public function update(DocumentTypeRequest $request, DocumentType $documentType)
    {
        $documentType->update($request->validated());

        return redirect()->route('hrm.document-types.index')
            ->with('success', 'Document Type updated successfully.');
    }

    public function destroy(DocumentType $documentType)
    {
        $documentType->delete();

        return redirect()->route('hrm.document-types.index')
            ->with('success', 'Document Type deleted successfully.');
    }
}
