<?php

namespace Database\Factories;

use App\Models\ProductTag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductTagFactory extends Factory
{
    protected $model = ProductTag::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
        ];
    }
}
