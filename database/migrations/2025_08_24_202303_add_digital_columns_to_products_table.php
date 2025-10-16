<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('download_file')->nullable()->after('main_image');
            $table->string('download_url')->nullable()->after('download_file');
            $table->boolean('has_serials')->default(false)->after('download_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['download_file', 'download_url', 'has_serials']);
        });
    }
};
