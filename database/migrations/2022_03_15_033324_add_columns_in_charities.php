<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInCharities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charities', function (Blueprint $table) {
            //
            $table->text('ongoing_program')->nullable()->after('postal_code');
            $table->text('url')->nullable()->after('ongoing_program');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('charities', function (Blueprint $table) {
            //
            $table->dropColumn('ongoing_program');
            $table->dropColumn('url');
        });
    }
}
