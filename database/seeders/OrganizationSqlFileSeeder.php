<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationSqlFileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $path = base_path('database/seeds/organizations.sql');
        if (env('APP_ENV') == 'local') {
            $path = base_path('..\database\seeds\organizations.sql');
        }
        $sql = file_get_contents($path);
        DB::unprepared($sql);
}
}
