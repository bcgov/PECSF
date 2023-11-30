<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bank_deposit_form_attachments', function (Blueprint $table) {
            //
            $table->string('filename')->nullable()->after('bank_deposit_form_id');
            $table->string('original_filename')->nullable()->after('filename');
            $table->string('mime')->nullable()->after('original_filename');

        });

        DB::statement("ALTER TABLE bank_deposit_form_attachments ADD COLUMN file MEDIUMBLOB AFTER `local_path` ");
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bank_deposit_form_attachments', function (Blueprint $table) {
            //
            $table->dropColumn('filename');
            $table->dropColumn('original_filename');
            $table->dropColumn('mime');
            $table->dropColumn('file');
        });

    }
};
