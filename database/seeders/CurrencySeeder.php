<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run()
    {
        // Clear existing currencies
        Currency::truncate();

        // Create sample currencies
        Currency::create([
            'name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'exchange_rate' => 1.00,
            'is_default' => true,
            'is_active' => true,
        ]);

        Currency::create([
            'name' => 'Euro',
            'code' => 'EUR',
            'symbol' => '€',
            'exchange_rate' => 0.85,
            'is_default' => false,
            'is_active' => true,
        ]);

        Currency::create([
            'name' => 'Saudi Riyal',
            'code' => 'SAR',
            'symbol' => 'ر.س',
            'exchange_rate' => 3.75,
            'is_default' => false,
            'is_active' => true,
        ]);

        Currency::create([
            'name' => 'UAE Dirham',
            'code' => 'AED',
            'symbol' => 'د.إ',
            'exchange_rate' => 3.67,
            'is_default' => false,
            'is_active' => true,
        ]);
    }
}
