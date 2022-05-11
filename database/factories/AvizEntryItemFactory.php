<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use \App\Models\AvizEntry;
use \App\Models\Item;
use \App\Models\MeasureUnit;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AvizEntryItem>
 */
class AvizEntryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'aviz_entry_id' => AvizEntry::factory(),
            'item_id' => Item::factory(),
            'cim_code' => $this->faker->word(),
            'product_code' => $this->faker->word(),
            'quantity' => $this->faker->numberBetween(1,9),
            'exp_date' => $this->faker->date(),
            'lot' => $this->faker->word(),
            'measure_unit_id' => MeasureUnit::factory(),
            'price' => $this->faker->randomFloat(4, 1, 10),
            'tva' => $this->faker->numberBetween(1, 19),
            'tva_price' => $this->faker->randomFloat(4, 1, 10),
            'value' => $this->faker->randomFloat(4, 1, 10),
        ];
    }
}
