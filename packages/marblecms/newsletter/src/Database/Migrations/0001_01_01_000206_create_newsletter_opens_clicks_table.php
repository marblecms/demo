<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_opens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('send_id')->constrained('newsletter_sends')->cascadeOnDelete();
            $table->timestamp('opened_at')->useCurrent();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
        });

        Schema::create('newsletter_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('send_id')->constrained('newsletter_sends')->cascadeOnDelete();
            $table->text('url');
            $table->timestamp('clicked_at')->useCurrent();
            $table->string('ip', 45)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_clicks');
        Schema::dropIfExists('newsletter_opens');
    }
};
