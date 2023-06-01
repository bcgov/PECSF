<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEligibleEmployeeByBUSTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eligible_employee_by_bus', function (Blueprint $table) {
            $table->id();


            $table->integer("campaign_year");
            $table->date("as_of_date");
            $table->string('organization_code')->nullable();
            $table->string('business_unit_code')->nullable();
            $table->string('business_unit_name')->nullable();
            $table->float('ee_count')->nullable();

            $table->timestamps();

            $table->index(['campaign_year', 'as_of_date', 'organization_code', 'business_unit_code'], 'year_as_of_date_org_bu');
            $table->index(['campaign_year', 'organization_code', 'business_unit_code'], 'year_org_bu');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eligible_employee_by_bus');
    }
}
