<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Conversation;
use App\Models\Reponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Chaque test de cette classe verifie une regle de gestion du chapitre 2 du
 * rapport. Le nom de la methode porte le numero de la regle (RG1 a RG15) afin
 * que la specification et sa verification restent tracables l'une vers l'autre.
 */
class ReglesDeGestionTest extends TestCase
{
    use RefreshDatabase;

    // =================================================================
    //  Acces et comptes
    // =================================================================

    /** RG1 : un utilisateur doit etre authentifie pour acceder a l'application. */
    public function test_rg1_un_visiteur_non_authentifie_est_redirige_vers_la_connexion(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/conversations')->assertRedirect('/login');
        $this->get('/clients')->assertRedirect('/login');
    }

    /** RG2 : un compte desactive ne peut pas se connecter. */
    public function test_rg2_un_compte_desactive_est_deconnecte_et_renvoye_vers_la_connexion(): void
    {
        $inactif = User::factory()->inactif()->create();

        $this->actingAs($inactif)->get('/dashboard')->assertRedirect('/login');

        // Le middleware ne se contente pas de bloquer : il invalide la session.
        $this->assertGuest();
    }

    // =================================================================
    //  Conversations
    // =================================================================

    /** RG3 : une conversation est obligatoirement associee a un client. */
    public function test_rg3_une_conversation_sans_client_est_refusee(): void
    {
        $agent = User::factory()->create();

        $this->actingAs($agent)
            ->post('/conversations', [
                // ni client_id, ni nouveau_client_nom
                'sujet' => 'Commande introuvable',
                'contenu' => 'Bonjour, je ne retrouve pas ma commande.',
                'canal' => 'email',
                'categorie' => 'livraison',
                'priorite' => 'moyenne',
            ])
            ->assertSessionHasErrors('nouveau_client_nom');

        $this->assertDatabaseCount('conversations', 0);
    }

    /** RG4 : le statut doit appartenir a la liste fermee des statuts. */
    public function test_rg4_un_statut_hors_liste_est_refuse(): void
    {
        $agent = User::factory()->create();
        $conversation = Conversation::factory()->create();

        $this->actingAs($agent)
            ->patch("/conversations/{$conversation->id}/statut", ['statut' => 'archive'])
            ->assertSessionHasErrors('statut');

        $this->assertSame('nouveau', $conversation->fresh()->statut);
    }

    /** RG5 : la priorite doit appartenir a la liste fermee des priorites. */
    public function test_rg5_une_priorite_hors_liste_est_refusee(): void
    {
        $agent = User::factory()->create();
        $conversation = Conversation::factory()->create();

        $this->actingAs($agent)
            ->patch("/conversations/{$conversation->id}/priorite", ['priorite' => 'critique'])
            ->assertSessionHasErrors('priorite');

        $this->assertSame('moyenne', $conversation->fresh()->priorite);
    }

    /** RG6 : le statut par defaut d'une nouvelle conversation est "nouveau". */
    public function test_rg6_une_conversation_creee_a_le_statut_nouveau(): void
    {
        $agent = User::factory()->create();
        $client = Client::factory()->create();

        $this->actingAs($agent)->post('/conversations', [
            'client_id' => $client->id,
            'sujet' => 'Colis en retard',
            'contenu' => 'Mon colis devait arriver hier.',
            'canal' => 'whatsapp',
            'categorie' => 'livraison',
            'priorite' => 'haute',
        ]);

        $this->assertSame('nouveau', Conversation::first()->statut);
    }

    /** RG7 : toute reponse est horodatee et associee a son auteur. */
    public function test_rg7_une_reponse_porte_son_auteur_et_son_horodatage(): void
    {
        $agent = User::factory()->create();
        $conversation = Conversation::factory()->create();

        $this->actingAs($agent)->post("/conversations/{$conversation->id}/reponses", [
            'contenu' => 'Bonjour, votre colis part demain.',
        ]);

        $reponse = Reponse::first();

        $this->assertSame($agent->id, $reponse->agent_id);
        $this->assertNotNull($reponse->created_at);
    }

    /** RG10 : une conversation non assignee peut etre prise en charge par tout agent actif. */
    public function test_rg10_un_agent_peut_prendre_en_charge_une_conversation_libre(): void
    {
        $agent = User::factory()->create();
        $conversation = Conversation::factory()->create();

        $this->actingAs($agent)->post("/conversations/{$conversation->id}/assign");

        $this->assertSame($agent->id, $conversation->fresh()->agent_id);
    }

