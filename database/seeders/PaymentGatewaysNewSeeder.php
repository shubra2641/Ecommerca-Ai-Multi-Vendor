<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaysNewSeeder extends Seeder
{
    public function run(): void
    {
        // Stripe (disabled by default until keys filled in admin)
        PaymentGateway::updateOrCreate(['slug' => 'stripe'], [
            'name' => 'Stripe',
            'driver' => 'stripe',
            'enabled' => false,
            // store keys in config JSON to match current schema
            'config' => [
                'stripe' => [
                    'publishable_key' => null,
                    'secret_key' => null,
                    'webhook_secret' => null,
                    'mode' => 'test',
                ],
            ],
        ]);

        // Offline (enabled)
        PaymentGateway::updateOrCreate(['slug' => 'bank-transfer'], [
            'name' => 'Bank Transfer',
            'driver' => 'offline',
            'enabled' => true,
            'requires_transfer_image' => true,
            'transfer_instructions' => '<p> Bank Transfer .</p>',
        ]);
    }
}
