<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            // agent_id volontairement absent : une conversation non assignee
            // est l'etat par defaut a la creation (RG10).
            'sujet' => fake()->sentence(5),
            'contenu' => fake()->paragraph(),
            'canal' => fake()->randomElement(Conversation::CANAUX),
            'statut' => 'nouveau',
            'priorite' => 'moyenne',
            'categorie' => 'autre',
        ];
    }

    /** Conversation deja prise en charge par un agent donne. */
    public function assigneeA($agent): static
    {
        return $this->state(fn () => [
            'agent_id' => $agent->id,
            'statut' => 'en_cours',
        ]);
    }
}
