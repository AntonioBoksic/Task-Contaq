<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

use App\Models\Category;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['aperto', 'in lavorazione', 'chiuso']),
            'created_at' => $this->faker->dateTimeBetween('-30 days', 'now')
        ];
    }
}
