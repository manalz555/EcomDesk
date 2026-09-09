<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Client;
use App\Models\Company;
use App\Models\AutomationRule;
use App\Models\Reponse;
use App\Models\Conversation;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Fills the workspace with a realistic 3 weeks of activity so the dashboard
 * and analytics look like a real, busy support team rather than an empty
 * demo: varied conversations, spread-out dates, replies, tags, notes and
 * satisfaction ratings. Safe to run once on top of the base seed data
 * (guarded by a sentinel client below).
 */
class DemoDataSeeder extends Seeder
{
    /** Realistic exchanges, by category: [sujet, message client, réponse agent]. */
    private const SCENARIOS = [
        'livraison' => [
            ["Colis pas encore reçu", "Bonjour, ma commande #38412 devait arriver lundi et je n'ai toujours rien reçu. Pouvez-vous vérifier ?", "Bonjour, je viens de vérifier auprès du transporteur : votre colis est en cours d'acheminement et sera livré demain avant 18h. Toutes mes excuses pour ce retard."],
            ["Adresse de livraison à modifier", "Bonjour, je viens de passer commande mais je me suis trompée d'adresse, c'est l'ancienne qui est enregistrée…", "Bonjour, pas de souci ! J'ai mis à jour l'adresse avant l'expédition. Vous recevrez la confirmation par email dans quelques minutes."],
            ["Suivi de commande introuvable", "Le lien de suivi de ma commande ne fonctionne pas, la page affiche une erreur.", "Bonjour, le numéro de suivi a été régénéré. Voici le nouveau lien, il est actif dès maintenant. Bonne journée !"],
            ["Livraison express possible ?", "Bonjour, si je commande aujourd'hui, est-il possible d'être livré avant samedi ? C'est pour un cadeau.", "Bonjour ! Oui, en choisissant l'option express au paiement vous serez livré vendredi. Je vous ai appliqué un code de réduction sur les frais pour vous aider."],
        ],
        'paiement' => [
            ["Paiement refusé sans raison", "Bonjour, ma carte est refusée au moment de payer alors qu'elle fonctionne partout ailleurs.", "Bonjour, le problème venait de notre prestataire de paiement, c'est rétabli. Vous pouvez retenter, tout fonctionnera."],
            ["Débité deux fois", "Je viens de vérifier mon compte : j'ai été débité deux fois pour la même commande !", "Bonjour, je confirme le double débit — le remboursement du doublon est déjà lancé, vous le verrez sous 48h. Toutes mes excuses pour ce désagrément."],
            ["Facture introuvable", "Bonjour, j'ai besoin de la facture de ma commande du 12 pour ma comptabilité. Où puis-je la télécharger ?", "Bonjour, je viens de vous renvoyer la facture par email. Vous la trouverez aussi dans votre espace client, rubrique « Mes commandes »."],
        ],
        'remboursement' => [
            ["Retour effectué, pas de remboursement", "Bonjour, j'ai renvoyé l'article il y a 12 jours et je n'ai toujours pas été remboursée.", "Bonjour, votre retour a bien été réceptionné hier. Le remboursement est validé aujourd'hui, il apparaîtra sur votre compte sous 3 à 5 jours ouvrés."],
            ["Article défectueux", "Le produit reçu ne fonctionne pas du tout, je souhaite être remboursé intégralement.", "Bonjour, je suis désolée pour cette mauvaise expérience. Je vous envoie une étiquette de retour prépayée et le remboursement partira dès réception du colis."],
        ],
        'produit' => [
            ["Question sur la taille", "Bonjour, le modèle « Atlas » taille-t-il normalement ? Je fais du 40 habituellement.", "Bonjour ! Le modèle Atlas taille légèrement petit, nous conseillons de prendre une pointure au-dessus — donc un 41 pour vous."],
            ["Disponibilité réassort", "L'article est en rupture de stock, savez-vous quand il sera de nouveau disponible ?", "Bonjour, le réassort est prévu la semaine prochaine. Je peux vous prévenir par email dès qu'il est en ligne si vous le souhaitez ?"],
            ["Compatibilité accessoire", "Est-ce que la housse vendue sur votre site est compatible avec le modèle 2024 ?", "Bonjour, oui, la housse est compatible avec les modèles 2023 et 2024. N'hésitez pas si vous avez d'autres questions !"],
        ],
        'reclamation' => [
            ["Colis arrivé endommagé", "Le carton est arrivé complètement écrasé et le produit a une fissure. Je suis très déçue.", "Bonjour, je comprends tout à fait votre déception et je vous présente nos excuses. Un produit neuf part aujourd'hui en express, sans frais, et vous gardez l'ancien."],
            ["Article manquant dans la commande", "Bonjour, il manque un article dans mon colis alors qu'il est indiqué comme livré sur la facture.", "Bonjour, l'article manquant vous est réexpédié dès aujourd'hui en priorité. Encore désolée pour cette erreur de préparation."],
        ],
        'autre' => [
            ["Créer un compte professionnel", "Bonjour, je gère une boutique et j'aimerais savoir si vous proposez des tarifs revendeurs.", "Bonjour ! Oui, nous avons un programme B2B. Je vous envoie la grille tarifaire et les conditions par email."],
            ["Carte cadeau", "Est-il possible d'acheter une carte cadeau d'un montant personnalisé ?", "Bonjour, tout à fait ! Les cartes cadeaux sont disponibles de 100 à 2000 MAD, envoyées par email à la personne de votre choix."],
        ],
    ];

