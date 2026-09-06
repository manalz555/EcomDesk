<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('bot_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('canal')->nullable();
            $table->string('categorie')->nullable();
            $table->boolean('enabled')->default(false);
            $table->text('prompt_template')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_rules');
    }
};
