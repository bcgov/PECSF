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
        $current_year = now()->year;
        $september_first = Carbon::createFromDate($current_year, 9, 1);
        $january_fifteenth = Carbon::createFromDate($current_year + 1, 1, 15);

        Setting::create([
            "challenge_start_date" => $september_first,
            "challenge_end_date" => now(),
            "volunteer_start_date" => $september_first,
            "challenge_final_date" => $january_fifteenth,
            "campaign_start_date" => $september_first,
            "campaign_end_date" => now(),
            "campaign_final_date" => $january_fifteenth,
            "volunteer_language" => now()
        ]);
    }





}
