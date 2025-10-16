<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('product_variations', 'image')) {
            Schema::table('product_variations', function (Blueprint $table) {
                $table->string('image')->nullable()->after('attribute_hash');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('product_variations', 'image')) {
            Schema::table('product_variations', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }
    }
};
