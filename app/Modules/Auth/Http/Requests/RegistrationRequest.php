<?php

namespace App\Modules\Auth\Http\Requests;

use App\Models\User;
use App\Modules\Main\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name'           => ['required', 'min:2', 'max:128', new NameRule],
            'email'          => [
                'required',
                'email:rfc,dns',
                'max:100',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                // function($attribute, $value, $fail) {
                //     $exists = User::leftJoin('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                //         ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                //         ->whereNotIn('roles.name', ['User'])
                //         ->where("email", $value)
                //         ->exists();

                //     if ($exists) {
                //         $fail("The {$attribute} has already been taken.");
                //     }
                // }
            ],
            'mobile'         => ['required', 'numeric', 'min_digits:11', 'max_digits:14', 'regex:/^(\+88|88)?01[3-9]{1}\d{8}$/'],
            'agree'          => 'required|in:1',
        ];
    }

    public function attributes()
    {
        return [
            'mobile' => 'mobile number',
        ];
    }
} 
