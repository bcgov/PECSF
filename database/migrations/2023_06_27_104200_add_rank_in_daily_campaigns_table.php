<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRankInDailyCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_campaigns', function (Blueprint $table) {
            //
            $table->integer('rank')->nullable()->after('change_rate');
           
            $table->index(['campaign_year', 'as_of_date', 'daily_type', 'rank']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_campaigns', function (Blueprint $table) {
            //
            $table->dropColumn('rank');

            $table->dropIndex(['campaign_year', 'as_of_date', 'daily_type', 'rank']);
        });
    }
}
