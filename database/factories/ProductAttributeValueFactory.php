<?php

namespace Database\Factories;

use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductAttributeValueFactory extends Factory
{
    protected $model = ProductAttributeValue::class;

    public function definition(): array
    {
        $val = $this->faker->unique()->word();

        return [
            'product_attribute_id' => ProductAttribute::factory(),
            'value' => ucfirst($val),
            'slug' => Str::slug($val . '-' . uniqid()),
        ];
    }
}
