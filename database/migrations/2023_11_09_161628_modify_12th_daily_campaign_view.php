<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
                case when business_units.linked_bu_code = 'BC022' and pledges.dept_name like 'GCPE%'
                        then 'BGCPE'
                        else business_units.linked_bu_code
                end as business_unit_code, pledges.tgb_reg_district, 
                pledges.deptid, pledges.dept_name,
                    1 as donors, pledges.goal_amount as dollars,
                    (select calendar_year - 1 from campaign_years where pledges.campaign_year_id = campaign_years.id) as campaign_year,
                    organizations.code as organization_code, pledges.emplid, pledges.pecsf_id,
                    pledges.created_at
                from pledges
                left outer join organizations on pledges.organization_id = organizations.id 
                left outer join business_units on pledges.business_unit = business_units.code and business_units.deleted_at is null
                where organizations.code = 'GOV'
                    and pledges.deleted_at is null
                    and pledges.cancelled is null)
            union all   
            -- Pledge (Non Gov)
                (select 'pledges' as type, 
                    pledges.id, 
                    business_units.linked_bu_code, pledges.tgb_reg_district, 
                    null,null, 
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
                    and pledges.deleted_at is null
                    and pledges.cancelled is null)
            -- eForm (Gov)   
            union all    
                (select 'eform' as type, 
                    bank_deposit_forms.id, 
                    case when business_units.linked_bu_code = 'BC022' and bank_deposit_forms.dept_name like 'GCPE%'
                        then 'BGCPE'
                        else business_units.linked_bu_code
                    end,
                (select code from regions where regions.id = bank_deposit_forms.region_id),  
                bank_deposit_forms.deptid, bank_deposit_forms.dept_name,
                    case when event_type in ('Fundraiser', 'Gaming') then 0 else 1 end,  bank_deposit_forms.deposit_amount as dollars,   
                    (select calendar_year - 1 from campaign_years where bank_deposit_forms.campaign_year_id = campaign_years.id) as campaign_year,
                    bank_deposit_forms.organization_code, bank_deposit_forms.bc_gov_id, bank_deposit_forms.pecsf_id,
                    bank_deposit_forms.created_at
                from bank_deposit_forms 
                left outer join business_units on business_units.id = bank_deposit_forms.business_unit and business_units.deleted_at is null                                                
                left outer join organizations on bank_deposit_forms.organization_code = organizations.code and organizations.deleted_at is null
                where organizations.code = 'GOV'
                    and bank_deposit_forms.organization_code = 'GOV'
                    and bank_deposit_forms.approved = 1
                    and bank_deposit_forms.deleted_at is null)
            -- eForm (non-Gov)   
            union all    
                (select 'eform' as type, 
                    bank_deposit_forms.id, 
                    business_units.linked_bu_code, 
                    (select code from regions where regions.id = bank_deposit_forms.region_id),  department_id, null,
                    case when event_type in ('Fundraiser', 'Gaming') then 0 else 1 end,  bank_deposit_forms.deposit_amount as dollars,   
                    (select calendar_year - 1 from campaign_years where bank_deposit_forms.campaign_year_id = campaign_years.id) as campaign_year,
                    organization_code, bank_deposit_forms.bc_gov_id, bank_deposit_forms.pecsf_id,
                    bank_deposit_forms.created_at
                from bank_deposit_forms 
                left outer join organizations on bank_deposit_forms.organization_code = organizations.code and organizations.deleted_at is null
                left outer join business_units on business_units.code = organizations.bu_code and business_units.deleted_at is null
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
            case when business_units.linked_bu_code = 'BC022' and pledges.dept_name like 'GCPE%'
                    then 'BGCPE'
                    else business_units.linked_bu_code
            end as business_unit_code, pledges.tgb_reg_district, 
            pledges.deptid, pledges.dept_name,
                1 as donors, pledges.goal_amount as dollars,
                (select calendar_year - 1 from campaign_years where pledges.campaign_year_id = campaign_years.id) as campaign_year,
                organizations.code as organization_code, pledges.emplid, pledges.pecsf_id,
                pledges.created_at
            from pledges
            left outer join organizations on pledges.organization_id = organizations.id 
            left outer join business_units on pledges.business_unit = business_units.code and business_units.deleted_at is null
            where organizations.code = 'GOV'
                and pledges.deleted_at is null)
        union all   
        -- Pledge (Non Gov)
            (select 'pledges' as type, 
                pledges.id, 
                business_units.linked_bu_code, pledges.tgb_reg_district, 
                null,null, 
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
                case when business_units.linked_bu_code = 'BC022' and bank_deposit_forms.dept_name like 'GCPE%'
                    then 'BGCPE'
                    else business_units.linked_bu_code
                end,
            (select code from regions where regions.id = bank_deposit_forms.region_id),  
            bank_deposit_forms.deptid, bank_deposit_forms.dept_name,
                case when event_type in ('Fundraiser', 'Gaming') then 0 else 1 end,  bank_deposit_forms.deposit_amount as dollars,   
                (select calendar_year - 1 from campaign_years where bank_deposit_forms.campaign_year_id = campaign_years.id) as campaign_year,
                bank_deposit_forms.organization_code, bank_deposit_forms.bc_gov_id, bank_deposit_forms.pecsf_id,
                bank_deposit_forms.created_at
            from bank_deposit_forms 
            left outer join business_units on business_units.id = bank_deposit_forms.business_unit and business_units.deleted_at is null                                                
            left outer join organizations on bank_deposit_forms.organization_code = organizations.code and organizations.deleted_at is null
            where organizations.code = 'GOV'
                and bank_deposit_forms.organization_code = 'GOV'
                and bank_deposit_forms.approved = 1
                and bank_deposit_forms.deleted_at is null)
        -- eForm (non-Gov)   
        union all    
            (select 'eform' as type, 
                bank_deposit_forms.id, 
                business_units.linked_bu_code, 
                (select code from regions where regions.id = bank_deposit_forms.region_id),  department_id, null,
                case when event_type in ('Fundraiser', 'Gaming') then 0 else 1 end,  bank_deposit_forms.deposit_amount as dollars,   
                (select calendar_year - 1 from campaign_years where bank_deposit_forms.campaign_year_id = campaign_years.id) as campaign_year,
                organization_code, bank_deposit_forms.bc_gov_id, bank_deposit_forms.pecsf_id,
                bank_deposit_forms.created_at
            from bank_deposit_forms 
            left outer join organizations on bank_deposit_forms.organization_code = organizations.code and organizations.deleted_at is null
            left outer join business_units on business_units.code = organizations.bu_code and business_units.deleted_at is null
            where bank_deposit_forms.organization_code <> 'GOV'
                and bank_deposit_forms.approved = 1
                and bank_deposit_forms.deleted_at is null)

        SQL;

    }

};