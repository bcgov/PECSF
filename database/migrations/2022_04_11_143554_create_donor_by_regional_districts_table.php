<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonorByRegionalDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donor_by_regional_districts', function (Blueprint $table) {
            $table->id();
            $table->string('tgb_reg_district');
            $table->string('yearcd');
            $table->float('dollars');
            $table->integer('donors');
            $table->bigInteger('regional_district_id');
            $table->timestamps();

            $table->unique(['regional_district_id','yearcd']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donor_by_regional_districts');
    }
}
