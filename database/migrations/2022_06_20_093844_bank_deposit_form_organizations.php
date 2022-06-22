<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BankDepositFormOrganizations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_deposit_form_organizations', function (Blueprint $table) {
            $table->id();
            $table->integer('bank_deposit_form_id');
            $table->string('organization_name');
            $table->string('vendor_id');
            $table->string('donation_percent');
            $table->string('specific_community_or_initiative');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_deposit_form_organizations');
    }
}
