<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('agent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sujet');
            $table->text('contenu');
            $table->enum('canal', ['email', 'whatsapp', 'instagram', 'messenger', 'formulaire']);
            $table->enum('statut', ['nouveau', 'en_cours', 'en_attente', 'resolu'])->default('nouveau');
            $table->enum('priorite', ['faible', 'moyenne', 'haute'])->default('moyenne');
            $table->enum('categorie', ['livraison', 'paiement', 'remboursement', 'produit', 'reclamation', 'autre'])->default('autre');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
