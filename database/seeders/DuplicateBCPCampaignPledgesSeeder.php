<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\Pledge;
use App\Models\CampaignYear;
use App\Models\Organization;
use App\Models\PledgeCharity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EligibleEmployeeDetail;

class DuplicateBCPCampaignPledgesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $campaign_year = CampaignYear::where('calendar_year', 2024)->first();
        $from_org = Organization::where('code', 'GOV')->first();
        $to_org = Organization::where('code', 'BCP')->first();

        if (!($to_org)) {
            $to_org = Organization::create(
                [   
                    'code' => 'BCP',
                    'name' => 'BC Pension Corp',
                    'status' => 'A',
                    'effdt' => '2024-01-01',
                    'bu_code' => 'BC088',
                ]
            );
        }

        $pledges = Pledge::where('campaign_year_id', $campaign_year->id)
                         ->where('organization_id', $from_org->id)
                         ->where('business_unit', 'BC088')
                         ->whereNull('cancelled_at')
                         ->whereNull('deleted_at')
                         ->where('pay_period_amount', '>', 0)
                         ->orderBy('id')
// ->whereIn('id', [763] )
                         ->get();

        $create_count = 0;
        $skip_count = 0;
        
        foreach ($pledges as $idx => $pledge) {

            echo $idx . ' - ' . json_encode( $pledge, false) . PHP_EOL;

            $rec = Pledge::where('campaign_year_id', $campaign_year->id)
                            ->where('organization_id', $to_org->id)
                            ->whereNull('emplid')
                            ->where('user_id', '=', 0)
                            ->where('pecsf_id', $pledge->emplid)
                            ->first();

            if ($rec) {
                $skip_count++;
                echo PHP_EOL;
                echo '   FAILED - The pledge for new Organization BCP has been already created.' . PHP_EOL;
                echo PHP_EOL;
                
                continue;
            }


            $ee = EligibleEmployeeDetail::where('organization_code', 'GOV')
                                        ->where('emplid', $pledge->emplid)
                                        ->whereIn('year', [2023, 2024])
                                        ->orderBy('year')
                                        ->orderByDesc('as_of_date')
                                        ->first();

            $name = explode(',', $ee ? $ee->name : '');

            // Cloning 

            $new_pledge = $pledge->replicate()->fill([

                'organization_id' => $to_org->id,
                'emplid' => null,
                'user_id' => 0,
                'pecsf_id' => $pledge->emplid,
        
                'last_name'  => $name ? $name[0] : null,
                'first_name' => $name ? $name[1] : null,

                'one_time_amount' => 0, 
                'goal_amount'     => $pledge->goal_amount - $pledge->one_time_amount,

                'ods_export_status' => null,
                'ods_export_at' => null,

                "created_by_id" => null,
                "updated_by_id" => null, 

            ]);

            $new_pledge->save();

            // replicate the relationship
            $row = 0;
            foreach($pledge->charities as $index => $old_charity)
            {

                if ($old_charity->frequency == 'bi-weekly') {

                    $new_pledge_charity = new PledgeCharity();

                    $new_pledge_charity->charity_id =  $old_charity->charity_id;
                    $new_pledge_charity->additional =  $old_charity->additional;
                    $new_pledge_charity->percentage =  $old_charity->percentage;
                    $new_pledge_charity->amount =      $old_charity->amount;
                    $new_pledge_charity->frequency =   $old_charity->frequency;
                    $new_pledge_charity->goal_amount = $old_charity->goal_amount;

                    // $new_pledge->charities[$row] = $new_pledge_charity;
                    $new_pledge->charities()->save($new_pledge_charity);

                    $row += 1;
                }

            }

            $create_count++;
            echo PHP_EOL;
            echo '   SUCCESS - The new pledge for new Organization BCP was created.' . ' - id ' . $new_pledge->id . PHP_EOL;
            echo '   ' . json_encode( $new_pledge, false) . PHP_EOL;
            echo PHP_EOL;

            // var_dump( $new_pledge->charities[0]->toArray() );
           

            // dd( $new_pledge->toArray() );
            
        }

        echo PHP_EOL;
        echo 'Total rows: ' .  $pledges->count();
        echo PHP_EOL;
        echo 'Total created : ' .  $create_count . PHP_EOL;
        echo 'Total skipped : ' .  $skip_count . PHP_EOL;
    }


    
}
