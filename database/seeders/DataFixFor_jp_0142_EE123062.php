<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0142_EE123062 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::update("update pledges set one_time_amount = 1125, goal_amount = 5025, updated_at = now() where id = 1837");
        DB::update("delete from pledge_charities where pledge_id = 1837 and frequency = 'one-time' and deleted_at is null");
        DB::update("insert into pledge_charities (charity_id, pledge_id, frequency,	additional,	percentage,	amount,	goal_amount, created_at, updated_at) 
                    select charity_id, pledge_id, 'one-time', additional, percentage, 1125 * (percentage / 100), 1125 * (percentage / 100), now(), now() 
                        from pledge_charities where pledge_id = 1837 and deleted_at is null");
    }
}
