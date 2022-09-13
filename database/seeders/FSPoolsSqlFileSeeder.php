<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FSPoolsSqlFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = public_path('database/seeds/fspools.sql');
        if (env('APP_ENV') == 'local') {
            $path = public_path('..\database\seeds\fspools.sql');
        }
        $sql = file_get_contents($path);
        DB::unprepared($sql);
    }
}
