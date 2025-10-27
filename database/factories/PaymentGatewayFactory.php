<?php

namespace Database\Factories;

use App\Models\PaymentGateway;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class PaymentGatewayFactory extends Factory
{
    protected $model = PaymentGateway::class;

    public function definition(): array
    {
        $slug = Str::slug($this->faker->unique()->word().'_gw');

        $data = [
            'name' => ucfirst($slug),
            'slug' => $slug,
            'driver' => 'offline',
            'enabled' => true,
            'requires_transfer_image' => false,
            'transfer_instructions' => 'Pay offline.',
            'config' => [
                'api_key' => 'test_api_key',
                'secret_key' => 'test_secret_key',
                'sandbox_mode' => true,
            ],
        ];

        // Some test DBs (sqlite) or migration orders may not have the supported_currencies column.
        if (Schema::hasColumn('payment_gateways', 'supported_currencies')) {
            $data['supported_currencies'] = ['USD', 'EGP'];
        }

        return $data;
    }
}
