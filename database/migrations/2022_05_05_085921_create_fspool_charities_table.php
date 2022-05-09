<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFSPoolCharitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_s_pool_charities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('f_s_pool_id');
            $table->bigInteger('charity_id');
            $table->decimal('percentage', $precision = 8, $scale = 2);
            $table->string('status',1);
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->string('contact_title')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('notes')->nullable();
            $table->string('image')->nullable();
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
        Schema::dropIfExists('f_s_pool_charities');
    }
}
