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
        Schema::table('pledge_stagings', function (Blueprint $table) {

            $table->float('one_time_amount')->nullable()->after('pledge');;            //
            $table->float('biweekly_amount')->nullable()->after('one_time_amount');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pledge_stagings', function (Blueprint $table) {
            //
            $table->dropColumn('one_time_amount');
            $table->dropColumn('biweekly_amount');
        });
    }
};
