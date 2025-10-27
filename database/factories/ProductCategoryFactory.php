<?php

namespace Database\Factories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    protected $model = ProductCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => str_replace(' ', '-', $name).'-'.uniqid(),
            'description' => $this->faker->sentence(),
            'active' => true,
            'position' => 1,
        ];
    }
}
