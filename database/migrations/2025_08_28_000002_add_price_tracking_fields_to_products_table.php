<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('last_price', 12, 2)->nullable()->after('price');
            $table->decimal('last_sale_price', 12, 2)->nullable()->after('sale_price');
            $table->timestamp('price_changed_at')->nullable()->after('sale_end');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['last_price', 'last_sale_price', 'price_changed_at']);
        });
    }
};
