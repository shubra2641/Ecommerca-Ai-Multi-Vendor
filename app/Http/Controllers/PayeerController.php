<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class PayeerController extends Controller
{
    public function callback()
    {
        return response('OK');
    }
}
