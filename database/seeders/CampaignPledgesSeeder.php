<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CampaignPledgesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $generated_count = 100;
        $yearcd = 2023;

        //
        $faker = Factory::create();

        // prepare some static data 
        $pool_ids = \App\Models\FSPool::pluck('id')->all();
        $cy_id  = \App\Models\CampaignYear::where('calendar_year', $yearcd)->pluck('id')->first();
        $gov_org_id = \App\Models\Organization::where('code', 'GOV')->pluck('id')->first();

        $active_charity_ids = \App\Models\Charity::where('charity_status', 'Registered')->pluck('id')->shuffle()->take( $generated_count - ($generated_count / 50) );
        $inactive_charity_ids = \App\Models\Charity::where('charity_status', '<>', 'Registered')->pluck('id')->shuffle()->take(  ($generated_count / 50) );
        $charity_id_list = $active_charity_ids->merge($inactive_charity_ids);

        $active_user_ids = \App\Models\User::whereNotNull('guid')->where('acctlock','0')
                        ->whereNotExists(function ($query) use($gov_org_id, $cy_id) {
                            $query->select(DB::raw(1))
                                ->from('pledges')
                                ->where('pledges.organization_id', $gov_org_id)
                                ->whereColumn('pledges.user_id','users.id')
                                ->where('pledges.campaign_year_id', $cy_id);
                        })
                    ->pluck('id')->shuffle()->take( $generated_count - ($generated_count / 50) );

        $inactive_user_ids = \App\Models\User::whereNotNull('guid')->where('acctlock','1')
                    ->whereNotExists(function ($query) use($gov_org_id, $cy_id) {
                        $query->select(DB::raw(1))
                            ->from('pledges')
                            ->where('pledges.organization_id', $gov_org_id)
                            ->whereColumn('pledges.user_id','users.id')
                            ->where('pledges.campaign_year_id', $cy_id);
                    })
                    ->pluck('id')->shuffle()->take( $generated_count / 50 );

        $user_ids = $active_user_ids->merge($inactive_user_ids);
        
        // echo $user_ids;

        foreach($user_ids as $idx => $user_id) {

            $type = $faker->randomElement(['P','C']);

            $f_s_pool_id = 0;
            if ($type == 'P')
                $f_s_pool_id = $faker->randomElement( $pool_ids );
    
            $one_time_amount = $faker->randomElement([0, 4, 6,8,9,10,12,20,30,40,50, 100, 200, 300,400,500,600,700,800,900,1000]);
            $pay_period_amount = $faker->randomElement([0, 4, 6,8,9,10,12,20,30,40,50,100, 120, 140, 160, 180, 200,220,240,260,280,300]);
            
            $pay_period_annual_amt = $pay_period_amount * 26;

            $goal_amount = $one_time_amount + $pay_period_annual_amt;

            // echo        $type . PHP_EOL;             
            // echo        $f_s_pool_id . PHP_EOL;             
            // echo        $one_time_amount . PHP_EOL;             
            // echo        $pay_period_amount . PHP_EOL;             
            // echo        $goal_amount . PHP_EOL;     
        
            $pledge = \App\Models\Pledge::Create([
                'organization_id' => $gov_org_id,
                'user_id' => $user_id,
                'campaign_year_id' => $cy_id,
                'type' => $type,
                'f_s_pool_id' => $f_s_pool_id,
                'one_time_amount' => $one_time_amount,
                'pay_period_amount' => $pay_period_amount ,
                'goal_amount' => $goal_amount,
                'created_by_id' => 999,
                'updated_by_id' => 999,
            ]);

            echo $idx . ' - ' . json_encode( $pledge) . PHP_EOL;

            $pledge->charities()->delete();

            if ($type == 'C') {

                $count =  $faker->randomDigitNotNull();
                $percentages = $this->random_numbers_sum( $count, 100);

                $charity_ids = $charity_id_list->shuffle();

                // echo $charity_ids[0] . PHP_EOL;
                // echo $count . PHP_EOL; 
                // echo implode(', ', $percentages) . PHP_EOL; 

                $one_time_sum = 0;
                $one_time_goal_sum = 0;
                $pay_period_sum = 0;
                $pay_period_goal_sum = 0;

                foreach( $percentages as $index => $percent) {

                    $additional =  $faker->text(30);

                    $new_one_time = round( $percent * $one_time_amount /100, 2);
                    $new_one_time_goal = round( $percent * $one_time_amount /100, 2);
                    $new_pay_period = round( $percent * $pay_period_amount /100, 2);
                    $new_pay_period_goal = round( $percent * $pay_period_annual_amt /100, 2);

                    if ($index == ($count - 1)) {
                        $new_one_time = round($one_time_amount - $one_time_sum, 2);
                        $new_one_time_goal = round($one_time_amount - $one_time_goal_sum, 2);
                        $new_pay_period = round($pay_period_amount - $pay_period_sum, 2);
                        $new_pay_period_goal = round($pay_period_annual_amt - $pay_period_goal_sum, 2);
                    } 

                    if ($one_time_amount > 0 ) {

                        \App\Models\PledgeCharity::create([
                            'charity_id' => $charity_ids[$index],
                            'pledge_id' => $pledge->id,
                            'frequency' => 'one-time',
                            'additional' => $additional,
                            'percentage' => $percent,
                            'amount' => $new_one_time,
                            'goal_amount' => $new_one_time_goal,
                        ]);

                    }

                    if ($pay_period_amount > 0 ) {

                        \App\Models\PledgeCharity::create([
                            'charity_id' => $charity_ids[$index],
                            'pledge_id' => $pledge->id,
                            'frequency' => 'bi-weekly',
                            'additional' => $additional,
                            'percentage' => $percent,
                            'amount' => $new_pay_period,
                            'goal_amount' => $new_pay_period_goal,
                        ]);

                    }

                    $one_time_sum += $new_one_time;
                    $one_time_goal_sum += $new_one_time_goal;
                    $pay_period_sum += $new_pay_period;
                    $pay_period_goal_sum += $new_pay_period_goal;

                }


            }

        }

    }


    private function random_numbers_sum($num_numbers=3, $total=500)
    {
        $numbers = [];

        $loose_pcc = $total / $num_numbers;

        for($i = 1; $i < $num_numbers; $i++) {
            // Random number +/- 10%
            $ten_pcc = $loose_pcc * 0.1;
            $rand_num = mt_rand( ($loose_pcc - $ten_pcc), ($loose_pcc + $ten_pcc) );

            $numbers[] = $rand_num;
        }

        // $numbers now contains 1 less number than it should do, sum 
        // all the numbers and use the difference as final number.
        $numbers_total = array_sum($numbers);

        $numbers[] = $total - $numbers_total;

        return $numbers;
    }
}
