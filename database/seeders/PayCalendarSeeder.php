<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\PayCalendar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;



class PayCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // User Creation

        $start_date = Carbon::createFromDate(2023, 12, 03);

        $from_year = $start_date->year;
        $to_year = today()->year + 1;


        $date = $start_date;
        while ($date->year <= $to_year) {

          PayCalendar::create([
            'pay_begin_dt' => $date, 
            'pay_end_dt' => $date->copy()->addDays(13),
            'check_dt' => $date->copy()->addDays(19),
            'close_dt' => $date->copy()->addDays(13),
          ]);

          $date = $date->copy()->addDays(14);

        }

    }
}
