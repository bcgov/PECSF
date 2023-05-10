<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyCampaignView extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'daily_campaign_view';
    
    public function business_unit() {
        return $this->belongsTo(BusinessUnit::class, 'business_unit_code', 'code');
    }

    public static function dynamicSqlForChallengePage() {
    
        $sql = <<<SQL
                    select 1 as current, business_unit_code, organization_name,
                            -- 0 as participation_rate, 
                            case when (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                and eligible_employee_by_bus.organization_code = 'GOV' 
                                and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                                ) > 0 then 
                                    A.donors / (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                        and eligible_employee_by_bus.organization_code = 'GOV' 
                                        and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                                    ) * 100 
                                else 0 end as participation_rate,
                            -- 0 as previous_participation_rate, 
                            (select participation_rate from historical_challenge_pages where year = ?
                                and historical_challenge_pages.organization_name = A.organization_name
                            ) as previous_participation_rate,
                            (A.donors / (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                and eligible_employee_by_bus.organization_code = 'GOV' 
                                and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                            ) * 100) - COALESCE((select participation_rate from historical_challenge_pages where year = ?
                                and historical_challenge_pages.organization_name = A.organization_name
                                ),0)
                            as 'change_rate', 
                            A.donors, A.dollars, (@row_number:=@row_number + 1) AS rank
                            ,(select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                and eligible_employee_by_bus.organization_code = 'GOV' 
                                and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                            ) as ee_count
                     from 
                        (select business_unit_code, name as organization_name, sum(donors) as donors, sum(dollars) as dollars 
                        from daily_campaign_view  
                        left outer join business_units on business_units.code = business_unit_code
                        where campaign_year = ?
                        group by business_unit_code
                        order by sum(donors) desc) 
                        as A, (SELECT @row_number:=0) AS temp
                    where 1=1
                    order by A.donors / (select ee_count from eligible_employee_by_bus where eligible_employee_by_bus.campaign_year = ?
                                        and eligible_employee_by_bus.organization_code = 'GOV' 
                                        and eligible_employee_by_bus.business_unit_code = A.business_unit_code
                                    ) * 100 desc
                                
                SQL;

        return $sql;
    }

}
