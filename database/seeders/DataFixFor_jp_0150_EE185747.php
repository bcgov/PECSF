<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0150_EE185747 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pledge 6184 (change from one-time to bi-weekly)
        DB::update("update pledges set one_time_amount = 0.00, pay_period_amount = 73.84, goal_amount = 1919.84, updated_at = now() where id = 6184 and deleted_at is null;");
        DB::update("update pledge_charities set frequency = 'bi-weekly', amount = 73.84, goal_amount = 1919.84, updated_at = now() where pledge_id = 6184 and frequency = 'one-time' and deleted_at is null");
    }
}
