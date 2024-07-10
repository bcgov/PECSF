<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0155_DonateNow117 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reassign charity on Evenet Pledge 173 
        
        echo 'Before change:';
        $data = DB::table('donate_now_pledges')
                    ->whereRaw('id = 117 and deleted_at is null')
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

        // Data Fix
        DB::update("update donate_now_pledges set charity_id = 42650, updated_at = now() where id = 117 and deleted_at is null;");
        
        echo PHP_EOL;
        echo PHP_EOL;
        echo 'After change:';
        $data = DB::table('donate_now_pledges')
                    ->whereRaw('id = 117 and deleted_at is null')
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

    }
}
