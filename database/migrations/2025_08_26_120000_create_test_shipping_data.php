<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Insert test country if not exists
        if (! DB::table('countries')->where('name', 'Egypt')->exists()) {
            $countryId = DB::table('countries')->insertGetId([
                'name' => 'Egypt',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert test governorate
            $governorateId = DB::table('governorates')->insertGetId([
                'country_id' => $countryId,
                'name' => 'Cairo',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert test city
            $cityId = DB::table('cities')->insertGetId([
                'governorate_id' => $governorateId,
                'name' => 'Nasr City',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert test shipping zone
            $zoneId = DB::table('shipping_zones')->insertGetId([
                'name' => 'Cairo Zone',
                'code' => 'CAIRO',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insert test shipping rule
            DB::table('shipping_rules')->insert([
                'zone_id' => $zoneId,
                'country_id' => $countryId,
                'governorate_id' => $governorateId,
                'city_id' => $cityId,
                'price' => 25.00,
                'estimated_days' => 3,
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        DB::table('shipping_rules')->where('price', 25.00)->delete();
        DB::table('shipping_zones')->where('name', 'Cairo Zone')->delete();
        DB::table('cities')->where('name', 'Nasr City')->delete();
        DB::table('governorates')->where('name', 'Cairo')->delete();
        DB::table('countries')->where('name', 'Egypt')->delete();
    }
};
