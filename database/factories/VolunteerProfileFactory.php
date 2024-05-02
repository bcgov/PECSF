<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VolunteerProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $province_list = \App\Models\VolunteerProfile::PROVINCE_LIST;
        $role_list = \App\Models\VolunteerProfile::ROLE_LIST;

        $address_type = $this->faker->randomElement( ['G', 'S'] );

        return [

            'campaign_year' => $this->faker->year(),
            'organization_code' => $this->faker->randomElement(['LA', 'BCA', 'GOV', 'RET']),
            'emplid' => $this->faker->randomNumber(6,true),
            'pecsf_id' => $this->faker->randomNumber(6,true),
            'first_name' => $this->faker->words(1, true),
            'last_name' => $this->faker->words(1, true), 
            'employee_city_name' => $this->faker->city(),
            'employee_bu_code' => $this->faker->regexify('BC[0-9]{3}'),
            'employee_region_code' => $this->faker->randomNumber(3,true),
            'business_unit_code' => $this->faker->regexify('BC[0-9]{3}'),
            'no_of_years' => $this->faker->numberBetween(1, 50),
            'preferred_role' => $this->faker->randomElement( array_keys($role_list) ),
            'address_type' =>  $address_type,
            'address' => $address_type == 'G' ? null : substr($this->faker->address(), 0, 60),
            'city' => $address_type == 'G' ? null : $this->faker->city(),
            'province' => $address_type == 'G' ? null : $this->faker->randomElement( array_keys($province_list) ),
            'postal_code' => $address_type == 'G' ? null : $this->faker->regexify('/^[A-Z]\d[A-Z]\ {1}\d[A-Z]\d$/'),
            'opt_out_recongnition' => $this->faker->randomElement( ['Y', 'N'] ),

        ];
    }
}
