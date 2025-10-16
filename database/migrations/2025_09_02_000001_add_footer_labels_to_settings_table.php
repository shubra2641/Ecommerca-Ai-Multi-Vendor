<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'footer_labels')) {
                $table->json('footer_labels')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'footer_labels')) {
                $table->dropColumn('footer_labels');
            }
        });
    }
};
