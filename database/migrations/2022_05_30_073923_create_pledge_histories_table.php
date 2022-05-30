<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePledgeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pledge_histories', function (Blueprint $table) {
            $table->id();
            $table->string('campaign_type');
            $table->string("source");
            $table->string("tgb_reg_district")->nullable();
            $table->bigInteger('region_id')->nullable();
            $table->string('charity_bn');
            $table->bigInteger('charity_id')->nullable();
            $table->string('yearcd');
            $table->bigInteger('campaign_year_id')->nullable();
            $table->string("emplid");
            $table->string('GUID');
            $table->string("frequency");
            $table->float('pledge');
            $table->decimal('percent', $precision = 8, $scale = 2);
            $table->float('amount');
            $table->timestamps();

            $table->index(['GUID']); 

        });

        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('pledge_histories');
    }
}
