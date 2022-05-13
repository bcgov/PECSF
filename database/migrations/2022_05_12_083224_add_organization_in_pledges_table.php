<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrganizationInPledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pledges', function (Blueprint $table) {
            //
            $table->bigInteger('organization_id')->after('id');
            $table->bigInteger('campaign_year_id')->after('user_id');
            $table->bigInteger('f_s_pool_id')->after('campaign_year_id');
            $table->float('one_time_amount')->after('amount');
            $table->float('pay_period_amount')->after('one_time_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pledges', function (Blueprint $table) {
            //
            $table->dropColumn('organization_id');
            $table->dropColumn('campaign_year_id');
            $table->dropColumn('f_s_pool_id');
            $table->dropColumn('one_time_amount');
            $table->dropColumn('pay_period_amount');
        });
    }
}
