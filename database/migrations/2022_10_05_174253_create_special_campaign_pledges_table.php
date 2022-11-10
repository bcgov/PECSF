<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSpecialCampaignPledgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('special_campaign_pledges', function (Blueprint $table) {

            $table->id();

            $table->bigInteger('organization_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->string('pecsf_id')->nullable();
            $table->string('yearcd');
            $table->integer('seqno');

            $table->bigInteger('special_campaign_id')->nullable();

            $table->float('one_time_amount');

            $table->char('ods_export_status',1)->nullable();
            $table->timestamp('ods_export_at')->nullable();

            $table->bigInteger('created_by_id')->nullable();
            $table->bigInteger('updated_by_id')->nullable();

            $table->timestamps();

            $table->index(['organization_id', 'user_id', 'pecsf_id', 'yearcd', 'seqno'], 'tran_key_index');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('special_campaign_pledges');
    }
}
