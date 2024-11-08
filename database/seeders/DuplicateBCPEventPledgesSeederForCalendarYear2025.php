<?php

namespace Database\Seeders;

use Faker\Factory;
use App\Models\BusinessUnit;
use App\Models\CampaignYear;
use App\Models\Organization;
use App\Models\BankDepositForm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\BankDepositFormAttachments;
use App\Models\BankDepositFormOrganizations;

class DuplicateBCPEventPledgesSeederForCalendarYear2025 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $campaign_year = CampaignYear::where('calendar_year', 2025)->first();
        $from_org = Organization::where('code', 'GOV')->first();
        $to_org = Organization::where('code', 'BCP')->first();
        
        $business_unit = BusinessUnit::where('code', 'BC088')->first();

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

        $pledges = BankDepositForm::where('campaign_year_id', $campaign_year->id)
                         ->where('organization_code', 'GOV')
                         ->where('business_unit', $business_unit->id)
                         ->where('approved', 1)
                         ->whereNull('deleted_at')
                         ->orderBy('id')
                         ->get();

        $create_count = 0;
        $cancelled_count = 0;
        $skip_count = 0;
        
        foreach ($pledges as $idx => $pledge) {

            echo $idx . ' - ' . json_encode( $pledge, false) . PHP_EOL;

            $rec = BankDepositForm::where('campaign_year_id', $campaign_year->id)
                            ->where('organization_code', $to_org->code)
                            ->where('business_unit', $business_unit->id)
                            ->where('bc_gov_id', $pledge->bc_gov_id)
                            ->where('pecsf_id', $pledge->pecsf_id)
                            ->first();

            if ($rec) {
                $skip_count++;
                echo PHP_EOL;
                echo '   FAILED - The pledge for new Organization BCP has been already created.' . PHP_EOL;
                echo PHP_EOL;
                
                continue;
            }


            // $ee = EligibleEmployeeDetail::where('organization_code', 'GOV')
            //                             ->where('emplid', $pledge->emplid)
            //                             ->whereIn('year', [2023, 2024])
            //                             ->orderBy('year')
            //                             ->orderByDesc('as_of_date')
            //                             ->first();

            // $name = explode(',', $ee ? $ee->name : '');

            // Cloning 

            $new_pledge = $pledge->replicate()->fill([

                'organization_code' => $to_org->code,
        
                "created_by_id" => null,
                "updated_by_id" => null, 

            ]);

            $new_pledge->save();


            // replicate the relationship (Organizations)
            $row = 0;
            foreach($pledge->organizations as $index => $old_org)
            {

                $new_pledge_org = new BankDepositFormOrganizations();

                $new_pledge_org->organization_name =  $old_org->organization_name;
                $new_pledge_org->vendor_id =  $old_org->vendor_id;
                $new_pledge_org->donation_percent =  $old_org->donation_percent;
                $new_pledge_org->specific_community_or_initiative = $old_org->specific_community_or_initiative;

                // $new_pledge->charities[$row] = $new_pledge_charity;
                $new_pledge->organizations()->save($new_pledge_org);

                $row += 1;

            }


            // replicate the relationship (Attachments)
            $row = 0;
            foreach($pledge->attachments as $index => $old_attachment)
            {

                    $new_pledge_attachment = new BankDepositFormAttachments();

                    $new_pledge_attachment->filename =  $old_attachment->filename;
                    $new_pledge_attachment->original_filename =  $old_attachment->original_filename;
                    $new_pledge_attachment->mime =  $old_attachment->mime;
                    $new_pledge_attachment->local_path = $old_attachment->local_path;
                    $new_pledge_attachment->file = $old_attachment->file;

                    // $new_pledge->charities[$row] = $new_pledge_attachment;
                    $new_pledge->attachments()->save($new_pledge_attachment);

                    $row += 1;

            }

            $create_count++;
            echo PHP_EOL;
            echo '   SUCCESS - The new Non-Gov pledge for new Organization BCP was created.' . ' - id ' . $new_pledge->id . PHP_EOL;
            echo '   ' . json_encode( $new_pledge, false) . PHP_EOL;
            echo PHP_EOL;

            // var_dump( $new_pledge->charities[0]->toArray() );

            // To cancelled the old pledge
            $cancelled_count++;
            $pledge->approved = 2;
            $pledge->approved_by_id = null;
            $pledge->approved_at = now();
            $pledge->save();

            echo PHP_EOL;
            echo '   SUCCESS - The old Gov pledge for Organization GOV was unapproved.' . ' - id ' . $pledge->id . PHP_EOL;
            echo '   ' . json_encode( $pledge, false) . PHP_EOL;
            echo PHP_EOL;

            // dd( $new_pledge->toArray() );
            
        }

        echo PHP_EOL;
        echo 'Total rows: ' .  $pledges->count();
        echo PHP_EOL;
        echo 'Total created    : ' .  $create_count . PHP_EOL;
        echo 'Total Unapproved : ' .  $cancelled_count . PHP_EOL;
        echo 'Total skipped    : ' .  $skip_count . PHP_EOL;
    }
    
}
