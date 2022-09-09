<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayCalendarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_calendars', function (Blueprint $table) {
            $table->id();

            $table->date('pay_end_dt');
            $table->date('pay_begin_dt');
            $table->date('check_dt');
            $table->date('close_dt');

            $table->index(['pay_end_dt', 'pay_begin_dt']);

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
        Schema::dropIfExists('pay_calendars');
    }
}
