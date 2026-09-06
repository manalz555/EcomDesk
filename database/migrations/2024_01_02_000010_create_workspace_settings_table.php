<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workspace_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('EcomDesk');
            $table->string('logo_path')->nullable();
            $table->string('primary_color', 7)->default('#0E0D0B');
            $table->json('business_hours')->nullable();
            $table->json('notification_prefs')->nullable();
            $table->json('smtp_config')->nullable();
            $table->json('security_config')->nullable();
            $table->enum('default_theme', ['light', 'dark', 'system'])->default('system');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workspace_settings');
    }
};
