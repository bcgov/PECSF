<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressInEmployeeJobsTable extends Migration
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
            $table->string('address1')->nullable()->after('office_postal');
            $table->string('address2')->nullable()->after('address1');
            $table->string('postal')->nullable()->after('country');
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
            $table->dropColumn('address1');
            $table->dropColumn('address2');
            $table->dropColumn('postal');

        });
    }
}
