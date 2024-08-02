<?php

namespace Database\Seeders;

use App\Models\BusinessUnit;
use App\Models\HistoricalChallengePage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoricalChallengePageData_2023 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $year = 2023;

        HistoricalChallengePage::where('year', $year)->delete();

        $file = fopen(database_path('seeds/'. $year . '_final.csv'),"r");
        while (($data = fgetcsv($file)) !== FALSE)
        {

            HistoricalChallengePage::create([
                "business_unit_code" => $data[0],
                "organization_name" => $data[1],
                "participation_rate" => $data[2],
                "previous_participation_rate" => $data[3],
                "change" => $data[4],
                "donors" => $data[5],
                "dollars" => $data[6],
                "year" => $year
            ]);

        }

        // recalcuate partipation rate and change
        DB::update("update historical_challenge_pages
            set participation_rate =  round(donors / (select ee_count from eligible_employee_by_bus where campaign_year = 2023 and business_unit_code = historical_challenge_pages.business_unit_code) * 100,2)
            , `change` = round((donors / (select ee_count from eligible_employee_by_bus where campaign_year = 2023 and business_unit_code = historical_challenge_pages.business_unit_code) * 100) -  previous_participation_rate, 2)  
            where (select ee_count from eligible_employee_by_bus where campaign_year = 2023 and business_unit_code = historical_challenge_pages.business_unit_code) > 0
                and year = 2023;");

    }
}
