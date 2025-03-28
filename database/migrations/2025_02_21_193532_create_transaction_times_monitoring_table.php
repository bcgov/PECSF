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

        Schema::create('transaction_times_monitoring', function (Blueprint $table) {
            //
            $table->id();

            $table->bigInteger('user_id');

            $table->string('action_type');
            $table->string('table_name');
            $table->bigInteger('tran_id')->nullable();

            $table->string('browser_name');
            $table->string('platform');
            $table->string('device');
            $table->string('ip');
            $table->string('user_guard')->nullable();
            $table->string('page');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->float('duration');
            
            $table->timestamps();


            $table->index(['duration'], 'duration_index');
            $table->index( ['table_name', 'action_type', 'duration'], 'table_action_duration_index');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::dropIfExists('transaction_times_monitoring');
        
    }
};
