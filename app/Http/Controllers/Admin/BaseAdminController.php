<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

abstract class BaseAdminController extends Controller
{
    /**
     * Standard JSON response format
     */
    protected function jsonResponse(bool $success, string $message, $data = null, int $status = 200): JsonResponse
    {
        $response = [
            'success' => $success,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Success response
     */
    protected function successResponse(string $message, $data = null, int $status = 200): JsonResponse
    {
        return $this->jsonResponse(true, $message, $data, $status);
    }

    /**
     * Error response
     */
    protected function errorResponse(string $message, $data = null, int $status = 400): JsonResponse
    {
        return $this->jsonResponse(false, $message, $data, $status);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse($errors, ?string $message = null): JsonResponse
    {
        return $this->jsonResponse(
            false,
            $message ?? __('Validation failed'),
            ['errors' => $errors],
            422
        );
    }

    /**
     * Validate request data
     */
    protected function validateData(array $data, array $rules, array $messages = []): ?array
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }

        return null;
    }

    /**
     * Get current user safely
     */
    protected function getCurrentUser(Request $request)
    {
        return $request->user();
    }

    /**
     * Format currency amount
     */
    protected function formatCurrency(float $amount, string $symbol = '$'): string
    {
        return number_format($amount, 2).' '.$symbol;
    }

    /**
     * Get pagination parameters
     */
    protected function getPaginationParams(Request $request): array
    {
        return [
            'per_page' => $request->get('per_page', 15),
            'page' => $request->get('page', 1),
        ];
    }
}
