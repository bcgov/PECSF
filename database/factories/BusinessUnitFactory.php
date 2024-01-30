<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BusinessUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $code = 'BC' . $this->faker->regexify('[0-9]{3}');

        return [
            //
            'code' => $code,
            'name' => $this->faker->words(2, true),
            'status' => $this->faker->randomElement(['A', 'I']),
            'effdt' => $this->faker->date(),
            'notes' => $this->faker->sentence(),
            'linked_bu_code' => $code,
            'created_by_id' => 1,
            'updated_by_id' => 1,
        ];
    }
}
