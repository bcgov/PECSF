<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeJobAsIndexesInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('emplid')->nullable()->after('employee_job_id');

            $table->index(['emplid']); 
            $table->index(['name']); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropIndex(['emplid']); 
            $table->dropIndex(['name']); 

            $table->dropColumn('emplid');

        });
    }
}
