<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductVariationFactory extends Factory
{
    protected $model = ProductVariation::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'sku' => strtoupper(Str::random(10)),
            'price' => $this->faker->randomFloat(2, 5, 300),
            'sale_price' => null,
            'sale_start' => null,
            'sale_end' => null,
            'manage_stock' => true,
            'stock_qty' => $this->faker->numberBetween(1, 50),
            'reserved_qty' => 0,
            'backorder' => false,
            'attribute_data' => ['size' => $this->faker->randomElement(['S', 'M', 'L'])],
            'attribute_hash' => null,
            'active' => true,
        ];
    }
}
