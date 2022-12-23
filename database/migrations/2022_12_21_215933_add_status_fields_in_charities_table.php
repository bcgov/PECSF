<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusFieldsInCharitiesTable extends Migration
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
            $table->string('type_of_qualified_donee')->nullable()->after('charity_status');
            $table->string('charity_type')->nullable()->after('designation_code');
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
            $table->dropColumn('type_of_qualified_donee');
            $table->dropColumn('charity_type');
        });
    }
}
