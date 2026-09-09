<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ajout du role "manager" a la liste fermee des roles.
        // ENUM est specifique a MySQL ; ailleurs (SQLite en test) la contrainte
        // equivalente est un CHECK, que l'on remplace par une colonne texte.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','manager','agent') NOT NULL DEFAULT 'agent'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('agent')->change();
            });
        }

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

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','agent') NOT NULL DEFAULT 'agent'");
        }
    }
};
