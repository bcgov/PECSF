<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEngineInNonGovPledgeHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('non_gov_pledge_histories', function (Blueprint $table) {
            
            //
            $table->string('source', 20)->nullable()->change();
            $table->string('tgb_reg_district', 20)->nullable()->change();
            $table->string('yearcd', 20)->nullable()->change();
            $table->string('org_code', 20)->nullable()->change();
            $table->string('emplid', 20)->nullable()->change();
            $table->string('pecsf_id', 20)->nullable()->change();
            $table->string('guid', 50)->nullable()->change();
            $table->string('event_type', 50)->nullable()->change();
            $table->string('event_sub_type', 50)->nullable()->change();


            $table->index(['yearcd']); 
        });

        DB::statement('ALTER TABLE non_gov_pledge_histories ENGINE = MyISAM');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        DB::statement('ALTER TABLE non_gov_pledge_histories ENGINE = InnoDB');

        Schema::table('non_gov_pledge_histories', function (Blueprint $table) {
            //
            $table->dropIndex(['yearcd']); 

            $table->string('source', 191)->nullable()->change();
            $table->string('tgb_reg_district', 191)->nullable()->change();
            $table->string('yearcd', 191)->nullable()->change();
            $table->string('org_code', 191)->nullable()->change();
            $table->string('emplid', 191)->nullable()->change();
            $table->string('pecsf_id', 191)->nullable()->change();
            $table->string('guid', 191)->nullable()->change();
            $table->string('event_type', 191)->nullable()->change();
            $table->string('event_sub_type', 191)->nullable()->change();
        });


    }
}
