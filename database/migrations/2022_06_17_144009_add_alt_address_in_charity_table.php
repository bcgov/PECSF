<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAltAddressInCharityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charities', function (Blueprint $table) {
            //
            $table->char('use_alt_address',1)->nullable()->after('postal_code');
            $table->string('alt_address1')->nullable()->after('use_alt_address');
            $table->string('alt_address2')->nullable()->after('alt_address1');
            $table->string('alt_city')->nullable()->after('alt_address2');
            $table->char('alt_province',10)->nullable()->after('alt_city');
            $table->char('alt_country',10)->nullable()->after('alt_province');
            $table->string('alt_postal_code')->nullable()->after('alt_country');

            $table->string('financial_contact_name')->nullable()->after('alt_postal_code');
            $table->string('financial_contact_title')->nullable()->after('financial_contact_name');
            $table->string('financial_contact_email')->nullable()->after('financial_contact_title');

            $table->bigInteger('created_by_id')->nullable()->after('url');
            $table->bigInteger('updated_by_id')->nullable()->after('created_by_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('charities', function (Blueprint $table) {
            //
            $table->dropColumn('use_alt_address');
            $table->dropColumn('alt_address1');
            $table->dropColumn('alt_address2');
            $table->dropColumn('alt_city');
            $table->dropColumn('alt_province');
            $table->dropColumn('alt_country');
            $table->dropColumn('alt_postal_code');

            $table->dropColumn('financial_contact_name');
            $table->dropColumn('financial_contact_title');
            $table->dropColumn('financial_contact_email');

            $table->dropColumn('created_by_id');
            $table->dropColumn('updated_by_id');
        });
    }
}
