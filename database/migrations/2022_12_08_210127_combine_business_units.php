<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CombineBusinessUnits extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        BusinessUnit::where("name" , "Government Communications and Public Engagement")
        ->update(["code" => "BC022"]);
        BusinessUnit::where("name" , "Emergency Management BC")
            ->update(["code" => "BC010"]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
