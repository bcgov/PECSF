<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorNamesInPledgeHistoriesTable extends Migration
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
            $table->string('charity_bn')->nullable()->change();
            $table->string('vendor_name1')->nullable()->after('amount');
            $table->string('vendor_name2')->nullable()->after('vendor_name1');
            $table->string('vendor_bn')->nullable()->after('vendor_name2');
            $table->string('remit_vendor')->nullable()->after('vendor_bn');
            $table->string('deptid')->nullable()->after('remit_vendor');
            $table->string('city')->nullable()->after('deptid');
            $table->date('created_date')->nullable()->after('city');
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
            $table->dropColumn('vendor_name1');
            $table->dropColumn('vendor_name2');
            $table->dropColumn('vendor_bn');
            $table->dropColumn('remit_vendor');
            $table->dropColumn('deptid');
            $table->dropColumn('city');
            $table->dropColumn('created_date');
        });
    }
}
