<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePledgeStagingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pledge_stagings', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('history_id');
            $table->string('pledge_type')->nullable();
            $table->bigInteger('pledge_id')->nullable();
            $table->string('calendar_year')->nullable();

            $table->string('organization_code')->nullable();
            $table->string('emplid')->nullable();
            $table->string('pecsf_id')->nullable();
            $table->string('name')->nullable();
            $table->string('business_unit_code')->nullable();
            $table->string('tgb_reg_district')->nullable();
            $table->string('deptid')->nullable();
            $table->string('dept_name')->nullable();
            $table->string('city')->nullable();
            $table->string('type')->nullable();
            $table->string('sub_type')->nullable();
            $table->string('pool_type')->nullable();
            $table->bigInteger('region_id')->nullable();
            $table->float('pledge')->nullable();
            $table->float('amount')->nullable();
            $table->bigInteger('created_by_id')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();

            $table->index(['history_id','pledge_type', 'calendar_year']);

        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pledge_stagings');
    }
}
