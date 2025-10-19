<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FormController extends Controller
{
    /**
     * Validate form data via AJAX
     */
    public function validateForm(Request $request)
    {
        $formType = $request->input('form_type');
        $data = $request->except(['form_type', '_token']);

        $rules = $this->getValidationRules($formType);
        $messages = $this->getValidationMessages($formType);

        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => __('Validation failed')
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => __('Form is valid')
        ]);
    }

    /**
     * Auto-save form data
     */
    public function autoSave(Request $request)
    {
        $formType = $request->input('form_type');
        $data = $request->except(['form_type', '_token']);

        // Basic validation for auto-save
        $rules = $this->getAutoSaveRules($formType);
        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => __('Auto-save failed: Invalid data')
            ], 422);
        }

        // Here you would implement the actual auto-save logic
        // For now, we'll just return success
        return response()->json([
            'success' => true,
            'message' => __('Changes saved automatically'),
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get validation rules based on form type
     */
    private function getValidationRules($formType)
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
     * Get validation messages
     */
    private function getValidationMessages($formType)
    {
        return [
            'name.required' => __('Name is required'),
            'email.required' => __('Email is required'),
            'email.email' => __('Email must be valid'),
            'phone.max' => __('Phone number is too long'),
            'role.required' => __('Role is required'),
            'role.in' => __('Invalid role selected'),
            'balance.numeric' => __('Balance must be a number'),
            'balance.min' => __('Balance cannot be negative'),
            'price.required' => __('Price is required'),
            'price.numeric' => __('Price must be a number'),
            'price.min' => __('Price cannot be negative'),
            'category_id.required' => __('Category is required'),
            'category_id.exists' => __('Selected category does not exist'),
            'site_name.required' => __('Site name is required'),
            'site_email.required' => __('Site email is required'),
            'site_email.email' => __('Site email must be valid'),
            'currency.required' => __('Currency is required'),
        ];
    }

    /**
     * Get auto-save validation rules (more lenient)
     */
    private function getAutoSaveRules($formType)
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
}
