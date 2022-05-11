<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\Item;
use \App\Models\ItemStock;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransferItem>
 */
class TransferItemFactory extends Factory
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
            'item_stock_id' => ItemStock::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
        ];
    }
}
