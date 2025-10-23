<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // You can add authorization logic here (policy/gate) if desired
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        // Centralize allowed statuses so controller stays small
        $allowed = ['pending', 'processing', 'completed', 'cancelled', 'on-hold', 'refunded'];

        return [
            'status' => ['required', 'string', 'max:50', 'in:' . implode(',', $allowed)],
            'note' => ['nullable', 'string', 'max:1000'],
            'tracking_number' => ['nullable', 'string', 'max:255'],
            'tracking_url' => ['nullable', 'url', 'max:1000'],
            'carrier' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => __('Invalid status'),
        ];
    }
}
