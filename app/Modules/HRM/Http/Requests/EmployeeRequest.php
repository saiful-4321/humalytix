<?php

namespace App\Modules\HRM\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employeeId = $this->route('employee') ? $this->route('employee')->id : null;

        return [
            // Personal Information
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:hrm_employees,email,' . $employeeId],
            'phone' => ['nullable', 'string', 'max:20'],
            'alternate_phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
            'blood_group' => ['nullable', 'string', 'max:10'],
            'marital_status' => ['nullable', 'in:single,married,divorced,widowed'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'religion' => ['nullable', 'string', 'max:100'],
            
            // KYC Information
            'nid_number' => ['nullable', 'string', 'max:50'],
            'passport_number' => ['nullable', 'string', 'max:50'],
            'tax_id' => ['nullable', 'string', 'max:50'],
            'driving_license' => ['nullable', 'string', 'max:50'],
            
            // Address
            'present_address' => ['nullable', 'string'],
            'permanent_address' => ['nullable', 'string'],
            
            // Emergency Contact
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_relationship' => ['nullable', 'string', 'max:100'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            
            // Employment Details
            'department_id' => ['required', 'exists:hrm_departments,id'],
            'branch_id' => ['required', 'exists:hrm_branches,id'],
            'business_unit_id' => ['nullable', 'exists:hrm_business_units,id'],
            'designation' => ['required', 'string', 'max:255'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,internship,consultant,temporary'],
            'joining_date' => ['required', 'date'],
            'confirmation_date' => ['nullable', 'date', 'after:joining_date'],
            'probation_end_date' => ['nullable', 'date', 'after:joining_date'],
            'reporting_to' => ['nullable', 'exists:hrm_employees,id'],
            
            // Salary Information
            'basic_salary' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_branch' => ['nullable', 'string', 'max:255'],
            
            // Status
            'status' => ['required', 'in:active,probation,confirmed,notice_period,resigned,terminated,suspended,on_leave'],
            'remarks' => ['nullable', 'string'],
            
            // Photo
            'photo' => ['nullable', 'image', 'max:5120'], // 5MB max
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email address is required',
            'email.email' => 'Please provide a valid email address',
            'email.unique' => 'This email is already registered',
            'department_id.required' => 'Department is required',
            'department_id.exists' => 'Selected department does not exist',
            'branch_id.required' => 'Branch is required',
            'branch_id.exists' => 'Selected branch does not exist',
            'designation.required' => 'Designation is required',
            'employment_type.required' => 'Employment type is required',
            'joining_date.required' => 'Joining date is required',
            'status.required' => 'Status is required',
            'photo.image' => 'Photo must be an image file',
            'photo.max' => 'Photo size should not exceed 5MB',
        ];
    }
}
