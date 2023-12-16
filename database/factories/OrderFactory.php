<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_type' => fake()->type(),
            'order_status' => fake()->status(),
            'order_data_list'=> fake()->data_list(),
            'order_data_list_serialized'=> fake()->data_list_ser(),
            'order_amount'=> fake()->amount(),
            'order_transaction' => Str::random(5),
            'order_ref_cinema'=> fake()->cinema(),
            'order_notes'=> fake()->notes(),
        ];
    }
}
