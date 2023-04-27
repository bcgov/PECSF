<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CleanupOldTablesRelatedToChallengePage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('donor_by_business_units');
        Schema::dropIfExists('donor_by_departments');
        Schema::dropIfExists('donor_by_regional_districts');
        
        Schema::dropIfExists('elligible_employees');
        
        Schema::dropIfExists('regional_districts');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      
    }
}
