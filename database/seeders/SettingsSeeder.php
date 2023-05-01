<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
                Setting::create([
                    "challenge_start_date" => Carbon::now(),
                    "challenge_end_date" => Carbon::now(),
                    "volunteer_start_date" => Carbon::now(),
                    "challenge_end_date" => Carbon::now(),
                    "challenge_final_date" => Carbon::now(),
                    "campaign_start_date" => Carbon::now(),
                    "campaign_end_date" => Carbon::now(),
                    "campaign_final_date" => Carbon::now(),
                    "volunteer_language" => Carbon::now()
                ]);
        }





}
