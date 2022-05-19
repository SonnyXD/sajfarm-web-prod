<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\Checklist;
use \App\Models\Medic;
use \App\Models\Inventory;
use \App\Models\Ambulance;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consumption>
 */
class ConsumptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'inventory_id' => Inventory::factory(),
            'medic_id' => Medic::factory(),
            'ambulance_id' => Ambulance::factory(),
            'patient_number' => $this->faker->numberBetween(1, 10),
            'tour' => $this->faker->word(),
            'document_date' => $this->faker->date(),
        ];
    }
}
