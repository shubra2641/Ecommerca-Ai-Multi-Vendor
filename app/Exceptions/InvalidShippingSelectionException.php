<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class InvalidShippingSelectionException extends RuntimeException
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ? $message : __('errors.invalid_shipping'));
    }
}
