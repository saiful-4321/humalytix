<?php

namespace App\Modules\Main\Http\Requests;

use App\Modules\Main\Rules\NameRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProfileRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => ['required', 'max:64', new NameRule],
            'email'    => ['required', 'email:rfc,dns', 'max:100', 'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', 'unique:users,email,'.auth()->user()->id], 
            'password' => 'required|string|confirmed|min:5',
        ];
    }
}
