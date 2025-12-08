<?php

namespace App\Modules\Main\Http\Requests;

use App\Modules\Main\Enums\ActiveInactiveEnum;
use App\Modules\Main\Models\UserWhiteListedIp;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserWhitelistedIpRequest extends FormRequest
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
            'user_id'     => 'sometimes|exists:users,id',
            'ip_address'  => [
                'sometimes',
                'ip', 
                function ($attr, $val, $fail) {
                    $exists = UserWhiteListedIp::where(function($query) use($val) {
                        return $query->where('user_id', $this->user_id)
                            ->where('ip_address', trim($val));
                    })
                    ->exists();

                    if ($exists) {
                        return $fail("The IP Address {$val} has already been taken by the current user!");
                    }
                }
            ],
            'status'      => 'nullable|in:'. implode(",", ActiveInactiveEnum::getValues()),
        ]; 
    }
}
