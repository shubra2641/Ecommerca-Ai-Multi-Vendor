<?php

declare(strict_types=1);

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class RemoveFromCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['nullable', 'integer'],
            'cart_key' => ['nullable', 'string'],
        ];
    }
}
