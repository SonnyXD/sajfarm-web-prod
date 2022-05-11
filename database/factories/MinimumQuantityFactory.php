<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\Item;
use \App\Models\Inventory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MinimumQuantity>
 */
class MinimumQuantityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'item_id' => Item::factory(),
            'inventory_id' => Inventory::factory(),
            'quantity' => $this->faker->numberBetween(1,10),
        ];
    }
}
