<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UseDateTypeInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->date('challenge_start_date')->nullable()->change();
            $table->date('challenge_end_date')->nullable()->change();
            $table->date('challenge_final_date')->nullable()->change();
            
            $table->date('campaign_start_date')->nullable()->change();
            $table->date('campaign_end_date')->nullable()->change();
            $table->date('campaign_final_date')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            
            $table->dateTime('challenge_start_date')->change();
            $table->dateTime('challenge_end_date')->change();
            $table->dateTime('challenge_final_date')->change();
            
            $table->dateTime('campaign_start_date')->change();
            $table->dateTime('campaign_end_date')->change();
            $table->dateTime('campaign_final_date')->change();

        });
    }
}
