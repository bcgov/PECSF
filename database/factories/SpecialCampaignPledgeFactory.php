<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SpecialCampaignPledgeFactory extends Factory
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
            'yearcd' => $this->faker->year(),
            'seqno' => $this->faker->randomDigitNotNull(),
            'special_campaign_id' => $this->faker->randomNumber(3,false),
            'one_time_amount' => $this->faker->randomFloat(2, 1, 1000),
            'deduct_pay_from' => $this->faker->date(),
            'first_name' => $this->faker->words(1, true),
            'last_name' => $this->faker->words(1, true),
            'city' => $this->faker->city(),

        ];
    }
}
