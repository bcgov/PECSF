<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsOnBusinessUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_units', function (Blueprint $table) {
            //
            $table->string('code')->after('id');
            $table->date('effdt')->after('code');
            $table->string('status',1)->after('effdt');
            $table->text('notes')->nullable()->after('name');
            $table->bigInteger('created_by_id')->nullable()->after('notes');
            $table->bigInteger('updated_by_id')->nullable()->after('created_by_id');

            $table->dropColumn('business_unit_code');	
            $table->dropColumn('yearcd');	
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('business_units', function (Blueprint $table) {
            //
            $table->string('business_unit_code')->after('id');	
            $table->string('yearcd')->after('business_unit_code');		

            $table->dropColumn('code');
            $table->dropColumn('effdt');
            $table->dropColumn('status');
            $table->dropColumn('notes');
            $table->dropColumn('created_by_id');
            $table->dropColumn('updated_by_id');

        });
    }
}
