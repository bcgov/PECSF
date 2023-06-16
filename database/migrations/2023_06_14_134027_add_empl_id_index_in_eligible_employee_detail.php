<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmplIdIndexInEligibleEmployeeDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('eligible_employee_details', function (Blueprint $table) {
            //
            $table->index(['year', 'as_of_date']);
            $table->index(['year', 'as_of_date', 'organization_code', 'emplid'], 'year_as_of_date_org_emplid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('eligible_employee_details', function (Blueprint $table) {
            //
            $table->dropIndex(['year', 'as_of_date']);
            $table->dropIndex('year_as_of_date_org_emplid');
        });
    }
}
