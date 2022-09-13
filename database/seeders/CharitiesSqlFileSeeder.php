<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CharitiesSqlFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $path = public_path('database/seeds/charities.sql');
        if (env('APP_ENV') == 'local') {
            $path = public_path('..\database\seeds\charities.sql');
        }
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
