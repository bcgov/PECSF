<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0167_EE185747 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pledge 6184 (setup both one-time to bi-weekly)
        DB::update("update pledges set one_time_amount = 73.84, pay_period_amount = 73.84, goal_amount = 1993.68, updated_at = now() where id = 6184 and deleted_at is null;");
        DB::update("update pledge_charities set updated_at = now(), deleted_at = now() where pledge_id = 6184 and deleted_at is null");
        DB::update("INSERT INTO pledge_charities (charity_id,pledge_id,frequency,additional,percentage,amount,goal_amount,created_at,updated_at) 
                    VALUES (79001, 6184,'bi-weekly',NULL,100.00,73.84,1919.84,now(), now())");
        DB::update("INSERT INTO pledge_charities (charity_id,pledge_id,frequency,additional,percentage,amount,goal_amount,created_at,updated_at) 
                    VALUES (79001, 6184,'one-time',NULL,100.00,73.84,73.84,now(), now())");
    }
}
