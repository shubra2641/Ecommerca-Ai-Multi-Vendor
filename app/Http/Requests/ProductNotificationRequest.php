<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductNotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
            'email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
