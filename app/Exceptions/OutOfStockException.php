<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class OutOfStockException extends RuntimeException
{
    public function __construct(string $productName)
    {
        parent::__construct(__('errors.out_of_stock', ['name' => $productName]));
    }
}
