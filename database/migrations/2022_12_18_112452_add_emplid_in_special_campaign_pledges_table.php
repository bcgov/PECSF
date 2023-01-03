<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmplidInSpecialCampaignPledgesTable extends Migration
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
            $table->string('emplid')->nullable()->after('organization_id');

            $table->index(['emplid']); 
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
            $table->dropIndex(['emplid']);

            $table->dropcolumn('emplid');

        });
    }
}
