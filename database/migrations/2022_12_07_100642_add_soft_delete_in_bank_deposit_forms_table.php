<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSoftDeleteInBankDepositFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_deposit_forms', function (Blueprint $table) {
            //
            $table->softDeletes();
        });

        Schema::table('bank_deposit_form_organizations', function (Blueprint $table) {
            //
            $table->softDeletes();
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
            //
            $table->dropSoftDeletes();
        });

        Schema::table('bank_deposit_form_organizations', function (Blueprint $table) {
            //
            $table->dropSoftDeletes();
        });
    }
}
