<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0212_CampaignPledge_5981_12581 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        echo 'Before change:' . PHP_EOL;
        $data = DB::table('pledges')
                    ->whereRaw("id in (12625, 12581) and deleted_at is null;")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

        echo PHP_EOL;
        echo 'Before change  (Donation -- 080964) :' . PHP_EOL;
        $data = DB::table('donations')
                    ->whereRaw("org_code = 'BCS' and pecsf_id = '080964' ")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

        echo PHP_EOL;
        echo 'Before change: (Donation -- 136016)' . PHP_EOL;
        $data = DB::table('donations')
                    ->whereRaw("org_code = 'LA' and pecsf_id = '136016' ")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

        // Data Fix
        /*
            Tran ID         PECSF ID               YEAR    

            12625           136016 --> 135016      2025        
            12581           080964 --> 081027      2025      

        */
        DB::update("update pledges set pecsf_id = '135016', 
                           updated_at = now() 
                     where id = 5981 and pecsf_id = '136016' and deleted_at is null;");

        DB::update("update donations set pecsf_id = '135016', 
                     updated_at = now() 
                     where org_code = 'LA' and pecsf_id = '136016';");

        /* ======================= */
        DB::update("update pledges set pecsf_id = '081027', 
                     updated_at = now() 
               where id = 12581 and pecsf_id = '080964' and deleted_at is null;");

        DB::update("update donations set pecsf_id = '081027', 
                    updated_at = now() 
                    where org_code = 'BCS' and pecsf_id = '080964';");

        echo PHP_EOL;
        echo PHP_EOL;
        echo 'After change:' . PHP_EOL;
        $data = DB::table('pledges')
                    ->whereRaw("id in (12625, 12581) and deleted_at is null;")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

        echo PHP_EOL;
        echo 'After change  (Donation -- 081027) :' . PHP_EOL;
        $data = DB::table('donations')
                    ->whereRaw("org_code = 'BCS' and pecsf_id = '081027' ")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

        echo PHP_EOL;
        echo 'After change: (Donation -- 135016)' . PHP_EOL;
        $data = DB::table('donations')
                    ->whereRaw("org_code = 'LA' and pecsf_id = '135016' ")
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

    }
}
