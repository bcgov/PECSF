<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Changechallgnepagecolumnetypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historical_challenge_pages', function (Blueprint $table) {
            $table->float('participation_rate', 50)->nullable()->change();
            $table->float('previous_participation_rate', 50)->nullable()->change();
            $table->float('change', 50)->nullable()->change();
            $table->float('dollars', 50)->nullable()->change();
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
    }
}
