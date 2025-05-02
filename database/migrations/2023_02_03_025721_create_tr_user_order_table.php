<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrUserOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_user_order', function (Blueprint $table) {
            $table->bigIncrements('userorder_id');
            $table->bigInteger('ms_user_id');
            $table->bigInteger('ms_wajibpajak_id');
            $table->integer('ms_subscription_id');
            $table->string('userorder_no');
            $table->integer('userorder_qty')->default(0);
            $table->bigInteger('userorder_price')->default(0);
            $table->float('userorder_discount')->default(0);
            $table->bigInteger('userorder_addon')->default(0);
            $table->bigInteger('userorder_ppn')->default(0);
            $table->bigInteger('userorder_total')->default(0);
            $table->enum('userorder_subscriptiontype', ['FREE','BRONZE','SILVER','GOLD','PLATINUM'])->default('FREE');
            $table->integer('userorder_paymentperiode')->default(1); // 1,6,12
            $table->enum('userorder_status', ['PENDING','CANCEL','EXPIRED','PAID'])->default('PENDING');
            $table->enum('userorder_kind', ['SUBSCRIPTION','EXTEND','UPGRADE'])->default('SUBSCRIPTION');
            $table->timestamp('userorder_expired_at');
            $table->timestamp('userorder_paid_at');
            $table->string('userorder_description');
            $table->string('userorder_xenditurl')->nullable();
            $table->text('userorder_xenditdata')->nullable();
            $table->text('userorder_xenditcalbackdata')->nullable();
            $table->string('userorder_paperurl')->nullable();
            $table->text('userorder_paperdata')->nullable();
            $table->text('userorder_subscriptiondata');
            $table->string('userorder_paymentmethod');
            $table->string('userorder_paymentchannel');
            $table->enum('userorder_active', [1,0])->default(1);
            $table->timestamp('userorder_created_at')->useCurrent();
            $table->timestamp('userorder_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_user_order');
    }
}
