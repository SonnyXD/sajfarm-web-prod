<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\Checklist;

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
            'checklist_id' => Checklist::factory(),
            'document_date' => $this->faker->date(),
        ];
    }
}
