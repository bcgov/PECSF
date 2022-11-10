<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMoreFieldsInNonGovPledgeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::dropIfExists('non_gov_pledge_histories');

        // Schema::table('non_gov_pledge_histories', function (Blueprint $table) {
            
        //     //
        //     $table->dropColumn('pledge_type');
        //     $table->dropColumn('yearcd');
        //     $table->dropColumn('remit_vendor_bn');
        //     $table->dropColumn('name');
        //     $table->dropColumn('first_name');
        //     $table->dropColumn('last_name');
        //     $table->dropColumn('amount');

        // });

        Schema::create('non_gov_pledge_histories', function (Blueprint $table) {
            //
            $table->id();

            $table->string('pledge_type')->nullable();
            $table->string('source')->nullable();
            $table->string('tgb_reg_district')->nullable();
            $table->string('charity_bn')->nullable();
            $table->string('yearcd')->nullable();

            $table->string('org_code')->nullable();
            $table->string("emplid")->nullable();
            $table->string("pecsf_id")->nullable();

            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('guid')->nullable();
            
            $table->string('vendor_id')->nullable();
            $table->string('additional_info')->nullable();
            $table->string('frequency')->nullable();

            $table->float('per_pay_amt')->nullable();
            $table->float('pledge')->nullable();
            $table->decimal('percent', $precision = 8, $scale = 2)->nullable();
            $table->float('amount')->nullable();
            $table->string('deduction_code')->nullable();

            $table->string('vendor_name1')->nullable();
            $table->string('vendor_name2')->nullable();
            $table->string('vendor_bn')->nullable();
            $table->string('remit_vendor')->nullable();

            $table->string('deptid')->nullable();
            $table->string('city')->nullable();
            $table->date('created_date')->nullable();

            $table->bigInteger('created_by_id')->nullable();
            $table->bigInteger('updated_by_id')->nullable();

            $table->timestamps();

            $table->index(['pledge_type','source']);
            $table->index(['org_code','pecsf_id', 'yearcd']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::dropIfExists('non_gov_pledge_histories');

        Schema::create('non_gov_pledge_histories', function (Blueprint $table) {
            
            $table->id();
            
            $table->string('org_code')->nullable();
            $table->string("emplid")->nullable();
            $table->string("pecsf_id")->nullable();
            $table->string('pledge_type')->nullable();
            $table->string('yearcd')->nullable();

            $table->string('vendor_id')->nullable();
            $table->string('vendor_bn')->nullable();

            $table->string('remit_vendor')->nullable();
            $table->string('remit_vendor_bn')->nullable();

            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            $table->string('city')->nullable();
            $table->float('amount')->nullable();

            $table->bigInteger('created_by_id')->nullable();
            $table->bigInteger('updated_by_id')->nullable();

            $table->timestamps();

            $table->index(['org_code','pecsf_id', 'yearcd']);

        });


    }
}
