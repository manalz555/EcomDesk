<?php

namespace Tests\Feature;

use App\Contracts\AiReplyService;
use App\Jobs\GenerateAutomatedReply;
use App\Models\AutomationRule;
use App\Models\Client;
use App\Models\Conversation;
use App\Models\Reponse;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Verifie les deux contributions distinctives du projet decrites au
 * chapitre 4 du rapport : le moteur d'automatisation par intelligence
 * artificielle fonde sur la validation humaine, et le widget de chat en
 * direct, premier canal reellement operationnel de la plateforme.
 */
class AutomatisationEtWidgetTest extends TestCase
{
    use RefreshDatabase;

    /** Cree un compte technique "assistant IA" et une regle active associee. */
    private function regleActive(array $attributs = []): AutomationRule
    {
        return AutomationRule::create(array_merge([
            'name' => 'Reponse automatique livraison',
            'bot_user_id' => User::factory()->bot()->create()->id,
            'canal' => null,          // joker : tous les canaux
            'categorie' => 'livraison',
            'enabled' => true,
            'prompt_template' => 'Reponds poliment au sujet de la livraison.',
        ], $attributs));
    }

    // =================================================================
    //  Moteur d'automatisation
    // =================================================================

    /** Une conversation correspondant a une regle active met une tache en file d'attente. */
    public function test_une_conversation_correspondant_a_une_regle_declenche_la_tache(): void
    {
        Bus::fake();
        $this->regleActive();

        $agent = User::factory()->create();
        $client = Client::factory()->create();

        $this->actingAs($agent)->post('/conversations', [
            'client_id' => $client->id,
            'sujet' => 'Ou est mon colis ?',
            'contenu' => 'Bonjour, je n ai aucune nouvelle de ma commande.',
            'canal' => 'email',
            'categorie' => 'livraison',
            'priorite' => 'moyenne',
        ]);

        Bus::assertDispatched(GenerateAutomatedReply::class);
    }

    /** Une conversation hors perimetre de la regle ne declenche rien. */
    public function test_une_categorie_non_couverte_ne_declenche_aucune_tache(): void
    {
        Bus::fake();
        $this->regleActive(['categorie' => 'remboursement']);

        $agent = User::factory()->create();
        $client = Client::factory()->create();

        $this->actingAs($agent)->post('/conversations', [
            'client_id' => $client->id,
            'sujet' => 'Produit abime',
            'contenu' => 'Le produit est arrive casse.',
            'canal' => 'email',
            'categorie' => 'produit',
            'priorite' => 'haute',
        ]);

        Bus::assertNotDispatched(GenerateAutomatedReply::class);
    }

    /** Une regle desactivee ne s'applique pas, meme si le canal et la categorie correspondent. */
    public function test_une_regle_desactivee_ne_s_applique_pas(): void
    {
        $regle = $this->regleActive(['enabled' => false]);
        $conversation = Conversation::factory()->create(['categorie' => 'livraison']);

        $this->assertFalse($regle->matches($conversation));
    }

    /**
     * Le job produit un BROUILLON et ne touche pas a la conversation :
     * c'est la traduction technique de la regle RG12.
     */
    public function test_la_tache_produit_un_brouillon_sans_modifier_la_conversation(): void
    {
        Notification::fake();
        $regle = $this->regleActive();

        $conversation = Conversation::factory()->create([
            'categorie' => 'livraison',
            'statut' => 'nouveau',
        ]);

        (new GenerateAutomatedReply($conversation, $regle))
            ->handle(app(AiReplyService::class));

        $brouillon = Reponse::first();

        $this->assertTrue($brouillon->is_draft, 'La reponse generee doit etre un brouillon.');
        $this->assertSame($regle->bot_user_id, $brouillon->agent_id, "L'auteur est le compte technique de l'assistant.");

        $conversation->refresh();
        $this->assertSame('nouveau', $conversation->statut, 'Le statut ne change qu a la validation humaine.');
        $this->assertNull($conversation->agent_id, "Aucun agent n'est assigne par la generation.");
    }

    /** L'equipe est notifiee qu'un brouillon attend validation. */
    public function test_la_generation_d_un_brouillon_notifie_le_personnel(): void
    {
        Notification::fake();
        $regle = $this->regleActive();
        $agent = User::factory()->create();
        $conversation = Conversation::factory()->create(['categorie' => 'livraison']);

        (new GenerateAutomatedReply($conversation, $regle))
            ->handle(app(AiReplyService::class));

        Notification::assertSentTo($agent, \App\Notifications\AutomatedDraftReady::class);
    }

