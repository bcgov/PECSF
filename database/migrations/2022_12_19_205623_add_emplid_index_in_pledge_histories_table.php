<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmplidIndexInPledgeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pledge_histories', function (Blueprint $table) {
            //
            $table->index(['emplid']); 
            $table->index(['source', 'emplid', 'yearcd', 'campaign_type', 'frequency', 'tgb_reg_district'], 'pledge_histories_emplid_plus_others');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pledge_histories', function (Blueprint $table) {
            //
            $table->dropIndex(['emplid']); 
            $table->dropIndex('pledge_histories_emplid_plus_others');
        });
    }
}
