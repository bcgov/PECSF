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

        for ($x = 1990; $x <= 2021; $x++) {
        
          CampaignYear::updateOrCreate([
            'calendar_year' => $x,
          ],[  
            'calendar_year' => $x,
            'number_of_periods' =>  26,
            'status' => 'I',
            'start_date' => Carbon::create($x -1, '09', '01'),
            'end_date' => Carbon::create($x -1, '11', '30'),
            'close_date' => Carbon::create($x , '12', '31'),
            'created_by_id' => 1,
            'modified_by_id' => 1,
          ]);

        }  
        
    }
}
