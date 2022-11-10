<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeletesInMultipleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_units', function (Blueprint $table) {
            //
            $table->softDeletes();
        });

        Schema::table('organizations', function (Blueprint $table) {
            //
            $table->softDeletes();
        });

        Schema::table('regions', function (Blueprint $table) {
            //
            $table->softDeletes();
        });

        Schema::table('special_campaigns', function (Blueprint $table) {
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
        Schema::table('business_units', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });

        Schema::table('organizations', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });

        Schema::table('regions', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });

        Schema::table('special_campaigns', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });

    }
}
