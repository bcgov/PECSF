<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Lock PECSF reporting window to September 1 - January 15
     */
    public function up()
    {
        $current_year = now()->year;
        $september_first = Carbon::createFromDate($current_year, 9, 1)->format('Y-m-d');
        $january_fifteenth = Carbon::createFromDate($current_year + 1, 1, 15)->format('Y-m-d');

        DB::table('settings')->update([
            'challenge_start_date' => $september_first,
            'challenge_final_date' => $january_fifteenth,
            'campaign_start_date' => $september_first,
            'campaign_final_date' => $january_fifteenth,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Reverting locked dates would cause data inconsistency, so no down action
    }
};
