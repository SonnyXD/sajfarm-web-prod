<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\Provider;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'provider_id' => Provider::factory(),
            'document_date' => $this->faker->date(),
            'number' => $this->faker->numberBetween(1, 1000),
            'due_date' => $this->faker->date(),
            'discount_procent' => $this->faker->randomFloat(3, 1, 80),
            'total' => $this->faker->randomFloat(3, 10, 500)
        ];
    }
}
