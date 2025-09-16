<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0232_Pledge_12799 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reassign charity on Evenet Pledge 173 
        
        echo 'Before change:';
        $data = DB::table('pledges')
                    ->whereRaw("id = 12799 and emplid in ('136279') and deleted_at is null;")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

        // Data Fix
        /*
            Backdate deduction for Transaction ID 12799 to 2025

            Tran ID         EE                        

            12799           136279   2026 --> 2025    

        */
        DB::update("update pledges set campaign_year_id = 21,
                            ods_export_status = 'C', ods_export_at = now(),
                            updated_at = now()
                     where id = 12799 and emplid in ('136279') and deleted_at is null;");
        
        echo PHP_EOL;
        echo PHP_EOL;
        echo 'After change:';
        $data = DB::table('pledges')
                    ->whereRaw("id = 12799 and emplid in ('136279') and deleted_at is null;")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

    }
}
