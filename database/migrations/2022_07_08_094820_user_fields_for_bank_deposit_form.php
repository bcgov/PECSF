<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UserFieldsForBankDepositForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_deposit_forms', function (Blueprint $table) {
            $table->string('pecsf_id')->nullable();
            $table->string('bc_gov_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_deposit_forms', function (Blueprint $table) {
            $table->dropColumn('pecsf_id');
            $table->dropColumn('bc_gov_id');
        });
    }
}
