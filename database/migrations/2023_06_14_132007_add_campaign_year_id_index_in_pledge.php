<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampaignYearIdIndexInPledge extends Migration
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
            $table->index(['campaign_year_id','organization_id', 'emplid', 'pecsf_id']);
            
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
            $table->dropIndex(['campaign_year_id','organization_id', 'emplid', 'pecsf_id']);
        });
    }
}
