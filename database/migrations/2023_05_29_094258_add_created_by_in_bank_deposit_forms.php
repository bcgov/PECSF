<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByInBankDepositForms extends Migration
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
            $table->bigInteger('created_by_id')->nullable()->after('address_postal_code');
            $table->bigInteger('updated_by_id')->nullable()->after('created_by_id');
            $table->bigInteger('approved_by_id')->nullable()->after('updated_by_id');
            $table->datetime('approved_at')->nullable()->after('approved_by_id');
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
            $table->dropColumn('created_by_id');
            $table->dropColumn('updated_by_id');
            $table->dropColumn('approved_by_id');
            $table->dropColumn('approved_at');
            
        });
    }
}
