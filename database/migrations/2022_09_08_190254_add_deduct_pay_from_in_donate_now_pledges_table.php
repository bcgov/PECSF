<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeductPayFromInDonateNowPledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donate_now_pledges', function (Blueprint $table) {
            //
            $table->date('deduct_pay_from')->nullable()->after('one_time_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donate_now_pledges', function (Blueprint $table) {
            //
            $table->dropColumn('deduct_pay_from');
        });
    }
}
