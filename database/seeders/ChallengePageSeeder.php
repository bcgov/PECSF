<?php

namespace Database\Seeders;

use App\Models\BusinessUnit;
use App\Models\HistoricalChallengePage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChallengePageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      /*  BusinessUnit::where("code", "GCPE")
            ->update(['code' => "BC022"]);
        BusinessUnit::where("code", "EMBC")
            ->update(['code' => "BC010"]);

        BusinessUnit::where("code", "BC131")
            ->update(['code' => "BC105"]);

        BusinessUnit::where("code", "BC067")
            ->update(['code' => "BC112"]);

        BusinessUnit::where("code", "BC063")
            ->update(['code' => "BC062"]);
*/



        DB::table('historical_challenge_pages')->truncate();

        $years = [2021,2020,2019,2018];

        foreach($years as $year){

            $file = fopen(database_path('seeds/'.$year.".csv"),"r");
            while (($data = fgetcsv($file)) !== FALSE)
            {

                HistoricalChallengePage::create([
                    "organization_name" => $data[0],
                    "participation_rate" => $data[1],
                    "previous_participation_rate" => $data[2],
                    "change" => $data[3],
                    "donors" => $data[4],
                    "dollars" => $data[5],
                    "year" => $year
                ]);

            }
        }




    }
}
