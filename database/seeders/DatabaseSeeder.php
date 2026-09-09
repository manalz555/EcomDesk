<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Manal Zouggarh',
            'email' => 'admin@ecomdesk.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        $manager = User::create([
            'name' => 'Karim Idrissi',
            'email' => 'manager@ecomdesk.test',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        $agent1 = User::create([
            'name' => 'Agent Un',
            'email' => 'agent1@ecomdesk.test',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        $agent2 = User::create([
            'name' => 'Agent Deux',
            'email' => 'agent2@ecomdesk.test',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        $clients = [
            ['nom' => 'Alaoui', 'prenom' => 'Sara', 'email' => 'sara.alaoui@test.com', 'telephone' => '0600000001'],
            ['nom' => 'Bennani', 'prenom' => 'Youssef', 'email' => 'y.bennani@test.com', 'telephone' => '0600000002'],
            ['nom' => 'Chraibi', 'prenom' => 'Imane', 'email' => 'imane.c@test.com', 'telephone' => '0600000003'],
        ];

        foreach ($clients as $data) {
            $client = Client::create($data);

            $conversation = Conversation::create([
                'client_id' => $client->id,
                'agent_id' => rand(0, 1) ? $agent1->id : null,
                'sujet' => 'Question concernant ma commande',
                'contenu' => "Bonjour, je n'ai pas encore recu de nouvelles sur ma commande, pouvez-vous m'aider ?",
                'canal' => ['email', 'whatsapp', 'instagram', 'messenger', 'formulaire'][array_rand([0,1,2,3,4])],
                'statut' => ['nouveau', 'en_cours', 'en_attente', 'resolu'][array_rand([0,1,2,3])],
                'priorite' => ['faible', 'moyenne', 'haute'][array_rand([0,1,2])],
                'categorie' => 'livraison',
            ]);

            if ($conversation->agent_id) {
                $conversation->reponses()->create([
                    'agent_id' => $conversation->agent_id,
                    'contenu' => 'Bonjour, je verifie cela immediatement et reviens vers vous rapidement.',
                ]);
            }
        }

        // WorkspaceSeeder pose l'espace de travail (entreprise, equipe, compte
        // technique de l'assistant, regle d'automatisation) ; DemoDataSeeder
        // le remplit de trois semaines d'activite realiste, datee par rapport
        // au jour d'execution. Sans ce second appel, `migrate:fresh --seed`
        // produisait une demonstration de trois conversations, sans brouillon
        // ni analytique exploitable.
        $this->call([
            WorkspaceSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
