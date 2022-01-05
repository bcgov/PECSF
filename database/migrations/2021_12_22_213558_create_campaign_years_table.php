<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignYearsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_years', function (Blueprint $table) {
            $table->id();
            $table->string('calendar_year')->unique();;
            $table->string('status');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('number_of_periods');
            $table->date('as_of_date');
            $table->date('close_date');
            $table->bigInteger('created_by_id');
            $table->bigInteger('modified_by_id');
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
        Schema::dropIfExists('campaign_years');
    }
}
