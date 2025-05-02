<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrWajibPajakSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_wajib_pajak_subscription', function (Blueprint $table) {
            $table->bigIncrements('wajibpajaksubscription_id');
            $table->bigInteger('ms_wajibpajak_id');
            $table->bigInteger('ms_user_id');
            $table->integer('ms_subscription_id');

            $table->text('wajibpajaksubscription_permission');
            $table->timestamp('wajibpajaksubscription_activated_at');
            $table->timestamp('wajibpajaksubscription_expired_at');
            $table->bigInteger('tr_userorder_id');
            $table->enum('wajibpajaksubscription_active', [1,0])->default(1);
            $table->timestamp('wajibpajaksubscription_created_at')->useCurrent();
            $table->timestamp('wajibpajaksubscription_updated_at')->nullable()->useCurrentOnUpdate();

            $table->foreign('ms_wajibpajak_id')->references('wajibpajak_id')->on('ms_wajib_pajak'); 
            $table->foreign('ms_subscription_id')->references('subscription_id')->on('ms_subscription'); 
            $table->foreign('tr_userorder_id')->references('userorder_id')->on('tr_user_order'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_wajib_pajak_subscription');
    }
}
