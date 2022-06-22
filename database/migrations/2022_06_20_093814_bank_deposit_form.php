<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class BankDepositForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_deposit_forms', function (Blueprint $table) {
            $table->id();
            $table->string('organization_code');
            $table->string('form_submitter_id');
            $table->string('event_type');
            $table->string('sub_type');
            $table->datetime('deposit_date');
            $table->float('deposit_amount');
            $table->text('description');
            $table->string('employment_city');
            $table->integer('region_id');
            $table->integer('department_id');
            $table->integer('regional_pool_id');
            $table->string('address_line_1');
            $table->string('address_line_2');
            $table->string('address_city');
            $table->string('address_province');
            $table->string('address_postal_code');
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
        //
        Schema::dropIfExists('bank_deposit_forms');
    }
}
