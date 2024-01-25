<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0087_reset_approval extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::update("update bank_deposit_forms set pecsf_id = null, approved = 0, approved_by_id = null, approved_at = null, updated_at = now() where SUBSTRING( pecsf_id, 2, 2) = '24'");
    }
}
