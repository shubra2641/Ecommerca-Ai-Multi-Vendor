<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductCategory;
use App\Models\ProductTag;
use App\Models\ProductVariation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ProductDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing (optional)
        // ProductVariation::truncate(); Product::truncate(); ProductTag::truncate();
        // ProductAttributeValue::truncate(); ProductAttribute::truncate(); ProductCategory::truncate();

        // Categories (parent + children)
        $electronics = ProductCategory::firstOrCreate(['slug' => 'electronics'], [
            'name' => 'Electronics',
            'active' => 1,
            'position' => 1,
        ]);
        $smartphones = ProductCategory::firstOrCreate(['slug' => 'smartphones'], [
            'name' => 'Smartphones',
            'parent_id' => $electronics->id,
            'active' => 1,
            'position' => 2,
        ]);
        $laptops = ProductCategory::firstOrCreate(['slug' => 'laptops'], [
            'name' => 'Laptops',
            'parent_id' => $electronics->id,
            'active' => 1,
            'position' => 3,
        ]);
        $fashion = ProductCategory::firstOrCreate(['slug' => 'fashion'], [
            'name' => 'Fashion',
            'active' => 1,
            'position' => 4,
        ]);
        $men = ProductCategory::firstOrCreate(['slug' => 'men-fashion'], [
            'name' => 'Men',
            'parent_id' => $fashion->id,
            'active' => 1,
            'position' => 5,
        ]);
        $women = ProductCategory::firstOrCreate(['slug' => 'women-fashion'], [
            'name' => 'Women',
            'parent_id' => $fashion->id,
            'active' => 1,
            'position' => 6,
        ]);

        // Tags
        $tagFeatured = ProductTag::firstOrCreate(['slug' => 'featured'], [
            'name' => 'Featured',
        ]);
        $tagNew = ProductTag::firstOrCreate(['slug' => 'new'], [
            'name' => 'New',
        ]);
        $tagOffer = ProductTag::firstOrCreate(['slug' => 'offer'], [
            'name' => 'Offer',
        ]);
        $tagTech = ProductTag::firstOrCreate(['slug' => 'tech'], [
            'name' => 'Tech',
        ]);
        $tagWear = ProductTag::firstOrCreate(['slug' => 'wear'], [
            'name' => 'Wear',
        ]);

        // Attributes
        $color = ProductAttribute::firstOrCreate(['slug' => 'color'], ['name' => 'Color']);
        foreach (['Black', 'White', 'Blue', 'Red'] as $c) {
            ProductAttributeValue::firstOrCreate(['slug' => Str::slug($c)], ['product_attribute_id' => $color->id, 'value' => $c]);
        }
        $size = ProductAttribute::firstOrCreate(['slug' => 'size'], ['name' => 'Size']);
        foreach (['S', 'M', 'L', 'XL'] as $s) {
            ProductAttributeValue::firstOrCreate(['slug' => Str::slug($s)], ['product_attribute_id' => $size->id, 'value' => $s]);
        }
        $storage = ProductAttribute::firstOrCreate(['slug' => 'storage'], ['name' => 'Storage']);
        foreach (['64GB', '128GB', '256GB'] as $st) {
            ProductAttributeValue::firstOrCreate(['slug' => Str::slug($st)], ['product_attribute_id' => $storage->id, 'value' => $st]);
        }

        // Helper
        $now = Carbon::now();
        $future = $now->copy()->addDays(7);

        // Product 1: Simple smartphone on sale
        $p1 = Product::firstOrCreate(['slug' => 'nova-lite-5'], [
            'product_category_id' => $smartphones->id,
            'type' => 'simple',
            'physical_type' => 'physical',
            'sku' => 'PHONE-001',
            'name' => 'Nova Lite 5',
            'short_description' => 'Affordable smartphone with great battery',
            'description' => 'Full description of Nova Lite 5 with specs...',
            'price' => 299.00,
            'sale_price' => 249.00,
            'sale_start' => $now->copy()->subDay(),
            'sale_end' => $future,
            'manage_stock' => 1,
            'stock_qty' => 120,
            'reserved_qty' => 0,
            'backorder' => 0,
            'is_featured' => 1,
            'is_best_seller' => 1,
            'active' => 1,
        ]);
        $p1->tags()->sync([$tagFeatured->id, $tagOffer->id, $tagTech->id]);

        // Product 2: Variable smartphone (storage variants)
        $p2 = Product::firstOrCreate(['slug' => 'quantum-x'], [
            'product_category_id' => $smartphones->id,
            'type' => 'variable',
            'physical_type' => 'physical',
            'sku' => 'PHONE-002',
            'name' => 'Quantum X',
            'short_description' => 'Flagship performance, multiple storage options',
            'description' => 'Quantum X flagship details...',
            'price' => 699.00,
            'manage_stock' => 0,
            'is_featured' => 1,
            'active' => 1,
        ]);
        $p2->tags()->sync([$tagFeatured->id, $tagTech->id, $tagNew->id]);
        if ($p2->variations()->count() == 0) {
            $storageOptions = [
                ['64GB', 699],
                ['128GB', 749],
                ['256GB', 799],
            ];
            foreach ($storageOptions as [$stLabel, $price]) {
                $p2->variations()->create([
                    'sku' => 'QX-'.Str::slug($stLabel),
                    'price' => $price,
                    'attribute_data' => ['storage' => $stLabel],
                    'manage_stock' => 1,
                    'stock_qty' => 50,
                    'reserved_qty' => 0,
                    'active' => 1,
                ]);
            }
        }

        // Product 3: Simple Laptop no sale
        $p3 = Product::firstOrCreate(['slug' => 'aero-book-pro'], [
            'product_category_id' => $laptops->id,
            'type' => 'simple',
            'physical_type' => 'physical',
            'sku' => 'LAP-100',
            'name' => 'Aero Book Pro',
            'short_description' => 'Lightweight productivity ultrabook',
            'description' => 'Aero Book Pro full description...',
            'price' => 1099.00,
            'manage_stock' => 1,
            'stock_qty' => 40,
            'reserved_qty' => 0,
            'is_featured' => 0,
            'is_best_seller' => 0,
            'active' => 1,
        ]);
        $p3->tags()->sync([$tagTech->id]);

        // Product 4: Variable T-Shirt (color + size) with some sale
        $p4 = Product::firstOrCreate(['slug' => 'classic-tee'], [
            'product_category_id' => $men->id,
            'type' => 'variable',
            'physical_type' => 'physical',
            'sku' => 'TSHIRT-CL',
            'name' => 'Classic Tee',
            'short_description' => 'Soft cotton classic men tee',
            'description' => 'Classic Tee long description with material & care...',
            'price' => 25.00,
            'manage_stock' => 0,
            'is_featured' => 1,
            'active' => 1,
        ]);
        $p4->tags()->sync([$tagWear->id, $tagOffer->id]);
        if ($p4->variations()->count() == 0) {
            $colors = ['Black', 'White'];
            $sizes = ['S', 'M', 'L'];
            foreach ($colors as $col) {
                foreach ($sizes as $sz) {
                    $p4->variations()->create([
                        'sku' => 'CT-'.Str::substr($col, 0, 1).$sz,
                        'price' => 25.00,
                        'sale_price' => ($col === 'Black' && $sz === 'M')
                            ? 20.00
                            : null,
                        'sale_start' => ($col === 'Black' && $sz === 'M')
                            ? $now->copy()->subDay()
                            : null,
                        'sale_end' => ($col === 'Black' && $sz === 'M')
                            ? $future
                            : null,
                        'attribute_data' => ['color' => $col, 'size' => $sz],
                        'manage_stock' => 1,
                        'stock_qty' => 30,
                        'reserved_qty' => 0,
                        'active' => 1,
                    ]);
                }
            }
        }

        // Product 5: Digital product (eBook)
        $p5 = Product::firstOrCreate(['slug' => 'modern-laravel-guide'], [
            'product_category_id' => $women->id,
            'type' => 'simple',
            'physical_type' => 'digital',
            'sku' => 'EBOOK-MLG',
            'name' => 'Modern Laravel Guide',
            'short_description' => 'Comprehensive eBook on modern Laravel patterns',
            'description' => 'Chapters, examples, best practices...',
            'price' => 39.00,
            'sale_price' => 29.00,
            'sale_start' => $now->subDays(2),
            'sale_end' => $future,
            'manage_stock' => 0,
            'is_featured' => 1,
            'active' => 1,
        ]);
        $p5->tags()->sync([$tagNew->id, $tagOffer->id]);

        // Product 6: Simple fashion item with future sale not active yet
        $p6 = Product::firstOrCreate(['slug' => 'silk-scarf-lux'], [
            'product_category_id' => $women->id,
            'type' => 'simple',
            'physical_type' => 'physical',
            'sku' => 'SCARF-LUX',
            'name' => 'Silk Scarf Lux',
            'short_description' => 'Premium silk scarf with artistic pattern',
            'description' => 'Fabric details, care instructions...',
            'price' => 59.00,
            'sale_price' => 49.00,
            'sale_start' => $future,
            'sale_end' => $future->copy()->addDays(5),
            'manage_stock' => 1,
            'stock_qty' => 80,
            'reserved_qty' => 0,
            'active' => 1,
        ]);
        $p6->tags()->sync([$tagWear->id]);
    }
}
