<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'acronym' => $this->faker->regexify('[A-Z]{4}'),
            'status' => $this->faker->randomElement(['A', 'I']),
            'effdt' => Carbon::parse($this->faker->date()),
            'notes' => $this->faker->sentence(),
            'linked_bu_code' => $code,
            'created_by_id' => 1,
            'updated_by_id' => 1,
        ];
    }
}
