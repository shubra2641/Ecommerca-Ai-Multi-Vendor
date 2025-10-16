<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_interests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('email')->index();
            $table->string('type')->default('stock'); // stock, price_drop, etc.
            $table->string('status')->default('pending'); // pending, notified, cancelled
            $table->timestamp('notified_at')->nullable();
            $table->json('meta')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->unique(['product_id', 'email', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_interests');
    }
};
