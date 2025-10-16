<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('brand_id')->nullable()->after('product_category_id')->constrained('brands')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('products')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropConstrainedForeignId('brand_id');
            });
        }
        Schema::dropIfExists('brands');
    }
};
