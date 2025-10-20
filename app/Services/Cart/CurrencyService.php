<?php

namespace App\Services\Cart;

use App\Models\Currency;

class CurrencyService
{
    public function getCurrentCurrency(): Currency
    {
        $currencyId = session('currency_id');
        return $currencyId ? Currency::find($currencyId) : Currency::getDefault();
    }

    public function getCurrentCurrencySymbol(): string
    {
        return $this->getCurrentCurrency()->symbol ?? Currency::defaultSymbol();
    }
}
