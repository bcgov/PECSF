<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventsInPledgeHistorySummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pledge_history_summaries', function (Blueprint $table) {
            //
            $table->string('event_type')->nullable()->after('region');
            $table->string('event_sub_type')->nullable()->after('event_type');
            $table->date('event_deposit_date')->nullable()->after('event_sub_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pledge_history_summaries', function (Blueprint $table) {
            //
            $table->dropColumn('event_type');
            $table->dropColumn('event_sub_type');
            $table->dropColumn('event_deposit_date');

        });
    }
}