    // =================================================================
    //  Degradation controlee du service d'IA
    // =================================================================

    /** Sans cle d'API configuree, le service produit une reponse de repli au lieu d'echouer. */
    public function test_sans_cle_api_le_service_retombe_sur_une_reponse_simulee(): void
    {
        $conversation = Conversation::factory()->create(['sujet' => 'Suivi de commande']);

        $reponse = app(AiReplyService::class)->generateReply($conversation);

        $this->assertNotEmpty($reponse);
        $this->assertStringContainsString('Suivi de commande', $reponse);
    }

    /** Si l'API externe echoue, le service degrade sans interrompre la chaine de traitement. */
    public function test_un_echec_de_l_api_externe_n_interrompt_pas_le_traitement(): void
    {
        Http::fake(['api.openai.com/*' => Http::response([], 500)]);

        $conversation = Conversation::factory()->create(['sujet' => 'Remboursement']);

        $reponse = app(AiReplyService::class)->generateReply($conversation);

        $this->assertNotEmpty($reponse, 'Le service doit toujours retourner un texte exploitable.');
    }

    // =================================================================
    //  Widget de chat en direct
    // =================================================================

    /** Un visiteur anonyme ouvre une conversation et recoit un jeton de reconnaissance. */
    public function test_un_visiteur_anonyme_ouvre_une_conversation_et_recoit_un_jeton(): void
    {
        $reponse = $this->postJson('/widget/messages', [
            'message' => 'Bonjour, avez-vous ce modele en taille M ?',
        ]);

        $reponse->assertOk()->assertJsonStructure(['token', 'conversation_id']);

        $conversation = Conversation::first();
        $this->assertSame('live_chat', $conversation->canal);
        $this->assertSame('nouveau', $conversation->statut);
        $this->assertNotNull($conversation->client->widget_token);
    }

    /** Un second message du meme visiteur alimente la conversation existante. */
    public function test_un_second_message_du_meme_visiteur_ne_cree_pas_de_doublon(): void
    {
        $jeton = $this->postJson('/widget/messages', ['message' => 'Premier message'])->json('token');

        $this->postJson('/widget/messages', [
            'token' => $jeton,
            'message' => 'Deuxieme message',
        ])->assertOk();

        $this->assertDatabaseCount('conversations', 1);
        $this->assertDatabaseCount('clients', 1);
    }

    /** Le visiteur ne voit que les reponses validees, jamais les brouillons. */
    public function test_le_visiteur_voit_la_reponse_validee_de_l_agent(): void
    {
        $jeton = $this->postJson('/widget/messages', ['message' => 'Bonjour'])->json('token');
        $conversation = Conversation::first();
        $agent = User::factory()->create(['name' => 'Yasmine']);

        Reponse::factory()->brouillon()->create([
            'conversation_id' => $conversation->id,
            'contenu' => 'BROUILLON NON VALIDE',
        ]);
        Reponse::factory()->create([
            'conversation_id' => $conversation->id,
            'agent_id' => $agent->id,
            'contenu' => 'Bonjour, comment puis-je vous aider ?',
        ]);

        $contenu = $this->getJson("/widget/messages?token={$jeton}")->getContent();

        $this->assertStringContainsString('comment puis-je vous aider', $contenu);
        $this->assertStringNotContainsString('BROUILLON NON VALIDE', $contenu);
    }

    /** Un jeton inconnu ne donne acces a aucune conversation. */
    public function test_un_jeton_inconnu_ne_donne_acces_a_aucune_conversation(): void
    {
        Conversation::factory()->create(['canal' => 'live_chat']);

        $this->getJson('/widget/messages?token=jeton-invente')
            ->assertOk()
            ->assertJson(['messages' => []]);
    }

    /** Le point d'envoi public refuse un message vide ou demesure. */
    public function test_le_point_d_envoi_public_valide_le_message(): void
    {
        $this->postJson('/widget/messages', ['message' => ''])
            ->assertJsonValidationErrors('message');

        $this->postJson('/widget/messages', ['message' => str_repeat('a', 2001)])
            ->assertJsonValidationErrors('message');
    }
}
