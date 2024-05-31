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
        Schema::table('employee_jobs', function (Blueprint $table) {
            //
            $table->date('hire_dt')->nullable()->after('effseq');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_jobs', function (Blueprint $table) {
            //
            $table->dropColumn('hire_dt');
        });
    }
};
