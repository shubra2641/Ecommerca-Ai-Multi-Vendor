<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('products', 'used_attributes')) {
            Schema::table('products', function (Blueprint $table) {
                $table->json('used_attributes')->nullable()->after('gallery');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('products', 'used_attributes')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('used_attributes');
            });
        }
    }
};
