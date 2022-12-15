<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNonGovPledgeHistorySummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('non_gov_pledge_history_summaries', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('pledge_history_id')->nullable();
            $table->string('org_code',30)->nullable();
            $table->string('emplid',30)->nullable();
            $table->string('pecsf_id',30)->nullable();
            $table->string('yearcd',30)->nullable();
            $table->string("source",20)->nullable();
            $table->string('pledge_type',20)->nullable();
            $table->string("frequency",20)->nullable();
            $table->float('per_pay_amt')->nullable();
            $table->float('pledge')->nullable();
            $table->string('region')->nullable();

            $table->string("event_type")->nullable();
            $table->string("event_sub_type")->nullable();
            $table->string("event_deposit_date")->nullable();

            $table->timestamps();

            $table->index(['org_code', 'emplid', 'pecsf_id', 'yearcd', 'pledge_type', 'frequency'],'non_gov_history_id_yearcd_type_freq');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('non_gov_pledge_history_summaries');
    }
}
