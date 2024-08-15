<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0169_DonateNowPledges1_to_6 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reassign charity on Evenet Pledge 173 
        
        echo 'Before change:';
        $data = DB::table('donate_now_pledges')
                    ->whereRaw("id between 1 and 6 and emplid in ('010058','064306', '160588') and deleted_at is null;")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

        // Data Fix
        /*
            EE                    Deduct Pay      Actual Deduct Pay Date  

            010058                2024-01-05       2024-01-19 
            064306                2024-01-05       2024-01-19 
            160588                2024-01-05       2024-01-19 
        */
        DB::update("update donate_now_pledges set deduct_pay_from = '2024-01-19', updated_at = now() where id between 1 and 6 and emplid in ('010058','064306', '160588') 
                        and deleted_at is null;");
        
        echo PHP_EOL;
        echo PHP_EOL;
        echo 'After change:';
        $data = DB::table('donate_now_pledges')
                    ->whereRaw("id between 1 and 6 and emplid in ('010058','064306', '160588') and deleted_at is null;")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

    }
}
