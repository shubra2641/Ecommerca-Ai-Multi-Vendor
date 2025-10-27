<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    protected $model = Currency::class;

    public function definition(): array
    {
        $code = $this->faker->unique()->currencyCode();

        return [
            'name' => $code,
            'code' => $code,
            'symbol' => '$',
            'exchange_rate' => 1,
            'is_default' => false,
            'is_active' => true,
        ];
    }
}
