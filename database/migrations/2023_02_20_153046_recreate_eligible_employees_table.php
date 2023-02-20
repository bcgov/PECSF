<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreateEligibleEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('elligible_employees', function (Blueprint $table) {
            $table->id();
            $table->string('as_of_date')->nullable();
            $table->integer('ee_count')->nullable();
            $table->string('business_unit')->nullable();
            $table->string('business_unit_name')->nullable();
            $table->string('cde')->nullable();
            $table->string('year')->nullable();
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
        Schema::dropIfExists('elligible_employees');
    }
}
