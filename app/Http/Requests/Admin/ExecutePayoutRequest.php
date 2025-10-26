<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ExecutePayoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('payout')->status === 'pending';
    }

    public function rules(): array
    {
        return [
            'admin_note' => 'nullable|string|max:1000',
            'proof' => 'nullable|image|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'admin_note.max' => 'Admin note must not exceed 1000 characters.',
            'proof.image' => 'Proof must be an image.',
            'proof.max' => 'Proof must not exceed 5MB.',
        ];
    }
}
