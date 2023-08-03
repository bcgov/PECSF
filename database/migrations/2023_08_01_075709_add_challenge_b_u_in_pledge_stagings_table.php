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
            //
            $table->string('challenge_bu_code')->nullable()->after('business_unit_code');
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
            $table->dropColumn('challenge_bu_code');
        });
    }
};
