<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StaticChallengePage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('historical_challenge_pages', function (Blueprint $table) {
            $table->id();
            $table->string('organization_name');
            $table->string('participation_rate');
            $table->string('previous_participation_rate');
            $table->string('change')->nullable();
            $table->string('donors')->nullable();
            $table->string("dollars");
            $table->string("year");
            $table->timestamps();
        });
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historical_challenge_pages');
    }
}
