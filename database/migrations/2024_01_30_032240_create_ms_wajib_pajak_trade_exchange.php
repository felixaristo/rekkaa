<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsWajibPajakTradeExchange extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_wajib_pajak_trade_exchange', function (Blueprint $table) {
            $table->increments('wajibpajaktradeexchange_id');
            $table->integer('ms_wajibpajak_id');
            $table->string('wajibpajaktradeexchange_enid');
            $table->string('wajibpajaktradeexchange_fullname');
            $table->string('wajibpajaktradeexchange_email');
            $table->string('wajibpajaktradeexchange_main_number');
            $table->string('wajibpajaktradeexchange_additional_number');
            $table->string('wajibpajaktradeexchange_fax')->nullable();
            $table->string('wajibpajaktradeexchange_main_pic_name');
            $table->string('wajibpajaktradeexchange_main_pic_number');
            $table->string('wajibpajaktradeexchange_main_pic_email');
            $table->text('wajibpajaktradeexchange_additional_pic')->nullable();
            $table->string('wajibpajaktradeexchange_billing_address')->nullable();
            $table->integer('wajibpajaktradeexchange_billing_countries')->nullable();
            $table->integer('wajibpajaktradeexchange_billing_province')->nullable();
            $table->integer('wajibpajaktradeexchange_billing_city')->nullable();
            $table->string('wajibpajaktradeexchange_billing_postal_code')->nullable();
            $table->boolean('wajibpajaktradeexchange_is_billing_shipping')->default(TRUE);
            $table->string('wajibpajaktradeexchange_shipping_address')->nullable();
            $table->integer('wajibpajaktradeexchange_shipping_countries')->nullable();
            $table->integer('wajibpajaktradeexchange_shipping_province')->nullable();
            $table->integer('wajibpajaktradeexchange_shipping_city')->nullable();
            $table->string('wajibpajaktradeexchange_shipping_postal_code')->nullable();
            $table->string('ms_bank_id');
            $table->string('wajibpajaktradeexchange_bank_account');
            $table->string('wajibpajaktradeexchange_bank_number');
            $table->string('wajibpajaktradeexchange_npwp');
            $table->text('wajibpajaktradeexchange_note')->nullable();
            $table->boolean('wajibpajaktradeexchange_active')->default(TRUE);
            $table->timestamp('wajibpajaktradeexchange_created_at')->useCurrent();
            $table->timestamp('wajibpajaktradeexchange_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_wajib_pajak_trade_exchange');
    }
}
