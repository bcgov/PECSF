<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Modify5thDailyCampaignView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
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
        DB::statement($this->dropView());
        DB::statement($this->create_Old_View());

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    private function create_New_View(): string
    {
        return <<<SQL

            CREATE VIEW daily_campaign_view AS

                -- Pledge (Gov)
                    (select 'pledges' COLLATE utf8mb4_unicode_ci as type,               
                    pledges.id as tran_id, 
                    employee_jobs.business_unit as business_unit_code, employee_jobs.tgb_reg_district, 
                    employee_jobs.deptid, employee_jobs.dept_name,
                        1 as donors, pledges.goal_amount as dollars,
                        (select calendar_year - 1 from campaign_years where pledges.campaign_year_id = campaign_years.id) as campaign_year,
                        organizations.code as organization_code, pledges.emplid, pledges.pecsf_id,
                        pledges.created_at
                    from pledges
                    left outer join organizations on pledges.organization_id = organizations.id 
                    left outer join employee_jobs on organizations.id = employee_jobs.organization_id and pledges.emplid = employee_jobs.emplid
                    where (employee_jobs.empl_rcd = (select min(J2.empl_rcd) from employee_jobs as J2
                    								 where J2.emplid = employee_jobs.emplid) 
                    		or employee_jobs.empl_rcd is null)                    
                        and pledges.emplid is not null
                        and organizations.code = 'GOV'
                        and pledges.deleted_at is null)
                union all   
                -- Pledge (Non Gov)
                    (select 'pledges' as type, 
                        pledges.id, 
                        business_units.linked_bu_code, 
                        cities.TGB_REG_DISTRICT,'','', 
                        1 as donors, pledges.goal_amount as dollars,   
                        (select calendar_year - 1 from campaign_years where pledges.campaign_year_id = campaign_years.id) as campaign_year,
                        organizations.code, pledges.emplid, pledges.pecsf_id,
                        pledges.created_at
                    from pledges 
                    left outer join organizations on pledges.organization_id = organizations.id and organizations.deleted_at is null
                    left outer join business_units on organizations.bu_code = business_units.code and business_units.deleted_at is null
                    left outer join cities on pledges.city = cities.city 
                    where 1=1 
                        and organizations.code <> 'GOV'
                        and pledges.deleted_at is null)
                -- eForm (Gov)   
                union all    
                    (select 'eform' as type, 
                        bank_deposit_forms.id, 
                        case when event_type in ('Fundraiser', 'Gaming') then business_units.code else employee_jobs.business_unit end, 
                    (select code from regions where regions.id = bank_deposit_forms.region_id),  
                    department_id, '',
                        case when event_type in ('Fundraiser', 'Gaming') then 0 else 1 end,  bank_deposit_forms.deposit_amount as dollars,   
                        (select calendar_year - 1 from campaign_years where bank_deposit_forms.campaign_year_id = campaign_years.id) as campaign_year,
                        bank_deposit_forms.organization_code, bank_deposit_forms.bc_gov_id, bank_deposit_forms.pecsf_id,
                        bank_deposit_forms.created_at
                    from bank_deposit_forms 
                    left outer join business_units on business_units.id = bank_deposit_forms.business_unit and business_units.deleted_at is null                                                
                    -- left outer join eligible_employee_details on bank_deposit_forms.organization_code = eligible_employee_details.organization_code 
                    --                 and bank_deposit_forms.bc_gov_id = eligible_employee_details.emplid
                    -- where as_of_date = (select max(as_of_date) from  eligible_employee_details e1 
                    --                         where 1 = 1 
                    --                         and e1.year = YEAR( CURDATE() ) 
                    --                         and e1.as_of_date <= CURDATE())
                    left outer join organizations on bank_deposit_forms.organization_code = organizations.code and organizations.deleted_at is null
                    left outer join employee_jobs on organizations.id = employee_jobs.organization_id
                                                and bank_deposit_forms.bc_gov_id = employee_jobs.emplid
                    where (employee_jobs.empl_rcd = (select min(J2.empl_rcd) from employee_jobs as J2
                    								 where J2.emplid = employee_jobs.emplid) 
                    		or employee_jobs.empl_rcd is null)                    
                        and organizations.code = 'GOV'
                        and bank_deposit_forms.organization_code = 'GOV'
                        and bank_deposit_forms.bc_gov_id is not null                    
                        and bank_deposit_forms.approved = 1
                        and bank_deposit_forms.deleted_at is null)
                -- eForm (non-Gov)   
                union all    
                    (select 'eform' as type, 
                        bank_deposit_forms.id, 
                        business_units.linked_bu_code, 
                        (select code from regions where regions.id = bank_deposit_forms.region_id),  department_id, '',
                        case when event_type in ('Fundraiser', 'Gaming') then 0 else 1 end,  bank_deposit_forms.deposit_amount as dollars,   
                        (select calendar_year - 1 from campaign_years where bank_deposit_forms.campaign_year_id = campaign_years.id) as campaign_year,
                        organization_code, bank_deposit_forms.bc_gov_id, bank_deposit_forms.pecsf_id,
                        bank_deposit_forms.created_at
                    from bank_deposit_forms 
                    left outer join organizations on bank_deposit_forms.organization_code = organizations.code and organizations.deleted_at is null
                    left outer join business_units on business_units.id = bank_deposit_forms.business_unit and business_units.deleted_at is null
                    where bank_deposit_forms.organization_code <> 'GOV'
                        and bank_deposit_forms.approved = 1
                        and bank_deposit_forms.deleted_at is null)

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

            DROP VIEW IF EXISTS daily_campaign_view;

            SQL;

    }


    private function create_Old_View(): string
    {
        return <<<SQL

            CREATE VIEW daily_campaign_view AS

            -- Pledge (Gov)
                (select 'pledges' COLLATE utf8mb4_unicode_ci as type,               
                pledges.id as tran_id, 
                eligible_employee_details.business_unit as business_unit_code, eligible_employee_details.tgb_reg_district, 
                eligible_employee_details.deptid, eligible_employee_details.dept_name,
                    1 as donors, pledges.goal_amount as dollars,
                    (select calendar_year - 1 from campaign_years where pledges.campaign_year_id = campaign_years.id) as campaign_year,
                    organizations.code as organization_code, pledges.emplid, pledges.pecsf_id,

                    pledges.created_at
                from pledges
                left outer join organizations on pledges.organization_id = organizations.id 
                left outer join eligible_employee_details on organizations.code = eligible_employee_details.organization_code and pledges.emplid = eligible_employee_details.emplid
                where as_of_date = (select max(as_of_date) from  eligible_employee_details e1 
                                    where 1 = 1 
                                    -- and e1.organization_code =  eligible_employee_details.organization_code 
                                    and e1.year = YEAR( CURDATE() ) 
                                    and e1.as_of_date <= CURDATE())
                    and pledges.emplid is not null
                    and organizations.code = 'GOV'
                    and pledges.deleted_at is null)
            union all   
            -- Pledge (Non Gov)
                (select 'pledges' as type, 
                    pledges.id, 
                    business_units.linked_bu_code, 
                    cities.TGB_REG_DISTRICT,'','', 
                    1 as donors, pledges.goal_amount as dollars,   
                    (select calendar_year - 1 from campaign_years where pledges.campaign_year_id = campaign_years.id) as campaign_year,
                    organizations.code, pledges.emplid, pledges.pecsf_id,
                    pledges.created_at
                from pledges 
                left outer join organizations on pledges.organization_id = organizations.id and organizations.deleted_at is null
                left outer join business_units on organizations.bu_code = business_units.code and business_units.deleted_at is null
                left outer join cities on pledges.city = cities.city 
                where 1=1 
                    and organizations.code <> 'GOV'
                    and pledges.deleted_at is null)
            -- eForm (Gov)   
            union all    
                (select 'eform' as type, 
                    bank_deposit_forms.id, 
                    eligible_employee_details.business_unit, 
                (select code from regions where regions.id = bank_deposit_forms.region_id),  
                department_id, '',
                    case when event_type in ('Fundraiser', 'Gaming') then 0 else 1 end,  bank_deposit_forms.deposit_amount as dollars,   
                    (select calendar_year - 1 from campaign_years where bank_deposit_forms.campaign_year_id = campaign_years.id) as campaign_year,
                    bank_deposit_forms.organization_code, bank_deposit_forms.bc_gov_id, bank_deposit_forms.pecsf_id,
                    bank_deposit_forms.created_at
                from bank_deposit_forms 
                left outer join business_units on business_units.id = bank_deposit_forms.business_unit and business_units.deleted_at is null                                                
                left outer join eligible_employee_details on bank_deposit_forms.organization_code = eligible_employee_details.organization_code 
                                and bank_deposit_forms.bc_gov_id = eligible_employee_details.emplid
                where as_of_date = (select max(as_of_date) from  eligible_employee_details e1 
                                        where 1 = 1 
                                        and e1.year = YEAR( CURDATE() ) 
                                        and e1.as_of_date <= CURDATE())
                    and bank_deposit_forms.organization_code = 'GOV'
                    and bank_deposit_forms.bc_gov_id is not null              
                    and bank_deposit_forms.approved = 1
                    and bank_deposit_forms.deleted_at is null)
            -- eForm (non-Gov)   
            union all    
                (select 'eform' as type, 
                    bank_deposit_forms.id, 
                    business_units.linked_bu_code, 
                    (select code from regions where regions.id = bank_deposit_forms.region_id),  department_id, '',
                    case when event_type in ('Fundraiser', 'Gaming') then 0 else 1 end,  bank_deposit_forms.deposit_amount as dollars,   
                    (select calendar_year - 1 from campaign_years where bank_deposit_forms.campaign_year_id = campaign_years.id) as campaign_year,
                    organization_code, bank_deposit_forms.bc_gov_id, bank_deposit_forms.pecsf_id,
                    bank_deposit_forms.created_at
                from bank_deposit_forms 
                left outer join organizations on bank_deposit_forms.organization_code = organizations.code and organizations.deleted_at is null
                left outer join business_units on business_units.id = bank_deposit_forms.business_unit and business_units.deleted_at is null
                where bank_deposit_forms.organization_code <> 'GOV'
                    and bank_deposit_forms.approved = 1
                    and bank_deposit_forms.deleted_at is null)

            SQL;

    }
}
