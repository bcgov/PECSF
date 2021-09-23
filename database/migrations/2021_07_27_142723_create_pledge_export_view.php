<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePledgeExportView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* DB::statement("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));"); */
        DB::statement("CREATE OR REPLACE VIEW pledge_export as 
            SELECT '' AS `GUID`, u.id AS EMPLOYEEID, u.name as EMPLOYEE_NAME, 
            IF(STRCMP(p.frequency, 'bi-weekly') = 0, 'BiWeekly', 'One Time') as DonationType, p.created_at as Date,
            pc.goal_amount as GoalAmount,
            YEAR(p.created_at) as year,
            pc.amount as DonationAmount, 
            (100 * pc.amount / p.amount) as percent,
            c.charity_name, c.registration_number AS CRABN,
            pc.additional as SUPPORTED_PROGRAM, c.address as TEXTSTRE1, '' as TEXTSTRE2, c.city as NAMECITY, c.province AS CODESTTE, c.postal_code as CODEPSTL, '' as NAMECTAC, '' as TITLECTAC
            FROM `pledge_charities` pc, `pledges` p, `users` u, `charities` c

            WHERE p.id = pledge_id
            AND u.id = user_id
            AND c.id = charity_id
            AND report_generated_at is null");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW pledge_export");
    }
}
