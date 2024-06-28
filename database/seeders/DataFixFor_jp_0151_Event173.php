<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0151_Event173 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reassign charity on Evenet Pledge 173 
        
        echo 'Before change:';
        $data = DB::table('bank_deposit_form_organizations')
                    ->whereRaw('id = 352 and deleted_at is null')
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

        // Data Fix
        DB::update("update bank_deposit_form_organizations set organization_name = 'First Nations Health Foundation', vendor_id = 133718, updated_at = now() where id = 352 and bank_deposit_form_id = 173 and deleted_at is null;");

        echo PHP_EOL;
        echo PHP_EOL;
        echo 'After change:';
        $data = DB::table('bank_deposit_form_organizations')
                    ->whereRaw('id = 352 and deleted_at is null')
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

    }
}
