<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
       
        $code = $this->faker->regexify('/^[A-Z]{3}$/');

        return [
            //
            'code' => $code,    // $this->faker->randomElement(['LA', 'BCA', 'GOV', 'RET']),
            'name' => $this->faker->words(2, true),
            'status' => $this->faker->randomElement(['A', 'I']),
            'effdt' => $this->faker->date(),
            'bu_code' => null,

            'created_by_id' => 1,
            'updated_by_id' => 1,
        ];
    }
}