    private const CLIENTS = [
        ['nom' => 'El Amrani', 'prenom' => 'Salma', 'email' => 'salma.elamrani@gmail.com', 'telephone' => '0661234501'],
        ['nom' => 'Benjelloun', 'prenom' => 'Mehdi', 'email' => 'mehdi.benjelloun@outlook.com', 'telephone' => '0662234502'],
        ['nom' => 'Tahiri', 'prenom' => 'Nada', 'email' => 'nada.tahiri@gmail.com', 'telephone' => '0663234503'],
        ['nom' => 'Ouazzani', 'prenom' => 'Reda', 'email' => 'reda.ouazzani@gmail.com', 'telephone' => '0664234504'],
        ['nom' => 'Lahlou', 'prenom' => 'Kenza', 'email' => 'kenza.lahlou@yahoo.fr', 'telephone' => '0665234505'],
        ['nom' => 'Berrada', 'prenom' => 'Anas', 'email' => 'anas.berrada@gmail.com', 'telephone' => '0666234506'],
        ['nom' => 'Fassi', 'prenom' => 'Lina', 'email' => 'lina.fassi@gmail.com', 'telephone' => '0667234507'],
        ['nom' => 'Skalli', 'prenom' => 'Hamza', 'email' => 'hamza.skalli@hotmail.com', 'telephone' => '0668234508'],
        ['nom' => 'Bennis', 'prenom' => 'Aya', 'email' => 'aya.bennis@gmail.com', 'telephone' => '0669234509'],
        ['nom' => 'Chami', 'prenom' => 'Walid', 'email' => 'walid.chami@gmail.com', 'telephone' => '0660234510'],
        ['nom' => 'Idrissi', 'prenom' => 'Sofia', 'email' => 'sofia.idrissi@gmail.com', 'telephone' => '0661234511'],
        ['nom' => 'Alami', 'prenom' => 'Yassine', 'email' => 'yassine.alami@gmail.com', 'telephone' => '0662234512'],
    ];

    private const TAGS = [
        ['name' => 'VIP', 'color' => '#a6854f'],
        ['name' => 'Commande', 'color' => '#3b6b47'],
        ['name' => 'Retour', 'color' => '#8a3b3b'],
        ['name' => 'À rappeler', 'color' => '#5a564f'],
    ];

