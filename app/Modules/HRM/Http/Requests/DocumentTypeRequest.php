<?php

namespace App\Modules\HRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DocumentTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:hrm_document_types,name,' . $this->route('document_type'),
            'category' => 'required|string|in:kyc,employment,education,certification,other',
            'is_required' => 'boolean',
            'urgency' => 'required|in:required,nice_to_have,not_required',
            'description' => 'nullable|string',
            'order' => 'integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
