<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLockdownFieldsInSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->dateTime('system_lockdown_start')->nullable()->after('id');
            $table->dateTime('system_lockdown_end')->nullable()->after('system_lockdown_start');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->dropColumn('system_lockdown_start');
            $table->dropColumn('system_lockdown_end');
        });
    }
}
