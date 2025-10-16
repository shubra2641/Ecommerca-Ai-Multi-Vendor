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
        } // safety
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'footer_app_links')) {
                $table->json('footer_app_links')->nullable();
            }
            if (! Schema::hasColumn('settings', 'footer_support_heading')) {
                $table->json('footer_support_heading')->nullable();
            }
            if (! Schema::hasColumn('settings', 'footer_support_subheading')) {
                $table->json('footer_support_subheading')->nullable();
            }
            if (! Schema::hasColumn('settings', 'footer_sections_visibility')) {
                $table->json('footer_sections_visibility')->nullable();
            }
            if (! Schema::hasColumn('settings', 'footer_payment_methods')) {
                $table->json('footer_payment_methods')->nullable();
            }
            if (! Schema::hasColumn('settings', 'rights_i18n')) {
                $table->json('rights_i18n')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        } // safety
        Schema::table('settings', function (Blueprint $table) {
            foreach (
                [
                    'footer_app_links',
                    'footer_support_heading',
                    'footer_support_subheading',
                    'footer_sections_visibility',
                    'footer_payment_methods',
                    'rights_i18n',
                ] as $col
            ) {
                if (Schema::hasColumn('settings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
