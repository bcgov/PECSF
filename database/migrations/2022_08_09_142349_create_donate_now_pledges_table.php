<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonateNowPledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donate_now_pledges', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('organization_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('pecsf_id')->nullable();
            $table->string('yearcd');
            $table->integer('seqno');

            $table->string('type',1);
            $table->bigInteger('f_s_pool_id')->nullable();
            $table->bigInteger('charity_id')->nullable();
            $table->string('special_program')->nullable();

            $table->float('one_time_amount');

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('city')->nullable();
            
            $table->char('ods_export_status',1)->nullable();
            $table->timestamp('ods_export_at')->nullable();

            $table->bigInteger('created_by_id')->nullable();
            $table->bigInteger('updated_by_id')->nullable();

            $table->timestamps();

            $table->index([ 'organization_id', 'user_id', 'pecsf_id', 'yearcd' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donate_now_pledges');
    }
}
