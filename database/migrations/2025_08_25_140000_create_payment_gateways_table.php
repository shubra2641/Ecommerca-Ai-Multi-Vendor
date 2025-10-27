<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('payment_gateways')) {
            Schema::create('payment_gateways', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('driver'); // stripe, offline, future: encrypted
                $table->boolean('enabled')->default(false);
                // Offline specific
                $table->boolean('requires_transfer_image')->default(false);
                $table->text('transfer_instructions')->nullable();
                // Stripe specific (separate columns, secrets encrypted at app layer)
                $table->string('stripe_publishable_key')->nullable();
                $table->text('stripe_secret_key')->nullable();
                $table->text('stripe_webhook_secret')->nullable();
                $table->string('stripe_mode')->nullable()->default('test'); // test|live
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
