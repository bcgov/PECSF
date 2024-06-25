<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0147_update_Plege6185 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::update("update pledges set campaign_year_id = 20, updated_at = now()  where id  = 6185 and campaign_year_id = 21;");
    }
}
