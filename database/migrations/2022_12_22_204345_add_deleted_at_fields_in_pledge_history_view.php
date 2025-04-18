<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtFieldsInPledgeHistoryView extends Migration
{
    public function up()
    {
        //
        DB::statement($this->dropView());
        DB::statement($this->create_New_View());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        // DB::statement($this->dropView());
        // DB::statement($this->create_Old_View());
    }

    private function create_New_View(): string
    {

        return <<<SQL

            CREATE VIEW pledge_history_view AS

                (select 'GF' as source, pledges.user_id, pledges.id, pledges.emplid, campaign_years.calendar_year as yearcd, type,  
                    'Annual' as donation_type, 'Bi-Weekly' as frequency, pledges.pay_period_amount as amount, pledges.pay_period_amount * campaign_years.number_of_periods  as pledge,
                        (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id = pledges.f_s_pool_id) as region  
                    from pledges, campaign_years
                    where pledges.campaign_year_id = campaign_years.id
                    and pledges.pay_period_amount <> 0 
                    and pledges.deleted_at is null ) 
                union all
                (select 'GF', pledges.user_id, pledges.id, pledges.emplid, campaign_years.calendar_year as yearcd, type,
                    'Annual', 'One-Time', pledges.one_time_amount, pledges.one_time_amount, 
                        (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id =   pledges.f_s_pool_id)
                    from pledges, campaign_years
                    where pledges.campaign_year_id = campaign_years.id
                    and pledges.one_time_amount <> 0
                    and pledges.deleted_at is null )
                union all
                    (select 'GF', donate_now_pledges.user_id, donate_now_pledges.id, donate_now_pledges.emplid, yearcd, type,
                    'Donate Now', 'One-Time', donate_now_pledges.one_time_amount, donate_now_pledges.one_time_amount, 
                        case when type = 'P' then 
                            (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id = donate_now_pledges.f_s_pool_id)
                        else 
                            (select charities.charity_name from charities where donate_now_pledges.charity_id = charities.id )
                        end 
                    from donate_now_pledges 
                    where donate_now_pledges.deleted_at is null) 
                union all
                    (select 'GF', special_campaign_pledges.user_id, special_campaign_pledges.id, special_campaign_pledges.emplid, yearcd, 'C' as 'type',
                        'Special Campaign', 'One-Time', special_campaign_pledges.one_time_amount, special_campaign_pledges.one_time_amount, 
                        (select special_campaigns.name from special_campaigns where special_campaign_pledges.special_campaign_id = special_campaigns.id)
                        from special_campaign_pledges 
                        where special_campaign_pledges.deleted_at is null)                    
                union all
                    (select 'GF', null, bank_deposit_forms.id, bc_gov_id, year(created_at), case when regional_pool_id is null then 'C' else 'P' end as 'type',
                        'Event', 'One-Time', bank_deposit_forms.deposit_amount, bank_deposit_forms.deposit_amount, 
                            (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id = bank_deposit_forms.regional_pool_id)
                        from bank_deposit_forms
                        where bank_deposit_forms.organization_code = 'GOV'
                          and bank_deposit_forms.event_type in ('Cash One-Time Donation','Cheque One-Time Donation')
                          and bank_deposit_forms.approved = 1
                          and bank_deposit_forms.deleted_at is null) 
                union all 
                    (select 'BI', NULL, pledge_history_id, emplid, yearcd, source,  
                        campaign_type, frequency, per_pay_amt, pledge, region 
                        from pledge_history_summaries);

        SQL;

    }

    private function create_Old_View(): string
    {
        return <<<SQL

        CREATE VIEW pledge_history_view AS

            (select 'GF' as source, pledges.user_id, pledges.id, users.emplid, campaign_years.calendar_year as yearcd, type,  
                'Annual' as donation_type, 'Bi-Weekly' as frequency, pledges.pay_period_amount as amount, pledges.pay_period_amount * campaign_years.number_of_periods  as pledge,
                    (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id = pledges.f_s_pool_id) as region  
                from pledges, campaign_years, users     
                where pledges.campaign_year_id = campaign_years.id
                and pledges.user_id = users.id
                and pledges.pay_period_amount <> 0 ) 
            union all
            (select 'GF', pledges.user_id, pledges.id, users.emplid, campaign_years.calendar_year as yearcd, type,
                'Annual', 'One-Time', pledges.one_time_amount, pledges.one_time_amount, 
                    (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id =   pledges.f_s_pool_id)
                from pledges, campaign_years, users
                where pledges.campaign_year_id = campaign_years.id
                and pledges.user_id = users.id
                and pledges.one_time_amount <> 0
                and pledges.deleted_at is null )
            union all
                (select 'GF', donate_now_pledges.user_id, donate_now_pledges.id, users.emplid, yearcd, type,
                'Donate Now', 'One-Time', donate_now_pledges.one_time_amount, donate_now_pledges.one_time_amount, 
                    case when type = 'P' then 
                        (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id = donate_now_pledges.f_s_pool_id)
                    else 
                        (select charities.charity_name from charities where donate_now_pledges.charity_id = charities.id )
                    end 
                from donate_now_pledges, users
                where donate_now_pledges.user_id = users.id
                  and donate_now_pledges.deleted_at is null) 
            union all
                (select 'GF', special_campaign_pledges.user_id, special_campaign_pledges.id, users.emplid, yearcd, 'C' as 'type',
                    'Special Campaign', 'One-Time', special_campaign_pledges.one_time_amount, special_campaign_pledges.one_time_amount, 
                    (select special_campaigns.name from special_campaigns where special_campaign_pledges.special_campaign_id = special_campaigns.id)
                    from special_campaign_pledges, users
                    where special_campaign_pledges.user_id = users.id
                      and special_campaign_pledges.deleted_at is null)                    
            union all
                (select 'GF', null, bank_deposit_forms.id, bc_gov_id, year(created_at), case when regional_pool_id is null then 'C' else 'P' end as 'type',
                    'Event', 'One-Time', bank_deposit_forms.deposit_amount, bank_deposit_forms.deposit_amount, 
                        (select regions.name from f_s_pools, regions where f_s_pools.region_id = regions.id and f_s_pools.id = bank_deposit_forms.regional_pool_id)
                    from bank_deposit_forms
                    where bank_deposit_forms.organization_code = 'GOV'
                      and bank_deposit_forms.event_type in ('Cash One-Time Donation','Cheque One-Time Donation')
                      and bank_deposit_forms.approved = 1
                      and bank_deposit_forms.deleted_at is null) 
            union all 
                (select 'BI', NULL, pledge_history_id, emplid, yearcd, source,  
                    campaign_type, frequency, per_pay_amt, pledge, region 
                    from pledge_history_summaries);

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
