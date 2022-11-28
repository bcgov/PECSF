<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDepositDateInPledgeHistoriesTable extends Migration
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
        Schema::table('pledge_histories', function (Blueprint $table) {
            //
            $table->dropColumn('event_deposit_date');
        });
    }
}
