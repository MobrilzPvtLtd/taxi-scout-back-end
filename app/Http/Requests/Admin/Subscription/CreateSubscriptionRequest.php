<?php

namespace App\Http\Requests\Admin\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'company_id' => 'required',
            'package_name' => 'required',
            'number_of_drivers' => 'required',
            'amount' => 'required|integer',
            'validity' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'package_name.required' => 'package name is required'
        ];
    }
}
