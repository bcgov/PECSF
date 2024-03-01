<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CityFactory extends Factory
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
            'city' => $this->faker->city(),
            'country' => 'CAN',
            'province' =>  $this->faker->randomElement(['BC', 'ON']),
            'TGB_REG_DISTRICT' => $this->faker->regexify('/^[0][0-2][1-9]$/'),
            'DescrShort' => $this->faker->words(2, true),
        ];
    }
}
