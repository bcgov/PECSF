<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEligibleEmployeeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eligible_employee_details', function (Blueprint $table) {
            $table->id();

            $table->string('year')->nullable();
            $table->date('as_of_date')->nullable();

            $table->string('organization_code')->nullable();
            $table->string('emplid');
            $table->string('empl_status')->nullable();

            $table->string('name')->nullable();

            $table->string('business_unit')->nullable();
            $table->string('business_unit_name')->nullable();

            $table->string('deptid')->nullable();
            $table->string('dept_name')->nullable();

            $table->string('tgb_reg_district')->nullable();
            
            $table->string('office_address1')->nullable();
            $table->string('office_address2')->nullable();
            $table->string('office_city')->nullable();
            $table->string('office_stateprovince')->nullable();
            $table->string('office_postal')->nullable();
            $table->string('office_country')->nullable();

            $table->string('organization_name')->nullable();

            $table->bigInteger('employee_job_id');

            $table->bigInteger('created_by_id')->nullable();
            $table->bigInteger('updated_by_id')->nullable();
            
            $table->timestamps();

            $table->index(['year','business_unit']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eligible_employee_details');
    }
}
