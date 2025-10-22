<?php

namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository
{
    public function create(array $data): Payment
    {
        $payment = new Payment($data);
        $payment->save();
        return $payment;
    }
}