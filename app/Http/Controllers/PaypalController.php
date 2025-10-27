<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;

class PaypalController extends Controller
{
    public function return(Payment $payment)
    {
        return app(PaymentService::class)->handlePayPalReturn($payment);
    }

    public function cancel(Payment $payment)
    {
        return app(PaymentService::class)->handlePayPalCancel($payment);
    }
}
