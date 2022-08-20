<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePledgeHistoryView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement($this->dropView());
        DB::statement($this->createView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement($this->dropView());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    private function createView(): string
    {
        return <<<SQL

            CREATE VIEW pledge_history_view AS

            (select 'GF' as source, pledges.id, users.GUID, campaign_years.calendar_year as yearcd, type,  
                'Annual' as donation_type, 'Bi-Weekly' as frequency, pledges.pay_period_amount as amount, pledges.pay_period_amount * campaign_years.number_of_periods  as pledge,
                    (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id = pledges.f_s_pool_id) as region  
                from pledges, campaign_years, users     
                where pledges.campaign_year_id = campaign_years.id
                and pledges.user_id = users.id
                and pledges.pay_period_amount <> 0 ) 
            union all
            (select 'GF', pledges.id, users.GUID, campaign_years.calendar_year as yearcd, type,
                'Annual', 'One-Time', pledges.one_time_amount, pledges.one_time_amount, 
                    (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id =   pledges.f_s_pool_id)
                from pledges, campaign_years, users
                where pledges.campaign_year_id = campaign_years.id
                and pledges.user_id = users.id
                and pledges.one_time_amount <> 0)
            union all
                (select 'GF', donate_now_pledges.id, users.GUID, yearcd, type,
                'Donate Now', 'One-Time', donate_now_pledges.one_time_amount, donate_now_pledges.one_time_amount, 
                    (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id = donate_now_pledges.f_s_pool_id)
                from donate_now_pledges, users
                where donate_now_pledges.user_id = users.id)
            union all 
                (select 'BI', pledge_histories.id, GUID, yearcd, case when source = 'Pool' then 'P' else 'C' end,
                campaign_type, frequency, per_pay_amt, pledge,    
                (select regions.name from regions where pledge_histories.tgb_reg_district  = regions.code)
                from pledge_histories 
                where `campaign_type` not in ('Annual') );

        SQL;

    }
   
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    private function dropView(): string
    {
        return <<<SQL

            DROP VIEW IF EXISTS pledge_history_view;

            SQL;

    }


}
