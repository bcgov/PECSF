<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pledges', function (Blueprint $table) {
            //
            $table->string('deptid')->nullable()->after('tgb_reg_district');
            $table->string('dept_name')->nullable()->after('deptid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pledges', function (Blueprint $table) {
            //
            $table->dropColumn('deptid');
            $table->dropColumn('dept_name');
        });
    }
};
