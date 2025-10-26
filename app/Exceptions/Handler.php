<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e): void {
            $e; // unused
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            $status = 500;
            $code = class_basename($e);
            $message = $e->getMessage() ? $e->getMessage() : __('errors.unexpected');
            if ($e instanceof OutOfStockException) {
                $status = 422;
            } elseif ($e instanceof InvalidShippingSelectionException) {
                $status = 422;
            } elseif (method_exists($e, 'getStatusCode')) {
                $status = $e->getStatusCode();
            }

            return response()->json([
                'ok' => false,
                'error' => [
                    'message' => $message,
                    'type' => $code,
                    'code' => $status,
                ],
            ], $status);
        }

        return parent::render($request, $e);
    }
}
