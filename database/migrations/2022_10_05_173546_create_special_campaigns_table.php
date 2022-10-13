<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_campaigns', function (Blueprint $table) {
            
            $table->id();

            $table->string('name');
            $table->string('description');
            $table->string('banner_text');
            $table->bigInteger('charity_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('image')->nullable();

            $table->bigInteger('created_by_id')->nullable();
            $table->bigInteger('updated_by_id')->nullable();

            $table->timestamps();

            $table->index([ 'charity_id']);
            $table->index([ 'name', 'description', 'banner_text']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_campaigns');
    }
}
