<?php

namespace Database\Seeders;

use bfinlay\SpreadsheetSeeder\SpreadsheetSeeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CharitySeeder extends SpreadsheetSeeder
{

    public function __construct()
    {

    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->file = [
            '/database/seeds/*.xlsx'
        ];



        $this->aliases = [
            "BN/Registration Number" => "registration_number",
            "Charity Name" => Str::snake("Charity Name"),
            "Charity Status" => Str::snake("Charity Status"),
            "Effective Date of Status" => Str::snake("Effective Date of Status"),
            "Sanction" => Str::snake("Sanction"),
            "Designation Code" => Str::snake("Designation Code"),
            "Category Code" => Str::snake("Category Code"),
            "Address" => Str::snake("Address"),
            "City" => Str::snake("City"),
            "Province" => Str::snake("Province"),
            "Country" => Str::snake("Country"),
            "Postal Code" => Str::snake("Postal Code")
        ];

        $this->batchInsertSize = 10;

        $this->textOutput = false;

        parent::run();
    }
}