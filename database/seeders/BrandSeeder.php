<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $names = ['Apple', 'Samsung', 'Xiaomi', 'Huawei', 'Lenovo', 'Dell', 'HP', 'Sony', 'Asus', 'Acer', 'LG', 'Nike', 'Adidas', 'Puma', 'Reebok'];
        foreach ($names as $n) {
            Brand::firstOrCreate(['slug' => Str::slug($n)], ['name' => $n, 'active' => true]);
        }
    }
}
