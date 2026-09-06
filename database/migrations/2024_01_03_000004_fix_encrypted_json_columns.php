<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Les colonnes chiffrees via le cast Eloquent "encrypted:array" stockent du texte
     * chiffre (non-JSON), incompatible avec la contrainte JSON_VALID automatique de MySQL
     * sur les colonnes de type JSON. On les convertit en TEXT.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE channel_integrations MODIFY COLUMN config TEXT NULL');
        DB::statement('ALTER TABLE workspace_settings MODIFY COLUMN smtp_config TEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE channel_integrations MODIFY COLUMN config JSON NULL');
        DB::statement('ALTER TABLE workspace_settings MODIFY COLUMN smtp_config JSON NULL');
    }
};