    /** RG10 (contre-exemple) : une conversation deja assignee n'est pas volee a son agent. */
    public function test_rg10_une_conversation_deja_assignee_ne_change_pas_de_proprietaire(): void
    {
        $premier = User::factory()->create();
        $second = User::factory()->create();
        $conversation = Conversation::factory()->assigneeA($premier)->create();

        $this->actingAs($second)->post("/conversations/{$conversation->id}/assign");

        $this->assertSame($premier->id, $conversation->fresh()->agent_id);
    }

    /** RG11 : repondre a une conversation "nouveau" la fait passer "en cours". */
    public function test_rg11_repondre_fait_passer_la_conversation_en_cours(): void
    {
        $agent = User::factory()->create();
        $conversation = Conversation::factory()->create(['statut' => 'nouveau']);

        $this->actingAs($agent)->post("/conversations/{$conversation->id}/reponses", [
            'contenu' => 'Nous traitons votre demande.',
        ]);

        $this->assertSame('en_cours', $conversation->fresh()->statut);
    }

    /** RG15 : la satisfaction n'accepte qu'une valeur de 1 a 5. */
    public function test_rg15_une_satisfaction_hors_bornes_est_refusee(): void
    {
        $agent = User::factory()->create();
        $conversation = Conversation::factory()->create();

        $this->actingAs($agent)
            ->patch("/conversations/{$conversation->id}/satisfaction", ['satisfaction' => 9])
            ->assertSessionHasErrors('satisfaction');

        $this->assertNull($conversation->fresh()->satisfaction);
    }

    /** RG15 (cas nominal) : une valeur valide est bien enregistree. */
    public function test_rg15_une_satisfaction_valide_est_enregistree(): void
    {
        $agent = User::factory()->create();
        $conversation = Conversation::factory()->create();

        $this->actingAs($agent)
            ->patch("/conversations/{$conversation->id}/satisfaction", ['satisfaction' => 4]);

        $this->assertSame(4, $conversation->fresh()->satisfaction);
    }

    // =================================================================
    //  Permissions
    // =================================================================

    /** RG8 : un agent peut creer une fiche client mais ne peut pas la modifier. */
    public function test_rg8_un_agent_ne_peut_pas_modifier_une_fiche_client(): void
    {
        $agent = User::factory()->create();
        $client = Client::factory()->create(['nom' => 'Alaoui']);

        $this->actingAs($agent)
            ->put("/clients/{$client->id}", ['nom' => 'Modifie'])
            ->assertForbidden();

        $this->assertSame('Alaoui', $client->fresh()->nom);
    }

    /** RG8 (cas nominal) : la creation, elle, lui est bien ouverte. */
    public function test_rg8_un_agent_peut_creer_une_fiche_client(): void
    {
        $agent = User::factory()->create();

        $this->actingAs($agent)->post('/clients', ['nom' => 'Bennani']);

        $this->assertDatabaseHas('clients', ['nom' => 'Bennani']);
    }

    /** RG9 : seul un administrateur peut supprimer un client. */
    public function test_rg9_un_agent_ne_peut_pas_supprimer_un_client(): void
    {
        $agent = User::factory()->create();
        $client = Client::factory()->create();

        $this->actingAs($agent)
            ->delete("/clients/{$client->id}")
            ->assertForbidden();

        $this->assertDatabaseCount('clients', 1);
    }

    /** RG9 (cas nominal) : l'administrateur, lui, en a le droit. */
    public function test_rg9_un_administrateur_peut_supprimer_un_client(): void
    {
        $admin = User::factory()->admin()->create();
        $client = Client::factory()->create();

        $this->actingAs($admin)->delete("/clients/{$client->id}");

        $this->assertDatabaseCount('clients', 0);
    }

    /** RG9 : la gestion des comptes est fermee aux agents et aux managers. */
    public function test_rg9_la_gestion_des_agents_est_reservee_a_l_administrateur(): void
    {
        $this->actingAs(User::factory()->create())->get('/agents')->assertForbidden();
        $this->actingAs(User::factory()->manager()->create())->get('/agents')->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get('/agents')->assertOk();
    }

    /**
     * L'analytique expose le classement de performance de chaque agent : elle
     * releve de la supervision. Le rapport et le diagramme de cas d'utilisation
     * la placent au niveau manager — ce test le garantit cote serveur.
     */
    public function test_l_analytique_est_reservee_au_manager_et_a_l_administrateur(): void
    {
        $this->actingAs(User::factory()->create())->get('/analytics')->assertForbidden();
        $this->actingAs(User::factory()->manager()->create())->get('/analytics')->assertOk();
        $this->actingAs(User::factory()->admin()->create())->get('/analytics')->assertOk();
    }

