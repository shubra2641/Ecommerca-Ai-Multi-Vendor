<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PaymentGatewaySeeder extends Seeder
{
    public function run()
    {
        // Default Offline Payment Gateway
        $offline = [
            'name' => 'Bank Transfer',
            'driver' => 'offline',
            'enabled' => true,
            'requires_transfer_image' => true,
            'transfer_instructions' => 'Please transfer the amount to our bank account and upload the transfer receipt.',
            'config' => [],
        ];

        if (Schema::hasColumn('payment_gateways', 'fees')) {
            $offline['fees'] = ['fixed' => 0, 'percentage' => 0];
        }
        if (Schema::hasColumn('payment_gateways', 'supported_currencies')) {
            $offline['supported_currencies'] = ['USD', 'EGP', 'SAR', 'AED'];
        }
        if (Schema::hasColumn('payment_gateways', 'supported_methods')) {
            $offline['supported_methods'] = ['bank_transfer'];
        }
        if (Schema::hasColumn('payment_gateways', 'maintenance_mode')) {
            $offline['maintenance_mode'] = false;
        }

        PaymentGateway::updateOrCreate(['slug' => 'offline'], $offline);

        // Default Stripe Payment Gateway (disabled by default)
        $stripe = [
            'name' => 'Stripe',
            'driver' => 'stripe',
            'enabled' => false,
            'requires_transfer_image' => false,
            'transfer_instructions' => null,
            'config' => [
                'stripe_publishable_key' => env('STRIPE_KEY', ''),
                'stripe_secret_key' => env('STRIPE_SECRET', ''),
                'stripe_webhook_secret' => env('STRIPE_WEBHOOK_SECRET', ''),
                'stripe_mode' => 'test',
            ],
            'sandbox_mode' => true,
        ];

        if (Schema::hasColumn('payment_gateways', 'fees')) {
            $stripe['fees'] = ['fixed' => 0, 'percentage' => 2.9];
        }
        if (Schema::hasColumn('payment_gateways', 'supported_currencies')) {
            $stripe['supported_currencies'] = ['USD', 'EUR', 'GBP', 'EGP'];
        }
        if (Schema::hasColumn('payment_gateways', 'supported_methods')) {
            $stripe['supported_methods'] = ['card'];
        }
        if (Schema::hasColumn('payment_gateways', 'maintenance_mode')) {
            $stripe['maintenance_mode'] = false;
        }

        PaymentGateway::updateOrCreate(['slug' => 'stripe'], $stripe);

        // Default PayMob Gateway (disabled by default)
        $paymob = [
            'name' => 'PayMob',
            'driver' => 'paymob',
            'enabled' => false,
            'requires_transfer_image' => false,
            'transfer_instructions' => null,
            'config' => [],
            'sandbox_mode' => true,
            'api_key' => null,
            'secret_key' => null,
            'merchant_id' => null,
            'webhook_secret' => null,
        ];

        if (Schema::hasColumn('payment_gateways', 'fees')) {
            $paymob['fees'] = ['fixed' => 0, 'percentage' => 2.5];
        }
        if (Schema::hasColumn('payment_gateways', 'supported_currencies')) {
            $paymob['supported_currencies'] = ['EGP'];
        }
        if (Schema::hasColumn('payment_gateways', 'supported_methods')) {
            $paymob['supported_methods'] = ['card', 'wallet'];
        }
        if (Schema::hasColumn('payment_gateways', 'maintenance_mode')) {
            $paymob['maintenance_mode'] = false;
        }

        PaymentGateway::updateOrCreate(['slug' => 'paymob'], $paymob);

        // Default Fawry Gateway (disabled by default)
        $fawry = [
            'name' => 'Fawry',
            'driver' => 'fawry',
            'enabled' => false,
            'requires_transfer_image' => false,
            'transfer_instructions' => null,
            'config' => [],
            'sandbox_mode' => true,
            'api_key' => null,
            'secret_key' => null,
            'merchant_id' => null,
            'webhook_secret' => null,
        ];

        if (Schema::hasColumn('payment_gateways', 'fees')) {
            $fawry['fees'] = ['fixed' => 2.0, 'percentage' => 0];
        }
        if (Schema::hasColumn('payment_gateways', 'supported_currencies')) {
            $fawry['supported_currencies'] = ['EGP'];
        }
        if (Schema::hasColumn('payment_gateways', 'supported_methods')) {
            $fawry['supported_methods'] = ['fawry_pay'];
        }
        if (Schema::hasColumn('payment_gateways', 'maintenance_mode')) {
            $fawry['maintenance_mode'] = false;
        }

        PaymentGateway::updateOrCreate(['slug' => 'fawry'], $fawry);

        // Default MyFatoorah Gateway (disabled by default)
        $myfatoorah = [
            'name' => 'MyFatoorah',
            'driver' => 'myfatoorah',
            'enabled' => false,
            'requires_transfer_image' => false,
            'transfer_instructions' => null,
            'config' => [],
            'sandbox_mode' => true,
            'api_key' => null,
            'secret_key' => null,
            'merchant_id' => null,
            'webhook_secret' => null,
        ];

        if (Schema::hasColumn('payment_gateways', 'fees')) {
            $myfatoorah['fees'] = ['fixed' => 0, 'percentage' => 2.75];
        }
        if (Schema::hasColumn('payment_gateways', 'supported_currencies')) {
            $myfatoorah['supported_currencies'] = ['KWD', 'SAR', 'AED', 'BHD', 'QAR'];
        }
        if (Schema::hasColumn('payment_gateways', 'supported_methods')) {
            $myfatoorah['supported_methods'] = ['card', 'knet', 'benefit'];
        }
        if (Schema::hasColumn('payment_gateways', 'maintenance_mode')) {
            $myfatoorah['maintenance_mode'] = false;
        }

        PaymentGateway::updateOrCreate(['slug' => 'myfatoorah'], $myfatoorah);
    }
}
