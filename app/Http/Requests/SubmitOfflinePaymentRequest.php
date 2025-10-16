<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitOfflinePaymentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'transfer_image' => 'nullable|file|image|max:5120',
        ];
    }
}
