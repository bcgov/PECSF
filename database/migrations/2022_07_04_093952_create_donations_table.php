<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            
            $table->string('org_code')->nullable();
            $table->string("emplid")->nullable();
            $table->string('name')->nullable();
            $table->string('yearcd')->nullable();
            $table->date('pay_end_date')->nullable();
            $table->string('source_type',2)->nullable();
            $table->string('frequency')->nullable();
            $table->float('amount')->nullable();

            $table->bigInteger('process_history_id')->nullable();
            $table->string('process_status',1)->nullable();
            $table->datetime('process_date')->nullable();

            $table->bigInteger('created_by_id')->nullable();
            $table->bigInteger('updated_by_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donations');
    }
}
