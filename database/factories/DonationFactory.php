<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'org_code' => $this->faker->randomElement(['LDB','BCS', 'LA']),
            'pecsf_id'=> $this->faker->randomNumber(6,true),
            'name'=> $this->faker->words(2, true),
            'yearcd' => $this->faker->year,
            'pay_end_date' =>  $this->faker->date(),
            'source_type' => 10,
            'frequency' => $this->faker->randomElement(['bi-weekly', 'one-time']),
            'amount' =>  $this->faker->randomFloat(2, 0.01, 1000),
            'process_history_id' => $this->faker->randomNumber(3,true),
        ];
    }
}
