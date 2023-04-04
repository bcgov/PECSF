<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampaignYearIdInBankDepositFormTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_deposit_forms', function (Blueprint $table) {
            //
            $table->bigInteger('campaign_year_id')->nullable()->after('form_submitter_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_deposit_forms', function (Blueprint $table) {
            //
            $table->dropColumn('campaign_year_id');
        });
    }
}
