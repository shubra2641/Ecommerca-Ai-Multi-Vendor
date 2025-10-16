<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::updateOrCreate(['id' => 1], [
            'site_name' => 'Demo Shop',
            'logo' => null,
            'seo_description' => 'Demo store for testing and development',
            'custom_css' => null,
            'custom_js' => null,
        ]);
    }
}
