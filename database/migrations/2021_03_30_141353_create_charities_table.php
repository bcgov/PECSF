<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charities', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->nullable();
            $table->string('charity_name')->nullable();
            $table->string('charity_status')->nullable();
            $table->date('effective_date_of_status')->nullable();
            $table->string('sanction')->nullable();
            $table->string('designation_code')->nullable();
            $table->string('category_code')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->char('province',2)->nullable();
            $table->char('country',2)->nullable();
            $table->string('postal_code')->nullable();
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
        Schema::dropIfExists('charity');
    }
}
