<?php

namespace App\Exceptions;

use RuntimeException;

class InvalidShippingSelectionException extends RuntimeException
{
    public function __construct(?string $message = null)
    {
        parent::__construct($message ?: __('errors.invalid_shipping'));
    }
}
