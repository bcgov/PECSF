<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepartIndexInTableEligibleEmployeeDetails extends Migration
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
            $table->dropIndex(['year', 'business_unit']);
            $table->index(['year', 'as_of_date', 'emplid']);
            $table->index(['year', 'as_of_date', 'business_unit']);
            $table->index(['year', 'as_of_date', 'dept_name']);
            $table->index(['year', 'as_of_date', 'tgb_reg_district']);

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
            $table->index(['year', 'business_unit']);
            $table->dropIndex(['year', 'as_of_date', 'emplid']);
            $table->dropIndex(['year', 'as_of_date', 'business_unit']);
            $table->dropIndex(['year', 'as_of_date', 'dept_name']);
            $table->dropIndex(['year', 'as_of_date', 'tgb_reg_district']);

        });
    }
}
