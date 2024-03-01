<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EmployeeJobFactory extends Factory
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
            "organization_id" => $this->faker->randomNumber(3, false),
            "emplid" => $this->faker->regexify('/^[0][0-9]{5}$/'),
            "empl_rcd" => 0,
            "effdt" =>  $this->faker->date(),
            "effseq" => 0,
            "empl_status" => "A",
            "empl_class" => "B",
            "empl_ctg" => "R",
            "job_indicator" => "P",
            "position_number" => $this->faker->regexify('/^[0-9]{8}$/'), 
            "position_title" => $this->faker->words(3, true),
            "appointment_status" => "Regular",
            "first_name" => $this->faker->word(),
            "last_name" => $this->faker->word(),
            "name" => $this->faker->words(2, true),
            "email" => $this->faker->email(),
            "guid" => uniqid(),
            "idir" => $this->faker->word(),
            "business_unit" => $this->faker->regexify('/^BC[0-9]{3}$/'), 
            "business_unit_id" => $this->faker->randomNumber(3, false),
            "deptid" => $this->faker->regexify('/^[0-9]{3}-[0-9]{4}$/'), 
            "dept_name" => $this->faker->words(2, true),
            "tgb_reg_district" =>  $this->faker->regexify('/^[0][0-9]{2}$/'),
            "region_id" => $this->faker->randomNumber(3, false),
            "office_address1" => $this->faker->address(),
            "office_address2" => $this->faker->address(),
            "office_city" => $this->faker->city(),
            "office_stateprovince" => $this->faker->regexify('/^[A-Z]{2}$/'),
            "office_country" => "CAN",
            "office_postal" =>  $this->faker->regexify('/^[A-Z]\d[A-Z]\ {1}\d[A-Z]\d$/'),
            "address1" => $this->faker->address(),
            "address2" => $this->faker->address(),
            "city" => $this->faker->city(),
            "stateprovince" => $this->faker->regexify('/^[A-Z]{2}$/'),
            "country" => "CAN",
            "postal" =>  $this->faker->regexify('/^[A-Z]\d[A-Z]\ {1}\d[A-Z]\d$/'),
            "organization" => $this->faker->words(2, true),
            "level1_program" => $this->faker->words(2, true),
            "level2_division" => $this->faker->words(3, true),
            "level3_branch" => $this->faker->words(3, true),
            "level4" => $this->faker->words(3, true),
            "supervisor_emplid" =>  $this->faker->regexify('/^[0][0-9]{5}$/'),
            "supervisor_name" => $this->faker->words(2, true),
            "supervisor_email" => $this->faker->email(),
            "date_updated" => $this->faker->date(),
            "date_deleted" => null,
            
            'created_by_id' => 1,
            'updated_by_id' => 1,
        ];
    }
}
