<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\CampaignYear;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CampaignYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        for ($x = 2005; $x <= 2030; $x++) {
          
          $periods = 26;
          if ($x == 2015 or $x == 2027) {
              $periods = 27;
          }

          CampaignYear::updateOrCreate([
            'calendar_year' => $x,
          ],[  
            'calendar_year' => $x,
            'number_of_periods' => $periods,
            'status' => $x == 2023 ? 'A' : 'I',
            'start_date' => Carbon::create($x -1, '09', '01'),
            'end_date' => Carbon::create($x -1, '11', '30'),
            'close_date' => Carbon::create($x , '12', '31'),
            'created_by_id' => 1,
            'modified_by_id' => 1,
          ]);

        }  
        
    }
}
