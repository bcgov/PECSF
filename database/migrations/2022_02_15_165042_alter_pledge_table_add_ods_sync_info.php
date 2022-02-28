<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPledgeTableAddOdsSyncInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('pledges', function (Blueprint $table) {
            $table->char('ods_export_status',1)->after('report_generated_at')->nullable();
            $table->timestamp('ods_export_at')->after('ods_export_status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('pledges', function (Blueprint $table) {
            $table->dropColumn('ods_export_status');
            $table->dropColumn('ods_export_at');
        });
    }
}
