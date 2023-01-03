<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmplidInPledgeHistorySummariesTable extends Migration
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
            $table->dropIndex('history_guid_yearcd_type_freq');
            $table->dropColumn('GUID');

            $table->string('emplid')->nullable()->after('pledge_history_id');
            $table->index(['emplid', 'yearcd', 'campaign_type', 'frequency'],'history_emplid_yearcd_type_freq');
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
            $table->dropIndex('history_emplid_yearcd_type_freq');
            $table->dropColumn('emplid');

            $table->string('GUID')->nullable()->after('pledge_history_id');
            $table->index(['GUID', 'yearcd', 'campaign_type', 'frequency'],'history_guid_yearcd_type_freq');
        });
    }
}
