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
        Schema::table('pledge_charity_stagings', function (Blueprint $table) {
            //
            $table->string('f_s_pool_id')->nullable()->after('pool_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pledge_charity_stagings', function (Blueprint $table) {
            //
            $table->dropColumn('f_s_pool_id');
        });
    }
};
