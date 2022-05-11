<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Provider>
 */
class ProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'office' => $this->faker->sentence(),
            'address' => $this->faker->sentence(),
            'regc' => $this->faker->sentence(),
            'cui' => $this->faker->sentence(),
        ];
    }
}
