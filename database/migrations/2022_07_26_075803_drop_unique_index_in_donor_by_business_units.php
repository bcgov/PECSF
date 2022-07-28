<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueIndexInDonorByBusinessUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donor_by_business_units', function (Blueprint $table) {
            //
            $table->dropUnique(['business_unit_id','yearcd']);
            $table->index(['business_unit_id','yearcd']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donor_by_business_units', function (Blueprint $table) {
            //
            $table->dropIndex(['business_unit_id','yearcd']);
            $table->unique(['business_unit_id','yearcd']);
            
        });
    }
}
