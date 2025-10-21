<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Chat>
 */
class ChatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::inRandomOrder()->first()->id,
            'sender_id' => User::inRandomOrder()->first()->id,
            'message' => $this->faker->sentence(12),
            'is_read' => $this->faker->boolean(50),  
        ];
    }
}
