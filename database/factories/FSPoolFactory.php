<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FSPoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //
            'region_id' => $this->faker->numberBetween(1,100),
            'status' => $this->faker->randomElement(['A', 'I']),
            'start_date' => $this->faker->date(),
            'created_by_id' => 1,
            'updated_by_id' => 1,
        ];
    }
}
