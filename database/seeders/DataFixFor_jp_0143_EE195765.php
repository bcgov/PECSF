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
        DB::update("update pledges set pay_period_amount = 50, goal_amount = 1350, updated_at = now() 
                       where id = 271 and deleted_at is null");
        DB::update("delete from pledge_charities where pledge_id = 271 and frequency = 'bi-weekly' and deleted_at is null");
        DB::update("insert into pledge_charities (charity_id, pledge_id, frequency,	additional,	percentage,	amount,	goal_amount, created_at, updated_at) 
                    select charity_id, pledge_id, 'bi-weekly', additional, percentage, 50 * (percentage / 100), 50 * (percentage / 100) * 26, now(), now() 
                        from pledge_charities where pledge_id = 271 and frequency = 'one-time' and deleted_at is null");
    }
}
