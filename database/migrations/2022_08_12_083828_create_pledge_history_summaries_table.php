<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePledgeHistorySummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pledge_history_summaries', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('pledge_history_id');
            $table->string('GUID',50)->nullable();
            $table->string('yearcd',20);
            $table->string("source",20);
            $table->string('campaign_type',20);
            $table->string("frequency",20);
            $table->float('per_pay_amt');
            $table->float('pledge');
            $table->string('region')->nullable();

            $table->timestamps();

            $table->index(['GUID', 'yearcd', 'campaign_type', 'frequency'],'history_guid_yearcd_type_freq');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pledge_history_summaries');
    }
}
