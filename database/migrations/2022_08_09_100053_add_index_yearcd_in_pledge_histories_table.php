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
            $table->index(['GUID', 'yearcd', 'campaign_type', 'frequency'], 'pledge_histories_guid_plus_others');
            
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
