<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0210_DonateNowPledges_245_246 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reassign charity on Evenet Pledge 173 
        
        echo 'Before change:';
        $data = DB::table('donate_now_pledges')
                    ->whereRaw("id between 245 and 246 and emplid in ('168370') and deleted_at is null;")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

        // Data Fix
        /*
            Tran ID         EE                          Deduct Pay      Actual Deduct Pay Date  

            245             168370   2025 --> 2024      2025-03-31  --> 2024-12-14       
            246             168370   2025 --> 2024      2025-03-31  --> 2024-12-14       

        */
        DB::update("update donate_now_pledges set yearcd = 2024, deduct_pay_from = '2024-12-14', 
                           ods_export_status = 'C', ods_export_at = now(),
                           updated_at = now() 
                     where id between 245 and 246 and emplid in ('168370') and deleted_at is null;");
        
        echo PHP_EOL;
        echo PHP_EOL;
        echo 'After change:';
        $data = DB::table('donate_now_pledges')
                    ->whereRaw("id between 245 and 246 and emplid in ('168370') and deleted_at is null;")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

    }
}
