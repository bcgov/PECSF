<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DataFixFor_jp_0170_EE080753  extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo 'Before change:';
        $data = \App\Models\Pledge::with('charities')
                    ->whereRaw('id = 5710 and deleted_at is null')
                    ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

        // Pledge 5710 (switch to one-time from bi-weekly)
        DB::update("update pledges set one_time_amount = 800.00, pay_period_amount = 0.00, goal_amount = 800.00, updated_at = now() 
                            where id = 5710 and deleted_at is null;");
        DB::update("update pledge_charities set updated_at = now(), deleted_at = now() where pledge_id = 5710 and deleted_at is null");

        DB::update("INSERT INTO pledge_charities (charity_id,pledge_id,frequency,additional,percentage,amount,goal_amount,created_at,updated_at,deleted_at) VALUES
	                    (103479,5710,'one-time',NULL,75.00,600.0,600.0,now(),now(),NULL),
	                    (89805,5710,'one-time',NULL,25.00,200.0,200.0,now(),now(),NULL);
                   ");

        echo PHP_EOL;
        echo PHP_EOL;
        echo 'After change:';
        $data = \App\Models\Pledge::with('charities')
                ->whereRaw('id = 5710 and deleted_at is null')
                ->get();
        echo json_encode($data, JSON_PRETTY_PRINT);

    }
}
