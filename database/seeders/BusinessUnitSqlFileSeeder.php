<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessUnitSqlFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $path = base_path('database/seeds/business_units.sql');
        if (env('APP_ENV') == 'local') {
            $path = base_path('../database/seeds/business_units.sql');
        }
        $sql = file_get_contents($path);
        DB::unprepared($sql);
}
}
