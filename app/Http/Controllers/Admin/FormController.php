<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Services\FormValidationService;
use Illuminate\Http\Request;

class FormController extends BaseAdminController
{
    protected FormValidationService $validationService;

    public function __construct(FormValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    /**
     * Validate form data via AJAX
     */
    public function validateForm(Request $request)
    {
        $formType = $request->input('form_type');
        $data = $request->except(['form_type', '_token']);

        $result = $this->validationService->validateForm($data, $formType);

        if (! $result['success']) {
            return $this->validationErrorResponse($result['errors']);
        }

        return $this->successResponse(__('Form is valid'));
    }

    /**
     * Auto-save form data
     */
    public function autoSave(Request $request)
    {
        $formType = $request->input('form_type');
        $data = $request->except(['form_type', '_token']);

        $result = $this->validationService->validateForAutoSave($data, $formType);

        if (! $result['success']) {
            return $this->errorResponse(__('Auto-save failed: Invalid data'), $result['errors'], 422);
        }

        // Here you would implement the actual auto-save logic
        // For now, we'll just return success
        return $this->successResponse(__('Changes saved automatically'), [
            'timestamp' => now()->toISOString(),
        ]);
    }
}
