<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','agent') NOT NULL DEFAULT 'agent'");

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_bot')->default(false)->after('role');
            $table->string('avatar_path')->nullable()->after('is_bot');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_bot', 'avatar_path']);
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','agent') NOT NULL DEFAULT 'agent'");
    }
};
