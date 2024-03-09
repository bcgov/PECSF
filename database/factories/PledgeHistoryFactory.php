<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PledgeHistoryFactory extends Factory
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
            'campaign_type' => $this->faker->randomElement(['Annual','Donate Today', 'Event']),
            'source' => $this->faker->randomElement(['Pool','Non-Pool']),
            'tgb_reg_district' => $this->faker->regexify('[0][0-9]{2}'),
            'region_id' => $this->faker->randomNumber(3,false),
           
            'charity_bn' => $this->faker->unique()->numberBetween(100000000,999999999) . 'R0001',
            'charity_id' => $this->faker->randomNumber(3,false),
            'yearcd' => $this->faker->year(),
            'campaign_year_id' => $this->faker->randomNumber(3,false),
            'name1' => $this->faker->words(3, true),
            'name2' => $this->faker->words(3, true),        // Region Name
            'emplid' => $this->faker->randomNumber(6,true),
            'GUID' => uniqid(),
            'vendor_id' => $this->faker->regexify('FS[0-9]{4}'),
            'additional_info' => $this->faker->words(3, true),
            'frequency' =>  $this->faker->randomElement(['Bi-Weekly','One-Time']),
            'per_pay_amt' => $this->faker->randomFloat(2, 0.01, 20),
            'percent' => $this->faker->randomFloat(2, 0, 100),
            'amount' => $this->faker->randomFloat(2),
            'vendor_name1' => $this->faker->words(3, true),
            'vendor_name2' => $this->faker->words(3, true),
            'vendor_bn' => $this->faker->unique()->numberBetween(100000000,999999999) . 'R0001',
            'remit_vendor' => $this->faker->regexify('FS[0-9]{4}'),
            'deptid' => $this->faker->regexify('[0-9]{3}-[0-9]{4}'),
            'city' => $this->faker->city(),
            'business_unit' => $this->faker->regexify('BC[0-9]{3}'),
            'event_descr' => $this->faker->words(3, true),
            'event_type' =>  $this->faker->randomElement(['Cash','Gaming', 'Other', 'Personal Cheque', 'Fund Raiser Event']),
            'event_sub_type' => $this->faker->randomElement(['Auctions','50/50 Draw', 'Bingo', 'Food', 'Other']),
            'event_deposit_date' =>  $this->faker->date(),



        ];
    }
}
