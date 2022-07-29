<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ColumnEmplidToPecsfIdinDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            //
            $table->string('pecsf_id')->after('org_code');
        });

        DB::table('donations')->update(["pecsf_id" =>  DB::raw('emplid')]);

        Schema::table('donations', function (Blueprint $table) {
            //
            $table->dropColumn('emplid');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            //
            $table->string('emplid')->after('org_code');
        });

        DB::table('donations')->update(["emplid" =>  DB::raw('pecsf_id')]);

        Schema::table('donations', function (Blueprint $table) {
            //
            $table->dropColumn('pecsf_id');
        });

    }
}
