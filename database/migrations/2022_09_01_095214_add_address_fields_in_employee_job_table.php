<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressFieldsInEmployeeJobTable extends Migration
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
            $table->string('office_address1')->nullable()->after('region_id');
            $table->string('office_address2')->nullable()->after('office_address1');
            $table->string('office_city')->nullable()->after('office_address2');
            $table->string('office_stateprovince')->nullable()->after('office_city');
            $table->string('office_country')->nullable()->after('office_stateprovince');
            $table->string('office_postal')->nullable()->after('office_country');

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
            $table->dropColumn('office_address1');
            $table->dropColumn('office_address2');
            $table->dropColumn('office_city');
            $table->dropColumn('office_stateprovince');
            $table->dropColumn('office_country');
            $table->dropColumn('office_postal');

        });
    }
}
