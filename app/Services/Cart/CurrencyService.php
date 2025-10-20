<?php

namespace App\Services\Cart;

use App\Models\Currency;
use Throwable;

class CurrencyService
{
    public function getCurrentCurrency(): Currency
    {
        $currencyId = session('currency_id');
        return $currencyId ? Currency::find($currencyId) : Currency::getDefault();
    }

    public function getDefaultCurrency(): Currency
    {
        return Currency::getDefault();
    }

    public function getCurrentCurrencySymbol(): string
    {
        $currentCurrency = $this->getCurrentCurrency();
        return $currentCurrency?->symbol ?? Currency::defaultSymbol();
    }

    public function convertToDisplayCurrency(float $amount): float
    {
        $currentCurrency = $this->getCurrentCurrency();
        $defaultCurrency = $this->getDefaultCurrency();

        if (!$this->shouldConvertCurrency($currentCurrency, $defaultCurrency)) {
            return $amount;
        }

        try {
            return $defaultCurrency->convertTo($amount, $currentCurrency, 2);
        } catch (Throwable $e) {
            return $amount;
        }
    }

    public function shouldConvertCurrency(Currency $currentCurrency, Currency $defaultCurrency): bool
    {
        return $currentCurrency 
            && $defaultCurrency 
            && $currentCurrency->id !== $defaultCurrency->id;
    }
}
