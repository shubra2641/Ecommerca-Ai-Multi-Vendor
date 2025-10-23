<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class InvoicesController extends Controller
{
    public function index()
    {
        $payments = Payment::where('user_id', Auth::id())->latest()->paginate(15);

        return view('front.account.invoices', compact('payments'));
    }
}
