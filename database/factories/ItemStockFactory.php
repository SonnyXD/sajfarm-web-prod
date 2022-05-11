<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\Item;
use \App\Models\Inventory;
use \App\Models\Invoice;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item_stock>
 */
class ItemStockFactory extends Factory
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
            'invoice_id' => Invoice::factory(),
            'quantity' => $this->faker->randomNumber(1, 100),
        ];
    }
}
