<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCancelledInSpecialCampaignPledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('special_campaign_pledges', function (Blueprint $table) {
            //
            $table->string('first_name')->nullable()->after('deduct_pay_from');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('city')->nullable()->after('last_name');
            $table->string('cancelled',1)->nullable()->after('city');
            $table->bigInteger('cancelled_by_id')->nullable()->after('cancelled');
            $table->dateTime('cancelled_at')->nullable()->after('cancelled_by_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('special_campaign_pledges', function (Blueprint $table) {
            //
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('city');
            $table->dropColumn('cancelled');
            $table->dropColumn('cancelled_by_id');
            $table->dropColumn('cancelled_at');
        });
    }
}
