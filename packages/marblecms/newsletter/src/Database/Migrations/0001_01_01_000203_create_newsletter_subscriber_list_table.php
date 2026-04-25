<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscriber_list', function (Blueprint $table) {
            $table->foreignId('subscriber_id')->constrained('newsletter_subscribers')->cascadeOnDelete();
            $table->foreignId('list_id')->constrained('newsletter_lists')->cascadeOnDelete();
            $table->timestamp('subscribed_at')->useCurrent();

            $table->primary(['subscriber_id', 'list_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscriber_list');
    }
};
