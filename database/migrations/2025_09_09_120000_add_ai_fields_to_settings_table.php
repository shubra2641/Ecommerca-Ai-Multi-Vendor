<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (! Schema::hasColumn('settings', 'ai_enabled')) {
                $table->boolean('ai_enabled')->default(false);
            }
            if (! Schema::hasColumn('settings', 'ai_provider')) {
                $table->string('ai_provider', 40)->nullable();
            }
            if (! Schema::hasColumn('settings', 'ai_openai_api_key')) {
                $table->text('ai_openai_api_key')->nullable(); // encrypted
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'ai_enabled')) {
                $table->dropColumn('ai_enabled');
            }
            if (Schema::hasColumn('settings', 'ai_provider')) {
                $table->dropColumn('ai_provider');
            }
            if (Schema::hasColumn('settings', 'ai_openai_api_key')) {
                $table->dropColumn('ai_openai_api_key');
            }
        });
    }
};
