<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Settingschallenge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dateTime('challenge_final_date');
            $table->dateTime('campaign_start_date');
            $table->dateTime('campaign_end_date');
            $table->dateTime('campaign_final_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->dropColumn('challenge_final_date');
            $table->dropColumn('campaign_start_date');
            $table->dropColumn('campaign_end_date');
            $table->dropColumn('campaign_final_date');
        });
    }
}
