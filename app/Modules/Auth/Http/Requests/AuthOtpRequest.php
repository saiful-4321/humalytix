<?php

namespace App\Modules\Auth\Http\Requests;

use App\Models\User; 
use App\Modules\Main\Enums\UserStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class AuthOtpRequest extends FormRequest
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
            'otp'    => "required|array|max:6|min:6", 
            'email'  => ['required', 'email:rfc,dns', 'max:100', 'regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'], 
            'mobile' => [
                'required',
                'numeric',
                'min_digits:11',
                'max_digits:14',
                'regex:/^(\+88|88)?01[3-9]{1}\d{8}$/',
                function ($attr, $val, $fail) {
                    $exists = User::where(function($query) use($val) {
                        return $query
                            ->where('status', UserStatusEnum::Pending)
                            ->whereIn('mobile', msisdns($val));
                    })
                    ->exists();

                    if (!$exists) {
                        return $fail("Invalid user! Please contact with author!");
                    }
                }
            ]
        ]; 
    }

    public function attributes()
    {
        return [
            'mobile' => 'mobile number',
        ];
    }
}
