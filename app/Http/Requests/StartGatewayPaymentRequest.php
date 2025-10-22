<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartGatewayPaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'gateway' => 'nullable|string',
        ];
    }
}
