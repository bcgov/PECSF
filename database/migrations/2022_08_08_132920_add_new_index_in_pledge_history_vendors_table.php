<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewIndexInPledgeHistoryVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pledge_history_vendors', function (Blueprint $table) {
            //
            $table->index(['tgb_reg_district', 'charity_bn', 'vendor_id', 'yearcd', 'effdt'], 'pledge_history_vendors_effdt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pledge_history_vendors', function (Blueprint $table) {
            //
            $table->dropIndex('pledge_history_vendors_effdt');
        });
    }
}
