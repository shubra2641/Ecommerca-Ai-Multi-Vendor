<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'maintenance_enabled')) {
                $table->boolean('maintenance_enabled')->default(false)->after('auto_publish_reviews');
            }
            if (! Schema::hasColumn('settings', 'maintenance_message')) {
                // JSON field for multilingual messages e.g. {"en":"...","ar":"..."}
                $table->text('maintenance_message')->nullable()->after('maintenance_enabled');
            }
            if (! Schema::hasColumn('settings', 'maintenance_reopen_at')) {
                $table->timestamp('maintenance_reopen_at')->nullable()->after('maintenance_message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'maintenance_reopen_at')) {
                $table->dropColumn('maintenance_reopen_at');
            }
            if (Schema::hasColumn('settings', 'maintenance_message')) {
                $table->dropColumn('maintenance_message');
            }
            if (Schema::hasColumn('settings', 'maintenance_enabled')) {
                $table->dropColumn('maintenance_enabled');
            }
        });
    }
};
