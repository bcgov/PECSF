<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('daily_campaigns', function (Blueprint $table) {
            $table->id();

            $table->integer("campaign_year");
            $table->date("as_of_date");
            $table->integer("daily_type");
            $table->string('business_unit')->nullable();
            $table->string('business_unit_name')->nullable();
            $table->string('region_code')->nullable();
            $table->string('region_name')->nullable();
            $table->string('deptid')->nullable();
            $table->string('dept_name')->nullable();

            $table->string('participation_rate')->nullable();;
            $table->string('previous_participation_rate')->nullable();;
            $table->string('change_rate')->nullable();

            $table->integer('eligible_employee_count')->nullable();
            $table->integer('donors')->nullable();
            $table->double("dollars",12,2)->nullable();

            $table->timestamps();

            $table->index(['campaign_year', 'as_of_date', 'daily_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_campaigns');
    }
}
