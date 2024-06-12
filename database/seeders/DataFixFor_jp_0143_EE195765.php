<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0143_EE195765 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::update("update pledges set one_time_amount = 0, pay_period_amount = 50, goal_amount = 1300, updated_at = now() 
                       where id = 271 and deleted_at is null");
        DB::update("update pledge_charities set frequency = 'bi-weekly', amount = 50, goal_amount = 1300, updated_at = now() 
                        where pledge_id = 271 and deleted_at is null");

    }
}
