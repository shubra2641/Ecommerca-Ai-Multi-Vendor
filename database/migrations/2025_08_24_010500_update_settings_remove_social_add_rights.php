<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'social_facebook')) {
                $table->dropColumn('social_facebook');
            }
            if (Schema::hasColumn('settings', 'social_twitter')) {
                $table->dropColumn('social_twitter');
            }
            if (Schema::hasColumn('settings', 'social_instagram')) {
                $table->dropColumn('social_instagram');
            }
            if (! Schema::hasColumn('settings', 'rights')) {
                $table->string('rights')->nullable()->after('custom_js');
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'rights')) {
                $table->dropColumn('rights');
            }
            if (! Schema::hasColumn('settings', 'social_facebook')) {
                $table->string('social_facebook')->nullable();
            }
            if (! Schema::hasColumn('settings', 'social_twitter')) {
                $table->string('social_twitter')->nullable();
            }
            if (! Schema::hasColumn('settings', 'social_instagram')) {
                $table->string('social_instagram')->nullable();
            }
        });
    }
};
