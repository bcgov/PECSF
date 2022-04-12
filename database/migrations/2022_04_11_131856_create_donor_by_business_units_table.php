<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonorByBusinessUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donor_by_business_units', function (Blueprint $table) {
            $table->id();
            $table->string('business_unit_code');
            $table->string('yearcd');
            $table->float('dollars');
            $table->integer('donors');
            $table->bigInteger('business_unit_id');
            $table->timestamps();

            $table->unique(['business_unit_id','yearcd']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donor_by_business_units');
    }
}
