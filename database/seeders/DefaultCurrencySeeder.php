<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class DefaultCurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * This seeder will ensure a default USD currency exists.
     * It is created but not executed automatically by me — run it locally with:
     * php artisan db:seed --class=Database\\Seeders\\DefaultCurrencySeeder
     */
    public function run()
    {
        Currency::updateOrCreate(
            ['code' => 'USD'],
            [
                'name' => 'US Dollar',
                'symbol' => '$',
                'exchange_rate' => 1.00, // base rate
                'is_default' => true,
                'is_active' => true,
            ]
        );
    }
}
