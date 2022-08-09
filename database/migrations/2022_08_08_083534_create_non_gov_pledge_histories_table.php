<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNonGovPledgeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('non_gov_pledge_histories');
    }
}
