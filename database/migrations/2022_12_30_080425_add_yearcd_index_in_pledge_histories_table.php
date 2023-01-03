<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddYearcdIndexInPledgeHistoriesTable extends Migration
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
            $table->dropIndex('pledge_histories_guid_plus_others');
            $table->dropIndex(['GUID']); 

            $table->string('emplid', 20)->nullable()->change();
            $table->string('event_type', 50)->nullable()->change();
            $table->string('event_sub_type', 50)->nullable()->change();

            $table->index(['yearcd']); 
        });
        
        DB::statement('ALTER TABLE pledge_histories ENGINE = MyISAM');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        DB::statement('ALTER TABLE pledge_histories ENGINE = InnoDB');
        
        Schema::table('pledge_histories', function (Blueprint $table) {
            //
            $table->string('emplid', 191)->nullable()->change();
            $table->string('event_type', 191)->nullable()->change();
            $table->string('event_sub_type', 191)->nullable()->change();
            $table->dropIndex(['yearcd']); 

            $table->index(['GUID']); 
            $table->index(['source', 'GUID', 'yearcd', 'campaign_type', 'frequency', 'tgb_reg_district'], 'pledge_histories_guid_plus_others');
            
        });
    }
}
