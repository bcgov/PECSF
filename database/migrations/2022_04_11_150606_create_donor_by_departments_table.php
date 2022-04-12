<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonorByDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donor_by_departments', function (Blueprint $table) {
            $table->id();
            $table->string('bi_department_id');
            $table->string('yearcd');
            $table->integer('donors');
            $table->bigInteger('department_id')->nullable();
            $table->timestamps();

            $table->unique(['department_id','yearcd']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('donor_by_departments');
    }
}
