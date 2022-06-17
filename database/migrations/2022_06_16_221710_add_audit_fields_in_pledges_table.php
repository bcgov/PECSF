<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAuditFieldsInPledgesTable extends Migration
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
            $table->bigInteger('created_by_id')->nullable()->after('bi_export_at');
            $table->bigInteger('updated_by_id')->nullable()->after('created_by_id');
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
            $table->dropColumn('created_by_id');
            $table->dropColumn('updated_by_id');
        });
    }
}
