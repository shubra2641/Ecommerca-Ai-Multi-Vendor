<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove existing default gateways to avoid duplicates
        DB::table('payment_gateways')->whereIn('slug', ['stripe', 'offline', 'cod', 'paytabs', 'tap', 'weaccept', 'paypal', 'payeer'])->delete();

        $gateways = [
            [
                'name' => 'Stripe',
                'slug' => 'stripe',
                'driver' => 'stripe',
                'enabled' => false,
                'config' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Offline / Bank Transfer',
                'slug' => 'offline',
                'driver' => 'offline',
                'enabled' => false,
                'config' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cash on Delivery',
                'slug' => 'cod',
                'driver' => 'cod',
                'enabled' => false,
                'config' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PayTabs',
                'slug' => 'paytabs',
                'driver' => 'paytabs',
                'enabled' => false,
                'config' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tap',
                'slug' => 'tap',
                'driver' => 'tap',
                'enabled' => false,
                'config' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'WeAccept',
                'slug' => 'weaccept',
                'driver' => 'weaccept',
                'enabled' => false,
                'config' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PayPal',
                'slug' => 'paypal',
                'driver' => 'paypal',
                'enabled' => false,
                'config' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Payeer',
                'slug' => 'payeer',
                'driver' => 'payeer',
                'enabled' => false,
                'config' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('payment_gateways')->insert($gateways);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally remove the default gateways
        DB::table('payment_gateways')->whereIn('slug', ['stripe', 'offline', 'cod', 'paytabs', 'tap', 'weaccept', 'paypal', 'payeer'])->delete();
    }
};
