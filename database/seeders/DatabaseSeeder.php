<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Manal Zouggarh',
            'email' => 'admin@ecomdesk.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Karim Idrissi',
            'email' => 'manager@ecomdesk.test',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Agent Un',
            'email' => 'agent1@ecomdesk.test',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Agent Deux',
            'email' => 'agent2@ecomdesk.test',
            'password' => Hash::make('password'),
            'role' => 'agent',
            'actif' => true,
            'email_verified_at' => now(),
        ]);

        // Les fiches clients et les conversations sont entierement produites par
        // DemoDataSeeder : les trois conversations generiques creees ici jusqu'a
        // present portaient toutes le meme sujet et arrivaient en tete de liste.

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
