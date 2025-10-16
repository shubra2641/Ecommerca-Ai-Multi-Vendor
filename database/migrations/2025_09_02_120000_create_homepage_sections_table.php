<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('title_i18n')->nullable();
            $table->json('subtitle_i18n')->nullable();
            $table->boolean('enabled')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->unsignedSmallInteger('item_limit')->nullable();
            $table->json('config')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
