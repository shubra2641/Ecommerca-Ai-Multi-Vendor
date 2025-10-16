<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_interests', function (Blueprint $table) {
            $table->string('phone', 40)->nullable()->after('email');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::table('product_interests', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropColumn('phone');
        });
    }
};
