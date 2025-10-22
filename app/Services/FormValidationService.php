<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;

class FormValidationService
{
    /**
     * Get validation rules based on form type
     */
    public function getValidationRules(string $formType): array
    {
        return match ($formType) {
            'user_form' => [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'nullable|string|max:20',
                'role' => 'required|in:admin,vendor,user',
                'balance' => 'nullable|numeric|min:0'
            ],
            'product_form' => [
                'name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'description' => 'nullable|string',
                'category_id' => 'required|exists:product_categories,id'
            ],
            'settings_form' => [
                'site_name' => 'required|string|max:255',
                'site_email' => 'required|email|max:255',
                'currency' => 'required|string|max:3'
            ],
            default => []
        };
    }

    /**
     * Get auto-save validation rules (more lenient)
     */
    public function getAutoSaveRules(string $formType): array
    {
        return match ($formType) {
            'user_form' => [
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'role' => 'nullable|in:admin,vendor,user',
                'balance' => 'nullable|numeric|min:0'
            ],
            'product_form' => [
                'name' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
                'description' => 'nullable|string',
                'category_id' => 'nullable|exists:product_categories,id'
            ],
            'settings_form' => [
                'site_name' => 'nullable|string|max:255',
                'site_email' => 'nullable|email|max:255',
                'currency' => 'nullable|string|max:3'
            ],
            default => []
        };
    }

    /**
     * Get validation messages
     */
    public function getValidationMessages(string $formType): array
    {
        return [
            'name.required' => __('Name is required'),
            'email.required' => __('Email is required'),
            'email.email' => __('Email must be valid'),
            'role.required' => __('Role is required'),
            'balance.numeric' => __('Balance must be a number'),
        ];
    }

    /**
     * Validate form data
     */
    public function validateForm(array $data, string $formType): array
    {
        $rules = $this->getValidationRules($formType);
        $messages = $this->getValidationMessages($formType);

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ];
        }

        return [
            'success' => true,
            'data' => $data
        ];
    }

    /**
     * Validate for auto-save
     */
    public function validateForAutoSave(array $data, string $formType): array
    {
        $rules = $this->getAutoSaveRules($formType);
        $messages = $this->getValidationMessages($formType);

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return [
                'success' => false,
                'errors' => $validator->errors()->toArray()
            ];
        }

        return [
            'success' => true,
            'data' => $data
        ];
    }
}
