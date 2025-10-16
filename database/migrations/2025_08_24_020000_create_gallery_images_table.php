<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->string('original_path');
            $table->string('webp_path')->nullable();
            $table->string('title')->nullable(); // SEO title
            $table->text('description')->nullable(); // SEO description
            $table->string('alt')->nullable();
            $table->unsignedBigInteger('filesize')->nullable();
            $table->string('mime')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_images');
    }
};
