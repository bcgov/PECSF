<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexYearcdInPledgeHistoriesTable extends Migration
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

            $table->string('source',20)->change();
            $table->string('yearcd',20)->change();
            $table->string('GUID', 50)->nullable()->change();
            $table->string('campaign_type',20)->change();
            $table->string('frequency',20)->change();
            $table->string('tgb_reg_district',20)->nullable()->change();

            $table->index(['source', 'GUID', 'yearcd', 'campaign_type', 'frequency', 'tgb_reg_district'], 'pledge_histories_guid_plus_others');
            
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

            $table->dropIndex('pledge_histories_guid_plus_others');
        });
    }
}
