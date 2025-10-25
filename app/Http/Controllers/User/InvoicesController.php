<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Helpers\GlobalHelper;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class InvoicesController extends Controller
{
    public function index()
    {
        $payments = Payment::where('user_id', Auth::id())->latest()->paginate(15);

        $currencyContext = GlobalHelper::getCurrencyContext();
        $currentCurrency = $currencyContext['currentCurrency'];
        $defaultCurrency = $currencyContext['defaultCurrency'];
        $currencySymbol = $currencyContext['currencySymbol'];

        // Convert payment amounts to current currency
        foreach ($payments as $payment) {
            $payment->display_amount = GlobalHelper::convertCurrency($payment->amount, $defaultCurrency, $currentCurrency, 2);
        }

        return view('front.account.invoices', compact('payments', 'currencySymbol'));
    }
}
