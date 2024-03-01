<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CharityFactory extends Factory
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
            'registration_number' => $this->faker->unique()->numberBetween(100000000,999999999) . 'R0001',
            'charity_name' => $this->faker->sentence(),
            'charity_status' => $this->faker->randomElement(['Registered', 'Revoked-Voluntary', 'Revoked-Failure to File',
                                'Annulled', 'Revoked-Audited', 'No-CRA-match', 'Revoked-Other']),
            'type_of_qualified_donee' => $this->faker->randomElement(['Charity','CAAA', 'NASO','']),
            'effective_date_of_status' => $this->faker->date(), 
            'sanction' => $this->faker->randomElement(['', 'Suspended', 'Penalized', 'designation_code']),
            'charity_type' => $this->faker->randomElement(['','Other purposes beneficial to the community', 'Advancement of Religion', 
                                    'Relief of Poverty', 'Advancement of Education']),
            'category_code' => $this->faker->regexify('[0-9]{4}'),
            'address' => substr($this->faker->address(), 0, 60),
            'city' => $this->faker->city(),
            'province' => $this->faker->regexify('/^[A-Z]{2}$/'),
            'country' => $this->faker->countryCode(),
            'postal_code' => $this->faker->regexify('/^[A-Z]\d[A-Z]\ {1}\d[A-Z]\d$/'),
            'phone' => $this->faker->phoneNumber(),
            'fax' => $this->faker->phoneNumber(),
    
            'use_alt_address' => $this->faker->randomElement(["0","1"]), 
            'alt_address1' => substr($this->faker->address(), 0, 60),
            'alt_address2' => substr($this->faker->address(), 0, 60), 
            'alt_city' => $this->faker->city(),
            'alt_province' => $this->faker->regexify('/^[A-Z]{2}$/'),
            'alt_country' => $this->faker->countryCode(),
            'alt_postal_code' => $this->faker->regexify('/^[A-Z]\d[A-Z]\ {1}\d[A-Z]\d$/'),
            'financial_contact_name' => $this->faker->words(2, true),
            'financial_contact_title' => $this->faker->words(2, true),
            'financial_contact_email' => $this->faker->email(),
            'comments' => $this->faker->sentence(),
            
            'created_by_id' => 1,
            'updated_by_id' => 1,
        ];
    }
}
