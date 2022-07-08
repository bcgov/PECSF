<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldToUniqueKeyInDonorByDepartment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donor_by_departments', function (Blueprint $table) {
            //
            $table->date('date')->after('yearcd');
            $table->dropUnique(['department_id','yearcd']);
            $table->unique(['department_id','yearcd','date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donor_by_departments', function (Blueprint $table) {
            //
            
            $table->dropUnique(['department_id','yearcd','date']);
            $table->dropColumn('date');
            $table->unique(['department_id','yearcd']);            
            
        });
    }
}
