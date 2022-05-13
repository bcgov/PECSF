<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPercentageInPledgeCharitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pledge_charities', function (Blueprint $table) {
            //
            $table->decimal('percentage', $precision = 8, $scale = 2)->after('additional');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pledge_charities', function (Blueprint $table) {
            //
            $table->dropColumn('percentage');
        });
    }
}
