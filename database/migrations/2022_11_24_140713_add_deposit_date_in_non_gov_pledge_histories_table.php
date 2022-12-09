<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepositDateInNonGovPledgeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('non_gov_pledge_histories', function (Blueprint $table) {
            //
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
        Schema::table('non_gov_pledge_histories', function (Blueprint $table) {
            //
            $table->dropColumn('event_deposit_date');
        });
    }
}
