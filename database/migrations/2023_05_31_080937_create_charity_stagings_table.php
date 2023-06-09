<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharityStagingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charity_stagings', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('history_id')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('charity_name')->nullable();
            $table->string('charity_status')->nullable();

            $table->timestamps();

            $table->index(['history_id', 'registration_number']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charity_stagings');
    }
}
