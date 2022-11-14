<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeJobAuditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_job_audits', function (Blueprint $table) {
            $table->id();

            $table->datetime('audit_stamp'); 
            $table->string('audit_action', 1);
            $table->string('emplid');
            $table->Integer('empl_rcd'); 
            $table->date( 'effdt')->nullable(); 
            $table->Integer('effseq')->nullable(); 
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
            $table->string('office_address1')->nullable();    
            $table->string('office_address2')->nullable();    
            $table->string('office_city')->nullable();    
            $table->string('office_stateprovince')->nullable();    
            $table->string('office_country')->nullable();    
            $table->string('office_postal')->nullable();   
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
            $table->date('date_updated')->nullable();
            $table->date('date_deleted')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_job_audits');
    }
}
