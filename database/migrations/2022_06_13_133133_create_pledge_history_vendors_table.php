<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePledgeHistoryVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pledge_history_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('charity_bn');
            $table->string('eff_status',1);
            $table->date('effdt');
            $table->string('name1')->nullable();
            $table->string('name2')->nullable();
            $table->string('tgb_reg_district')->nullable();
            $table->string('vendor_id')->nullable();
            $table->string('yearcd')->nullable();
            $table->timestamps();

        //    $table->index(['tgb_reg_district', 'charity_bn', 'effdt']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pledge_history_vendors');
    }
}
