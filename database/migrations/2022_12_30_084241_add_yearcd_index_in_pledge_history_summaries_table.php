<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearcdIndexInPledgeHistorySummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pledge_history_summaries', function (Blueprint $table) {
            //
            $table->string('emplid', 20)->nullable()->change();
            $table->string('region', 50)->nullable()->change();
            $table->string('event_type', 50)->nullable()->change();
            $table->string('event_sub_type', 50)->nullable()->change();

            $table->index(['yearcd']); 
        });

        DB::statement('ALTER TABLE pledge_history_summaries ENGINE = MyISAM');

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        DB::statement('ALTER TABLE pledge_history_summaries ENGINE = InnoDB');

        Schema::table('pledge_history_summaries', function (Blueprint $table) {
            //
            $table->string('emplid', 191)->nullable()->change();
            $table->string('region', 191)->nullable()->change();
            $table->string('event_type', 191)->nullable()->change();
            $table->string('event_sub_type', 191)->nullable()->change();

            $table->dropIndex(['yearcd']); 
        
        });
    }
}
