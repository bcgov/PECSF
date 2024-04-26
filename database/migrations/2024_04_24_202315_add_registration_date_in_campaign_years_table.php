<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('campaign_years', function (Blueprint $table) {
            //
            $table->date('volunteer_start_date')->nullable()->after('close_date');
            $table->date('volunteer_end_date')->nullable()->after('volunteer_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaign_years', function (Blueprint $table) {
            //
            $table->dropColumn('volunteer_start_date');
            $table->dropColumn('volunteer_end_date');
        });
    }
};
