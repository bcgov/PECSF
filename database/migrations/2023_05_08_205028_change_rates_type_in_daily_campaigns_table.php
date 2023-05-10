<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeRatesTypeInDailyCampaignsTable extends Migration
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
            $table->float('participation_rate')->nullable()->change();
            $table->float('previous_participation_rate')->nullable()->change();
            $table->float('change_rate')->nullable()->change();
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
            $table->string('participation_rate')->nullable()->change();
            $table->string('previous_participation_rate')->nullable()->change();
            $table->string('change_rate')->nullable()->change();
        });
    }
}
