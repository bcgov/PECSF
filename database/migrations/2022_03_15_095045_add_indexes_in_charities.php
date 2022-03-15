<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesInCharities extends Migration
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
            $table->unique('registration_number');
            $table->index(['charity_name']); 
            $table->index(['category_code']); 
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
            $table->dropUnique(['registration_number']);
            $table->dropIndex(['charity_name']);
            $table->dropIndex(['category_code']);
        });
    }
}
