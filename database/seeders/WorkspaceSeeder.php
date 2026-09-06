<?php

namespace Database\Seeders;

use App\Models\AutomationRule;
use App\Models\Company;
use App\Models\Team;
use App\Models\User;
use App\Models\WorkspaceSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class WorkspaceSeeder extends Seeder
{
    public function run(): void
    {
        WorkspaceSetting::current();

        $company = Company::firstOrCreate(
            ['name' => 'Atlas Boutique'],
            ['domain' => 'atlas-boutique.test', 'phone' => '0522000000']
        );

        $team = Team::firstOrCreate(
            ['name' => 'Support client'],
            ['description' => 'Equipe en charge des conversations entrantes tous canaux.']
        );

        $humanAgents = User::where('is_bot', false)->where('role', 'agent')->get();
        $team->users()->syncWithoutDetaching($humanAgents->pluck('id'));

        $bot = User::firstOrCreate(
            ['email' => 'assistant@ecomdesk.test'],
            [
                'name' => 'EcomDesk Assistant',
                'password' => Hash::make(str()->random(32)),
                'role' => 'agent',
                'actif' => true,
                'is_bot' => true,
                'email_verified_at' => now(),
            ]
        );

        AutomationRule::firstOrCreate(
            ['name' => 'Reponse automatique - Livraison'],
            [
                'bot_user_id' => $bot->id,
                'canal' => null,
                'categorie' => 'livraison',
                'enabled' => false,
                'prompt_template' => "Tu es l'assistant du service client d'EcomDesk. Reponds poliment et brievement a la demande du client concernant sa livraison, en francais.",
            ]
        );
    }
}
