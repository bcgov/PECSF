<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteInFSPoolsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('f_s_pools', function (Blueprint $table) {
            //
            $table->softDeletes();
        });

        Schema::table('f_s_pool_charities', function (Blueprint $table) {
            //
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('f_s_pools', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });

        Schema::table('f_s_pool_charities', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });
    }
}
