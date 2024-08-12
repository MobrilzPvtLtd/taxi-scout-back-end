<?php

namespace App\Http\Requests\Auth\Password;

use App\Http\Requests\BaseRequest;

class ValidateResetTokenRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'otp' => 'required',
            // 'token' => 'required',
            'email' => 'required|email',
        ];
    }
}
