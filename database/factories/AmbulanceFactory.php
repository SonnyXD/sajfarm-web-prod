<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\AmbulanceType;
use \App\Models\Substation;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ambulance>
 */
class AmbulanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'license_plate' => $this->faker->word(),
            'ambulance_type_id' => AmbulanceType::factory(),
            'substation_id' => Substation::factory(),
        ];
    }
}
