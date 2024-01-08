<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0083 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::update('update bank_deposit_forms set business_unit = (select id from business_units where code = :bu), updated_at = now() where id in (85,93,150,151,152,153)', ['bu'=> 'BC120']);
        DB::update('update bank_deposit_forms set business_unit = (select id from business_units where code = :bu), updated_at = now() where id = 97', ['bu'=> 'BC031']);
    }
}
