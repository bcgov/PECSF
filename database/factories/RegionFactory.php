<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RegionFactory extends Factory
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
            'code' => $this->faker->regexify('[0-9]{3}'),
            'name' => $this->faker->words(2, true),
            'status' => $this->faker->randomElement(['A', 'I']),
            'effdt' => $this->faker->date(),
            'notes' => $this->faker->sentence(),
            'created_by_id' => 1,
            'updated_by_id' => 1,
        ];
    }
}
