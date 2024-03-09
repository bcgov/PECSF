<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PledgeFactory extends Factory
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
            'organization_id' =>  $this->faker->randomNumber(3,false),
            'emplid' => $this->faker->randomNumber(6,true),
            'user_id' =>  $this->faker->randomNumber(3,false),
            'pecsf_id' => $this->faker->randomNumber(6,true),
            'business_unit' => $this->faker->regexify('BC[0-9]{3}'),
            'tgb_reg_district' => $this->faker->randomNumber(3,true),
            'deptid' => $this->faker->regexify('[0-9]{3}-[0-9]{4}'),
            'dept_name' => $this->faker->words(2, true),
            'first_name' => $this->faker->words(1, true),
            'last_name' => $this->faker->words(1, true),
            'city' => $this->faker->city(),
            'campaign_year_id' =>  $this->faker->randomNumber(3,false),
            'type' => $this->faker->randomElement(['P','C']),
            'region_id' => $this->faker->randomNumber(3,false),
            'f_s_pool_id' => $this->faker->randomNumber(3,false),
            'one_time_amount' => $this->faker->randomFloat(2, 0.01, 1000),
            'pay_period_amount' => $this->faker->randomFloat(2, 0.01, 50),
            'goal_amount' => $this->faker->randomFloat(2, 0.01, 1000),
        ];
    }
}
