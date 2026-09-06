<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channel_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('channel')->unique();
            $table->boolean('is_connected')->default(false);
            $table->string('webhook_token')->nullable();
            $table->json('config')->nullable();
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channel_integrations');
    }
};
