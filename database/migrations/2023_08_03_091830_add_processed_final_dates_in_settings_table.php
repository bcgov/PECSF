<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->date('challenge_processed_final_date')->nullable()->after('challenge_final_date');
            $table->date('campaign_processed_final_date')->nullable()->after('campaign_final_date');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
            $table->dropColumn('challenge_processed_final_date');
            $table->dropColumn('campaign_processed_final_date');

        });
    }
};