    /** Hierarchie des roles : le manager supervise les equipes, l'agent non. */
    public function test_la_gestion_des_equipes_est_ouverte_au_manager_mais_pas_a_l_agent(): void
    {
        $this->actingAs(User::factory()->create())->get('/teams')->assertForbidden();
        $this->actingAs(User::factory()->manager()->create())->get('/teams')->assertOk();
        $this->actingAs(User::factory()->admin()->create())->get('/teams')->assertOk();
    }

    // =================================================================
    //  Brouillons generes par l'intelligence artificielle
    // =================================================================

    /** RG12 : un brouillon non valide n'est jamais expose au client. */
    public function test_rg12_un_brouillon_non_valide_reste_invisible_du_client(): void
    {
        $bot = User::factory()->bot()->create();
        $conversation = Conversation::factory()->create(['canal' => 'live_chat']);
        $conversation->client->update(['widget_token' => 'jeton-visiteur']);

        Reponse::factory()->brouillon()->create([
            'conversation_id' => $conversation->id,
            'agent_id' => $bot->id,
            'contenu' => 'BROUILLON CONFIDENTIEL EN ATTENTE',
        ]);

        // Le visiteur interroge le widget avec son propre jeton : le brouillon
        // ne doit apparaitre dans aucune des reponses qui lui sont transmises.
        $reponse = $this->getJson('/widget/messages?token=jeton-visiteur');

        $reponse->assertOk();
        $this->assertStringNotContainsString('BROUILLON CONFIDENTIEL', $reponse->getContent());
    }

    /** RG13 : valider un brouillon en transfere la responsabilite a l'agent validant. */
    public function test_rg13_la_validation_attribue_la_reponse_a_l_agent_validant(): void
    {
        $bot = User::factory()->bot()->create();
        $agent = User::factory()->create();
        $conversation = Conversation::factory()->create();

        $brouillon = Reponse::factory()->brouillon()->create([
            'conversation_id' => $conversation->id,
            'agent_id' => $bot->id,
        ]);

        $this->actingAs($agent)->post(
            "/conversations/{$conversation->id}/drafts/{$brouillon->id}/approve",
            ['contenu' => 'Reponse relue et corrigee par l agent.']
        );

        $brouillon->refresh();

        $this->assertFalse($brouillon->is_draft, 'Le brouillon doit devenir une reponse envoyee.');
        $this->assertSame($agent->id, $brouillon->agent_id, "L'agent validant devient l'auteur responsable.");
        $this->assertSame('Reponse relue et corrigee par l agent.', $brouillon->contenu);
    }

    /** RG12/RG13 : rejeter un brouillon le supprime sans laisser de trace envoyee. */
    public function test_le_rejet_d_un_brouillon_le_supprime_definitivement(): void
    {
        $agent = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $brouillon = Reponse::factory()->brouillon()->create([
            'conversation_id' => $conversation->id,
            'agent_id' => User::factory()->bot()->create()->id,
        ]);

        $this->actingAs($agent)->delete("/conversations/{$conversation->id}/drafts/{$brouillon->id}");

        $this->assertDatabaseCount('reponses', 0);
    }

    /**
     * Un brouillon ne peut pas etre valide depuis une autre conversation :
     * la route verifie l'appartenance avant toute ecriture.
     */
    public function test_un_brouillon_ne_peut_pas_etre_valide_depuis_une_autre_conversation(): void
    {
        $agent = User::factory()->create();
        $conversationA = Conversation::factory()->create();
        $conversationB = Conversation::factory()->create();

        $brouillon = Reponse::factory()->brouillon()->create([
            'conversation_id' => $conversationA->id,
            'agent_id' => User::factory()->bot()->create()->id,
        ]);

        $this->actingAs($agent)
            ->post("/conversations/{$conversationB->id}/drafts/{$brouillon->id}/approve", [
                'contenu' => 'Tentative de validation croisee.',
            ])
            ->assertNotFound();

        $this->assertTrue($brouillon->fresh()->is_draft);
    }

    /** Une reponse deja envoyee ne peut pas etre re-validee comme un brouillon. */
    public function test_une_reponse_deja_envoyee_ne_peut_pas_etre_revalidee(): void
    {
        $agent = User::factory()->create();
        $conversation = Conversation::factory()->create();
        $reponse = Reponse::factory()->create(['conversation_id' => $conversation->id]);

        $this->actingAs($agent)
            ->post("/conversations/{$conversation->id}/drafts/{$reponse->id}/approve", [
                'contenu' => 'Modification illegitime.',
            ])
            ->assertNotFound();
    }
}
