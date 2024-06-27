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

    }
}
