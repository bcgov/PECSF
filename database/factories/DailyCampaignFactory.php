<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DailyCampaignFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $bu_code = 'BC' . $this->faker->regexify('[0-9]{3}');

        return [
            //
            "campaign_year" => $this->faker->year(),
            "as_of_date" => $this->faker->date(), 
            "daily_type" => 0,      // $this->faker->randomElement([0,1,2]),
            'business_unit' =>  'BC' . $this->faker->regexify('[0-9]{3}'),
            'business_unit_name' => $this->faker->words(2, true),
            'region_code' => null,
            'region_name' => null,
            'deptid' => null,
            'dept_name' => null,
            'participation_rate' => null,
            'previous_participation_rate' => $this->faker->randomFloat(),
            'change_rate' => $this->faker->randomFloat(),
            'rank' => $this->faker->randomNumber(2, false),
            'eligible_employee_count' => $this->faker->randomNumber(4, false),
            'donors' => $this->faker->randomNumber(4, false),
            "dollars" => $this->faker->randomFloat(),
        ];
    }
}
