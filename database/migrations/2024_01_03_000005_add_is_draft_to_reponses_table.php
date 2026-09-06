<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reponses', function (Blueprint $table) {
            // Une reponse generee par l'IA reste en brouillon tant qu'un agent ne l'a pas validee :
            // elle n'est "envoyee" (et ne compte dans l'historique/le statut) qu'apres validation.
            $table->boolean('is_draft')->default(false)->after('contenu');
        });
    }

    public function down(): void
    {
        Schema::table('reponses', function (Blueprint $table) {
            $table->dropColumn('is_draft');
        });
    }
};
