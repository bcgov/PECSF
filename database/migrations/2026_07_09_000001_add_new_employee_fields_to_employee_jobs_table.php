<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewEmployeeFieldsToEmployeeJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_jobs', function (Blueprint $table) {
            $table->string('preferred_name')->nullable()->after('email');
            $table->string('add_firstname')->nullable()->after('preferred_name');
            $table->string('add_lastname')->nullable()->after('add_firstname');
            $table->string('add_fullname')->nullable()->after('add_lastname');
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
            $table->dropColumn(['preferred_name', 'add_firstname', 'add_lastname', 'add_fullname']);
        });
    }
}
