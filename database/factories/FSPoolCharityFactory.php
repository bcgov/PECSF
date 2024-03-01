<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class FSPoolCharityFactory extends Factory
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
            'f_s_pool_id' => $this->faker->numberBetween(1,100),
            'charity_id'    => $this->faker->numberBetween(1,100),
            'percentage'    => $this->faker->numberBetween(1,100),
            'status'        => $this->faker->randomElement(['A', 'I']),
            'name'          =>  $this->faker->words(2, true),
            'description'   => $this->faker->sentence(),
            'contact_title' => $this->faker->words(2, true),
            'contact_name'  => $this->faker->words(2, true),
            'contact_email' => $this->faker->email(),
            'notes'         => $this->faker->sentence(),
            'image'         => $this->faker->words(2, true) . '.jpg',
        ];
    }
}
