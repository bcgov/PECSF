<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Roll forward every date on the /settings/challenge page to the 2026 campaign
     * year cycle (Sept 1, 2026 - Jan 15, 2027), replacing the prior campaign year's
     * values across both the "Statistics Page Updates" and "Daily Campaign Updates"
     * sections, plus the Eligible Employee snapshot capture dates.
     */
    public function up()
    {
        DB::table('settings')->update([
            // Statistics Page Updates
            'challenge_start_date' => '2026-09-01',
            'challenge_end_date'   => '2026-11-10',
            'challenge_final_date' => '2027-01-15',

            // Daily Campaign Updates
            'campaign_start_date' => '2026-09-01',
            'campaign_end_date'   => '2026-11-10',
            'campaign_final_date' => '2027-01-15',

            // Eligible Employee Snapshot Process
            'ee_snapshot_date_1' => '2026-09-01',
            'ee_snapshot_date_2' => '2026-10-15',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Reverting to last cycle's dates would cause data inconsistency, so no down action
    }
};
