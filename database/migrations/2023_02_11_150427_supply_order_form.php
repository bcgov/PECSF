<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SupplyOrderForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supply_order_forms', function (Blueprint $table) {
            $table->id();
            $table->integer('calendar');
            $table->integer('posters');
            $table->integer('stickers');
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('business_unit_id');
            $table->integer("include_name");
            $table->string("unit_suite_floor");
            $table->string("physical_address");
            $table->string("city");
            $table->string("province");
            $table->string("postal_code");
            $table->string("po_box");
            $table->dateTime("date_required");
            $table->string("comments");
            $table->string("address_type");
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
    }
}
