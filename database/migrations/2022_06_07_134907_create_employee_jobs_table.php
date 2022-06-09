<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_jobs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('organization_id')->nullable();
            $table->string('emplid');
            $table->integer('empl_rcd');
            $table->date('effdt');
            $table->integer('effseq');
            
            $table->string('empl_status')->nullable();
            $table->string('empl_class')->nullable();
            $table->string('empl_ctg')->nullable();
            $table->string('job_indicator')->nullable();

            $table->string('position_number')->nullable();
            $table->string('position_title')->nullable();
            $table->string('appointment_status')->nullable();

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('guid')->nullable();
            $table->string('idir')->nullable();

            $table->string('business_unit')->nullable();
            $table->bigInteger('business_unit_id')->nullable();
            $table->string('deptid')->nullable();
            $table->string('dept_name')->nullable();
            $table->string('tgb_reg_district')->nullable();
            $table->bigInteger('region_id')->nullable();

            $table->string('city')->nullable();
            $table->string('stateprovince')->nullable();
            $table->string('country')->nullable();

            $table->string('organization')->nullable();
            $table->string('level1_program')->nullable();
            $table->string('level2_division')->nullable();
            $table->string('level3_branch')->nullable();
            $table->string('level4')->nullable();

            $table->string('supervisor_emplid')->nullable();
            $table->string('supervisor_name')->nullable();
            $table->string('supervisor_email')->nullable();

            $table->timestamp('date_updated')->nullable();
            $table->timestamp('date_deleted')->nullable();

            $table->bigInteger('created_by_id')->nullable();
            $table->bigInteger('updated_by_id')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['emplid','empl_rcd']); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_jobs');
    }
}
