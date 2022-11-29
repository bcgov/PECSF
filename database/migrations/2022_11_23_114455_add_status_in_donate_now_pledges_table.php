<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusInDonateNowPledgesTable extends Migration
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
            $table->softDeletes();

            $table->string('cancelled',1)->nullable()->after('city');
            $table->bigInteger('cancelled_by_id')->nullable()->after('cancelled');
            $table->dateTime('cancelled_at')->nullable()->after('cancelled_by_id');
            
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
            $table->dropSoftDeletes();

            $table->dropColumn('cancelled');
            $table->dropColumn('cancelled_by_id');
            $table->dropColumn('cancelled_at');


        });
    }
}
