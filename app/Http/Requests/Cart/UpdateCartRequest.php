<?php

declare(strict_types=1);

namespace App\Http\Requests\Cart;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lines' => ['required', 'array'],
            'lines.*.cart_key' => ['required', 'string'],
            'lines.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }
}
