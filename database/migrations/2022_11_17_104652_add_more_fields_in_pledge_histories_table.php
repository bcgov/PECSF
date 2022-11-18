<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsInPledgeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pledge_histories', function (Blueprint $table) {
            //
            $table->string('business_unit')->nullable()->after('city');
            $table->string('event_descr')->nullable()->after('business_unit');
            $table->string('event_type')->nullable()->after('event_descr');
            $table->string('event_sub_type')->nullable()->after('event_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pledge_histories', function (Blueprint $table) {
            //
            $table->dropColumn('business_unit');
            $table->dropColumn('event_descr');
            $table->dropColumn('event_type');
            $table->dropColumn('event_sub_type');
        });
    }
}
