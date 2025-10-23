<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AdjustBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->can('access-admin');
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999'],
            'note' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => __('Amount is required'),
            'amount.numeric' => __('Amount must be numeric'),
            'amount.min' => __('Amount must be greater than zero'),
            'note.max' => __('Note may not be greater than :max characters'),
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('amount')) {
            $this->merge([
                'amount' => (float) preg_replace('/[^0-9.]/', '', $this->input('amount')),
            ]);
        }
    }
}
