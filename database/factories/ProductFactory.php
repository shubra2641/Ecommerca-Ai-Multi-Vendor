<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'product_category_id' => ProductCategory::factory(),
            'type' => 'simple',
            'physical_type' => 'physical',
            'sku' => strtoupper(Str::random(8)),
            'name' => ucfirst($name),
            'slug' => Str::slug($name . '-' . uniqid()),
            'short_description' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 10, 500),
            'sale_price' => null,
            'sale_start' => null,
            'sale_end' => null,
            'main_image' => null,
            'gallery' => [],
            'manage_stock' => true,
            'stock_qty' => $this->faker->numberBetween(5, 100),
            'reserved_qty' => 0,
            'backorder' => false,
            'is_featured' => false,
            'is_best_seller' => false,
            'seo_title' => null,
            'seo_description' => null,
            'seo_keywords' => null,
            'active' => true,
        ];
    }
}
