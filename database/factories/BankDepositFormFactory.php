<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankDepositFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $event_type = $this->faker->randomElement(['Cash One-Time Donation', 'Cheque One-Time Donation', 'Gaming', 'Fundraiser']);
        $sub_type = ($event_type == 'Gaming') ? $this->faker->randomElement(['50/50 Draw', 'none']) : 
                        (($event_type == 'Fundraiser') ? $this->faker->randomElement(['Auction', 'Entertainment', 'Food', 'Other', 'Sports', 'none']) : null);
                        
        return [
            //
            'organization_code' =>  $this->faker->randomNumber(3,false),
            'form_submitter_id' =>  $this->faker->randomNumber(3,false),
            'campaign_year_id' => $this->faker->randomNumber(3,false),
            'event_type' =>  $event_type,
            'sub_type' => $sub_type,
            'deposit_date' => Carbon::yesterday(),
            'deposit_amount' => $this->faker->randomFloat(2, 0.01, 1000),
            'description' => $this->faker->words(3, true),
            'employment_city' => $this->faker->city(),
            'region_id'	=>  $this->faker->randomNumber(3,false),
            'department_id' =>  $this->faker->randomNumber(3,false),
            'regional_pool_id' =>  $this->faker->randomNumber(3,false),
            'address_line_1' =>	substr($this->faker->address(), 0, 60),       //substr($this->faker->address(), 0, 60),
            'address_line_2' => substr($this->faker->address(), 0, 60),       //substr($this->faker->address(), 0, 60),
            'address_city' =>  $this->faker->city(),         //$this->faker->city(),
            'address_province' =>  $this->faker->regexify('/^[A-Z]{2}$/'),     //$this->faker->regexify('/^[A-Z]{2}$/'),
            'address_postal_code' => $this->faker->countryCode(),   // $this->faker->regexify('/^[A-Z]\d[A-Z]\ {1}\d[A-Z]\d$/'),

            'pecsf_id' => 'G'. substr(today()->year,2,2) -1 . '001',
            'bc_gov_id'	=>  $this->faker->randomNumber(6,true),
            'business_unit'	=> $this->faker->regexify('BC[0-9]{3}'),
            'deptid' => $this->faker->regexify('[0-9]{3}-[0-9]{4}'),
            'dept_name' => $this->faker->words(2, true),
            'employee_name' => $this->faker->words(2, true),

            'approved' => $this->faker->randomElement([0, 1]),
        ];

    }
}
