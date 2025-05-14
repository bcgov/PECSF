<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PayCalendarFactory extends Factory
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
            'pay_end_dt' => $this->faker->date(),
            'pay_begin_dt' =>  $this->faker->date(),
            'check_dt' => $this->faker->date(),
            'close_dt' => $this->faker->date(),
        ];
    }
}
