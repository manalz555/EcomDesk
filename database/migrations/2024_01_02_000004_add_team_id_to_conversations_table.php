<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->foreignId('team_id')->nullable()->after('agent_id')->constrained('teams')->nullOnDelete();
            $table->timestamp('last_message_at')->nullable()->after('categorie');
        });

        // Elargissement des canaux simules : ajout de telegram et du live chat, en plus des 5 canaux existants.
        // Le type ENUM n'existe qu'en MySQL : sur les autres SGBD (SQLite en test)
        // la colonne est deja un VARCHAR libre, l'elargissement n'a pas lieu d'etre.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE conversations MODIFY COLUMN canal ENUM('email','whatsapp','instagram','messenger','telegram','live_chat','formulaire') NOT NULL");
        }
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');
            $table->dropColumn('last_message_at');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE conversations MODIFY COLUMN canal ENUM('email','whatsapp','instagram','messenger','formulaire') NOT NULL");
        }
    }
};
