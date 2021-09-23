<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePledgeCharitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pledge_charities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('charity_id')->constrained();
            $table->foreignId('pledge_id')->constrained();
            $table->string('additional')->nullable();
            /* $table->unsignedTinyInteger('cheque_pending')->default(0); */
            $table->float('amount');
            $table->float('goal_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pledge_charities');
    }
}
