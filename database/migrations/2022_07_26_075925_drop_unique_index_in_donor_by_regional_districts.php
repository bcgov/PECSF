<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueIndexInDonorByRegionalDistricts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donor_by_regional_districts', function (Blueprint $table) {
            //
            $table->dropUnique(['regional_district_id','yearcd']);
            $table->index(['regional_district_id','yearcd']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donor_by_regional_districts', function (Blueprint $table) {
            //
            $table->dropIndex(['regional_district_id','yearcd']);
            $table->unique(['regional_district_id','yearcd']);
        });
    }
}
