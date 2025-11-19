<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CampaignYearFactory extends Factory
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
            'calendar_year' => $this->faker->unique()->numberBetween(2005,2099),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'number_of_periods' => $this->faker->randomNumber(2),
            'as_of_date' => $this->faker->date(),
            'close_date' => $this->faker->date(),
            'volunteer_start_date' => $this->faker->date(),
            'volunteer_end_date' => $this->faker->date(),
            'created_by_id' => 1,
            'modified_by_id' => 1,
        ];
    }
}
