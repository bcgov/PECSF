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
        Schema::dropIfExists('volunteer_profiles');

        Schema::create('volunteer_profiles', function (Blueprint $table) {
            //
            $table->id();
            $table->string('campaign_year');
            $table->string('organization_code');
            $table->string('emplid')->nullable();
            $table->string('pecsf_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            $table->string('business_unit_code');
            $table->integer('no_of_years');
            $table->string('preferred_role');

            $table->string('address_type');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('opt_out_recongnition');

            $table->bigInteger('created_by_id')->nullable();
            $table->bigInteger('updated_by_id')->nullable();

            $table->timestamps();

            $table->index(['campaign_year', 'organization_code', 'emplid'], 'cy_org_emplid'); 
            $table->index(['campaign_year', 'organization_code', 'pecsf_id'], 'cy_org_pecsf_id'); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('volunteer_profiles', function (Blueprint $table) {
            //
            Schema::dropIfExists('volunteer_profiles');
        });
    }
};
