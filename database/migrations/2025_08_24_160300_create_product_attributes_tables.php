<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });
        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_attribute_id')->constrained('product_attributes')->cascadeOnDelete();
            $table->string('value');
            $table->string('slug');
            $table->timestamps();
            $table->unique(['product_attribute_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
    }
};
