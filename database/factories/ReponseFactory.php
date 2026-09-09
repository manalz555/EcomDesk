<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Reponse;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reponse>
 */
class ReponseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'agent_id' => User::factory(),
            'contenu' => fake()->paragraph(),
            'is_draft' => false,
            'is_client' => false,
        ];
    }

    /** Brouillon genere par l'IA, en attente de validation humaine (RG12). */
    public function brouillon(): static
    {
        return $this->state(fn () => ['is_draft' => true]);
    }
}
