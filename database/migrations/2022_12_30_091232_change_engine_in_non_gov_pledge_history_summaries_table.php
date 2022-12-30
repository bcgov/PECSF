<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEngineInNonGovPledgeHistorySummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('non_gov_pledge_history_summaries', function (Blueprint $table) {
            //
            $table->string('pledge_type', 20)->nullable()->change();
            $table->string('source', 20)->nullable()->change();
            $table->string('yearcd', 20)->nullable()->change();
            $table->string('org_code', 20)->nullable()->change();
            $table->string('emplid', 20)->nullable()->change();
            $table->string('pecsf_id', 20)->nullable()->change();
            $table->string('event_type', 50)->nullable()->change();
            $table->string('event_sub_type', 50)->nullable()->change();
            $table->date('event_deposit_date')->nullable()->change();
        });

        DB::statement('ALTER TABLE non_gov_pledge_history_summaries ENGINE = MyISAM');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        DB::statement('ALTER TABLE non_gov_pledge_history_summaries ENGINE = InnoDB');

        Schema::table('non_gov_pledge_history_summaries', function (Blueprint $table) {
            //
            $table->string('pledge_type', 50)->nullable()->change();
            $table->string('source', 50)->nullable()->change();
            $table->string('yearcd', 50)->nullable()->change();
            $table->string('org_code', 50)->nullable()->change();
            $table->string('emplid', 50)->nullable()->change();
            $table->string('pecsf_id', 50)->nullable()->change();
            $table->string('event_type', 50)->nullable()->change();
            $table->string('event_sub_type', 50)->nullable()->change();

            $table->string('event_deposit_date')->nullable()->change();
        });
    }
}
