<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Nullabledepositformfields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_deposit_forms', function (Blueprint $table) {
            $table->string('organization_code')->nullable()->change();
            $table->string('form_submitter_id')->nullable()->change();
            $table->string('event_type')->nullable()->change();
            $table->string('sub_type')->nullable()->change();
            $table->datetime('deposit_date')->nullable()->change();
            $table->float('deposit_amount')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->string('employment_city')->nullable()->change();
            $table->integer('region_id')->nullable()->change();
            $table->integer('department_id')->nullable()->change();
            $table->integer('regional_pool_id')->nullable()->change();
            $table->string('address_line_1')->nullable()->change();
            $table->string('address_line_2')->nullable()->change();
            $table->string('address_city')->nullable()->change();
            $table->string('address_province')->nullable()->change();
            $table->string('address_postal_code')->nullable()->change();
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
    }
}
