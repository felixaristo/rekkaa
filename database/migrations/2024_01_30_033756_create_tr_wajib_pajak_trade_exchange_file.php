<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrWajibPajakTradeExchangeFile extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_wajib_pajak_trade_exchange_file', function (Blueprint $table) {
            $table->increments('wajibpajaktradeexchangefile_id');
            $table->integer('ms_wajibpajak_id');
            $table->foreign('ms_wajibpajak_id')->references('wajibpajak_id')->on('ms_wajib_pajak');
            $table->integer('ms_wajibpajaktradeexchange_id');
            $table->foreign('ms_wajibpajaktradeexchange_id')->references('wajibpajaktradeexchange_id')->on('ms_wajib_pajak_trade_exchange');
            $table->string('wajibpajaktradeexchangefile_filename');
            $table->string('wajibpajaktradeexchangefile_size');
            $table->string('wajibpajaktradeexchangefile_objectstorage');
            $table->timestamp('wajibpajaktradeexchangefile_created_at')->useCurrent();
            $table->timestamp('wajibpajaktradeexchangefile_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_wajib_pajak_trade_exchange_file');
    }
}
