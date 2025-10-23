<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class PayeerController extends Controller
{
    public function callback()
    {
        // TODO: implement Payeer callback signature validation & update payment status
        return response('OK');
    }
}