    public function run(): void
    {
        // Sentinel: never double-seed the same workspace.
        if (Client::where('email', 'salma.elamrani@gmail.com')->exists()) {
            $this->command?->warn('DemoDataSeeder: données déjà présentes, rien à faire.');

            return;
        }

        // Give the generic seed agents believable names (emails unchanged —
        // the demo logins agent1@/agent2@ecomdesk.test keep working).
        User::where('email', 'agent1@ecomdesk.test')->update(['name' => 'Yasmine Berrada']);
        User::where('email', 'agent2@ecomdesk.test')->update(['name' => 'Omar Tazi']);

        $companies = collect([
            Company::firstOrCreate(['name' => 'Maison Kella'], ['domain' => 'maisonkella.ma', 'phone' => '0522456789']),
            Company::firstOrCreate(['name' => 'Atlas Sport'], ['domain' => 'atlassport.ma', 'phone' => '0522987654']),
        ]);

        $tags = collect(self::TAGS)->map(fn ($t) => Tag::firstOrCreate(['name' => $t['name']], ['color' => $t['color']]));

        $agents = User::where('is_bot', false)->whereIn('role', ['admin', 'manager', 'agent'])->get();

        $clients = collect(self::CLIENTS)->map(function ($data, $i) use ($companies) {
            // Every third client belongs to one of the business accounts.
            if ($i % 3 === 0) {
                $data['company_id'] = $companies->random()->id;
            }

            return Client::create($data);
        });

        $now = Carbon::now();
        $count = 0;

        foreach (self::SCENARIOS as $categorie => $scenarios) {
            foreach ($scenarios as $i => [$sujet, $messageClient, $reponseAgent]) {
                // Each scenario runs twice with different clients/dates for volume.
                for ($round = 0; $round < 2; $round++) {
                    $createdAt = $now->copy()
                        ->subDays(rand(0, 20))
                        ->setTime(rand(9, 18), rand(0, 59));

                    $client = $clients->random();
                    $agent = $agents->random();

                    // Weighted status mix: half the traffic is resolved, the
                    // rest spread across the active states — like a real inbox.
                    $statut = collect(['resolu', 'resolu', 'resolu', 'en_cours', 'en_cours', 'nouveau', 'en_attente'])->random();
                    $assigned = $statut !== 'nouveau' || rand(0, 1);

                    $conversation = new Conversation([
                        'client_id' => $client->id,
                        'agent_id' => $assigned ? $agent->id : null,
                        'sujet' => $sujet,
                        'contenu' => $messageClient,
                        'canal' => collect(Conversation::CANAUX)->random(),
                        'categorie' => $categorie,
                        'priorite' => collect(['faible', 'moyenne', 'moyenne', 'haute'])->random(),
                        'statut' => $statut,
                        'satisfaction' => $statut === 'resolu' && rand(0, 9) < 7 ? collect([3, 4, 4, 5, 5, 5])->random() : null,
                        'last_message_at' => $createdAt,
                    ]);
                    $conversation->created_at = $createdAt;
                    $conversation->updated_at = $createdAt;
                    $conversation->save();

                    // Conversations being worked on have the agent's reply,
                    // posted a realistic delay after the customer wrote.
                    if ($assigned && $statut !== 'nouveau') {
                        $replyAt = $createdAt->copy()->addMinutes(rand(4, 180));
                        $reponse = $conversation->reponses()->make([
                            'agent_id' => $conversation->agent_id,
                            'contenu' => $reponseAgent,
                        ]);
                        $reponse->created_at = $replyAt;
                        $reponse->updated_at = $replyAt;
                        $reponse->save();

                        $conversation->update(['last_message_at' => $replyAt]);
                    }

                    // A third of conversations get a tag, a fifth an internal note.
                    if (rand(0, 2) === 0) {
                        $conversation->tags()->attach($tags->random()->id);
                    }
                    if (rand(0, 4) === 0) {
                        $note = $conversation->notes()->make([
                            'user_id' => $agents->random()->id,
                            'contenu' => collect([
                                'Client fidèle, commande régulièrement depuis 2024.',
                                'A déjà appelé deux fois — traiter en priorité.',
                                'Vérifier avec l\'entrepôt avant de confirmer.',
                                'Geste commercial accordé (bon de réduction -10%).',
                            ])->random(),
                        ]);
                        $note->created_at = $createdAt->copy()->addMinutes(rand(10, 120));
                        $note->updated_at = $note->created_at;
                        $note->save();
                    }

                    // Keep the dashboard activity feed lively too.
                    $log = new AuditLog([
                        'actor_id' => $conversation->agent_id ?? $agents->first()->id,
                        'action' => $statut === 'resolu' ? 'conversation.status_changed' : 'conversation.created',
                        'subject_type' => $conversation->getMorphClass(),
                        'subject_id' => $conversation->id,
                        'description' => $statut === 'resolu' ? 'Statut changé en « resolu »' : "Conversation créée : {$sujet}",
                    ]);
                    $log->created_at = $conversation->last_message_at;
                    $log->updated_at = $conversation->last_message_at;
                    $log->save();

                    $count++;
                }
            }
        }

        $this->demonstrerLeWidget($clients, $now);
        $this->laisserUnBrouillonEnAttente($clients, $now);

        $this->command?->info("DemoDataSeeder: {$count} conversations réalistes créées.");
    }

