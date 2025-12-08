<?php

namespace App\Modules\Main\Http\Requests;

use App\Modules\Main\Enums\UserStatusEnum;
use App\Modules\Main\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [  
            'name'          => ['required', 'max:64', new NameRule],
            'email'         => ['required', 'email:rfc,dns', 'max:100', 'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', 'unique:users,email,'.$this->id], 
            'password'      => 'nullable|string|confirmed|min:5',
            'mobile'        => ['required', 'min_digits:11', 'max_digits:14', 'regex:/^(\+88|88)?01[3-9]{1}\d{8}$/'], 
            'nid'           => ['nullable', 'digits_between:10,20'],
            'date_of_birth' => 'nullable|date_format:Y-m-d',
            'roles'         => 'sometimes',
            'status'        => 'sometimes|in:'. implode(",", UserStatusEnum::getValues()),
        ]; 
    }

    public function attributes()
    {
        return [
            'mobile' => 'mobile number',
        ];
    }

}
