<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_campaign_summaries', function (Blueprint $table) {
            $table->id();

            $table->integer("campaign_year");
            $table->date("as_of_date");

            $table->integer('donors')->nullable();
            $table->double("dollars",12,2)->nullable();

            $table->text('notes')->nullable();

            $table->bigInteger('created_by_id')->nullable();
            $table->bigInteger('updated_by_id')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index(['campaign_year']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_campaign_summaries');
    }
};
