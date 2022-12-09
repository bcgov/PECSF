<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftdeleteInPledgesTable extends Migration
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
            $table->softDeletes();
        });

        Schema::table('pledge_charities', function (Blueprint $table) {
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
        Schema::table('pledges', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });

        Schema::table('pledge_charities', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });
    }
}
