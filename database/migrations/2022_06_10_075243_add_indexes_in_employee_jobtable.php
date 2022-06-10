<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesInEmployeeJobtable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_jobs', function (Blueprint $table) {
            //
            $table->index(['guid']); 
            $table->index(['region_id']); 
            $table->index(['business_unit_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_jobs', function (Blueprint $table) {
            //
            $table->dropIndex(['guid']);
            $table->dropIndex(['region_id']);
            $table->dropIndex(['business_unit_id']);
        });
    }
}
