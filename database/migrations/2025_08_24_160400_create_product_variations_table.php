<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('sku')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->timestamp('sale_start')->nullable();
            $table->timestamp('sale_end')->nullable();
            $table->boolean('manage_stock')->default(false);
            $table->integer('stock_qty')->nullable();
            $table->integer('reserved_qty')->default(0);
            $table->boolean('backorder')->default(false);
            $table->boolean('active')->default(true);
            $table->json('attribute_data'); // e.g. {"color":"Black","size":"M"}
            $table->string('attribute_hash', 64)->nullable(); // sha256 of sorted attribute_data for uniqueness
            $table->timestamps();
            $table->unique(['product_id', 'attribute_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
