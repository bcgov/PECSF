<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendorIDInPledgeHistoriesTable extends Migration
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
            $table->float('per_pay_amt')->nullable()->after('frequency');
            $table->string('vendor_id')->nullable()->after('GUID');
            $table->string('additional_info')->nullable()->after('vendor_id');

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
            $table->dropColumn('per_pay_amt');
            $table->dropColumn('vendor_id');
            $table->dropColumn('additional_info');
        });
    }
}