    /**
     * Reconstitue un echange complet arrive par le widget de chat : le
     * visiteur ecrit sans compte, un agent lui repond, le visiteur relance.
     * C'est le seul canal reellement operationnel, il doit etre visible dans
     * la boite de reception des l'installation.
     */
    private function demonstrerLeWidget($clients, Carbon $now): void
    {
        $agent = User::where('email', 'agent1@ecomdesk.test')->first()
            ?? User::where('is_bot', false)->first();

        $visiteur = Client::create([
            'nom' => 'Visiteur ' . strtoupper(str()->random(4)),
            'widget_token' => str()->random(40),
        ]);

        $ouvertureAt = $now->copy()->subHours(5);

        $conversation = new Conversation([
            'client_id' => $visiteur->id,
            'agent_id' => $agent->id,
            'sujet' => 'Livraison possible a Marrakech ?',
            'contenu' => "Bonjour, est-ce que vous livrez a Marrakech et sous quel delai ?",
            'canal' => 'live_chat',
            'categorie' => 'livraison',
            'priorite' => 'moyenne',
            'statut' => 'en_cours',
            'last_message_at' => $ouvertureAt,
        ]);
        $conversation->created_at = $ouvertureAt;
        $conversation->updated_at = $ouvertureAt;
        $conversation->save();

        // Reponse de l'agent, puis relance du visiteur (is_client = true) :
        // une conversation peut contenir des messages venant des deux cotes.
        $echanges = [
            [12, false, "Bonjour ! Oui, nous livrons a Marrakech en 48h ouvrees. La livraison est offerte des 400 MAD d'achat."],
            [20, true, "Parfait, merci beaucoup pour votre reactivite !"],
        ];

        foreach ($echanges as [$minutes, $duClient, $contenu]) {
            $at = $ouvertureAt->copy()->addMinutes($minutes);

            $message = new Reponse([
                'conversation_id' => $conversation->id,
                'agent_id' => $duClient ? null : $agent->id,
                'contenu' => $contenu,
                'is_client' => $duClient,
                'is_draft' => false,
            ]);
            $message->created_at = $at;
            $message->updated_at = $at;
            $message->save();

            $conversation->update(['last_message_at' => $at]);
        }
    }

    /**
     * Laisse une conversation avec un brouillon genere par l'assistant et non
     * encore valide. Sans elle, la fonctionnalite phare du projet — la
     * validation humaine — n'est pas demontrable sur une installation neuve.
     */
    private function laisserUnBrouillonEnAttente($clients, Carbon $now): void
    {
        $regle = AutomationRule::where('categorie', 'livraison')->first();

        if (! $regle) {
            return;
        }

        // La regle est livree active : la chaine d'automatisation se declenche
        // des la premiere conversation « livraison » creee en demonstration.
        $regle->update(['enabled' => true]);

        $arriveeAt = $now->copy()->subMinutes(35);

        $conversation = new Conversation([
            'client_id' => $clients->random()->id,
            'agent_id' => null,
            'sujet' => 'Ou en est ma commande #38907 ?',
            'contenu' => "Bonjour, ma commande devait partir hier. Avez-vous une date d'expedition ?",
            'canal' => 'whatsapp',
            'categorie' => 'livraison',
            'priorite' => 'haute',
            'statut' => 'nouveau',
            'last_message_at' => $arriveeAt,
        ]);
        $conversation->created_at = $arriveeAt;
        $conversation->updated_at = $arriveeAt;
        $conversation->save();

        $brouillonAt = $arriveeAt->copy()->addSeconds(20);

        $brouillon = new Reponse([
            'conversation_id' => $conversation->id,
            'agent_id' => $regle->bot_user_id,
            'contenu' => "Bonjour, merci pour votre message concernant votre commande #38907. "
                . "Je verifie immediatement sa date d'expedition aupres de notre entrepot et "
                . "je reviens vers vous dans la journee.",
            'is_draft' => true,
        ]);
        $brouillon->created_at = $brouillonAt;
        $brouillon->updated_at = $brouillonAt;
        $brouillon->save();

        AuditLog::create([
            'actor_id' => $regle->bot_user_id,
            'action' => 'conversation.draft_generated',
            'subject_type' => $conversation->getMorphClass(),
            'subject_id' => $conversation->id,
            'description' => "Brouillon de réponse généré par la règle « {$regle->name} », en attente de validation.",
        ]);
    }
}
