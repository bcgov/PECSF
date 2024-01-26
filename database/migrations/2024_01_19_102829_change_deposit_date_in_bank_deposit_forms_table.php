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
        Schema::table('bank_deposit_forms', function (Blueprint $table) {
            //
            $table->date('deposit_date')->change();     
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bank_deposit_forms', function (Blueprint $table) {
            //
            $table->datetime('deposit_date')->change();     
        });
    }
};
