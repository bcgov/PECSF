<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0218_DonateNowPledges_298_299 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reassign charity on Evenet Pledge 173 
        
        echo 'Before change:';
        $data = DB::table('donate_now_pledges')
                    ->whereRaw("id between 298 and 299 and emplid in ('108054') and deleted_at is null;")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

        // Data Fix
        /*
            Tran ID         EE                          Deduct Pay      Actual Deduct Pay Date  

            298             108054     2025 --> 2024      2025-03-28  --> 2024-11-02       
            299             108054     2025 --> 2024      2025-03-28  --> 2024-11-02       

        */
        DB::update("update donate_now_pledges set yearcd = 2024, deduct_pay_from = '2024-11-02', 
                           ods_export_status = 'C', ods_export_at = now(),
                           updated_at = now() 
                     where id between 298 and 299 and emplid in ('108054') and deleted_at is null;");
        
        echo PHP_EOL;
        echo PHP_EOL;
        echo 'After change:';
        $data = DB::table('donate_now_pledges')
                    ->whereRaw("id between 298 and 299 and emplid in ('108054') and deleted_at is null;")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

    }
}
