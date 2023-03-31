<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupportedProgramInPledgeCharityStaging extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pledge_charity_stagings', function (Blueprint $table) {
            //
            $table->string('supported_program')->nullable()->after('charity_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pledge_charity_stagings', function (Blueprint $table) {
            //
            $table->dropColumn('supported_program');
        });
    }
}
