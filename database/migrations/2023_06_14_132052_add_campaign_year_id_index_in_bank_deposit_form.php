<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCampaignYearIdIndexInBankDepositForm extends Migration
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
            $table->index(['campaign_year_id','organization_code', 'bc_gov_id', 'pecsf_id'], 'year_org_emplid_pecsf_id');
            $table->index(['organization_code', 'bc_gov_id', 'approved']);
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
            $table->dropIndex( 'year_org_emplid_pecsf_id' );
            $table->dropindex(['organization_code', 'bc_gov_id', 'approved']);
            
        });
    }
}
