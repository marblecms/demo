<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_meta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->foreignId('language_id')->constrained('languages')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->string('description', 500)->nullable();
            $table->string('og_image_url')->nullable();
            $table->boolean('noindex')->default(false);
            $table->string('canonical_url')->nullable();
            $table->timestamps();

            $table->unique(['item_id', 'language_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_meta');
    }
};
