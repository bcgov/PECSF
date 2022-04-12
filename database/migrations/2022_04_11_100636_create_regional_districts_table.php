<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegionalDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('regional_districts', function (Blueprint $table) {
            $table->id();
            $table->string('tgb_reg_district')->unique();
            $table->string('reg_district_desc');
            $table->string('development_region');
            $table->string('provincial_quadrant');	
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
        Schema::dropIfExists('regional_districts');
    }
}
