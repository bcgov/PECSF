<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexInBankDepositFormsTable extends Migration
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
            $table->index(['bc_gov_id']);
            $table->index(['bc_gov_id', 'organization_code','event_type','approved'],'bc_gov_id_and_others');


        });

        Schema::table('bank_deposit_form_organizations', function (Blueprint $table) {
            //
            $table->index(['bank_deposit_form_id']);


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
            $table->dropIndex(['bc_gov_id']);
            $table->dropIndex('bc_gov_id_and_others');
        });

        Schema::table('bank_deposit_form_organizations', function (Blueprint $table) {
            //
            $table->dropIndex(['bank_deposit_form_id']);
        });
    }
}
