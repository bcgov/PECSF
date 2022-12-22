<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmplidInDonateNowPledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donate_now_pledges', function (Blueprint $table) {
            //
            $table->string('emplid')->nullable()->after('organization_id');
            $table->bigInteger('region_id')->nullable()->after('type');

            $table->index(['emplid']); 

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('donate_now_pledges', function (Blueprint $table) {
            //
            $table->dropIndex(['emplid']);
            
            $table->dropcolumn('emplid');
            $table->dropcolumn('region_id');


        });
    }
}
