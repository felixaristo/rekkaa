<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsSubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_subscription', function (Blueprint $table) {
            $table->bigIncrements('subscription_id');
            $table->string('subscription_title');
            $table->text('subscription_description');
            $table->text('subscription_data');
            $table->bigInteger('subscription_price');
            $table->bigInteger('subscription_priceold');
            $table->integer('subscription_order');
            $table->enum('subscription_type', ['FREE','BRONZE','SILVER','GOLD','PLATINUM']);
            $table->enum('subscription_entity_type', ['BADAN','INDIVIDU']);
            $table->text('subscription_discount'); // json discount [{value_type: 'TETAP/PERSEN', period: 1(month), value: 100000, type:'DISCOUNT/VOUCHER'}]
            $table->float('subscription_monthlydiscount')->default(0);
            $table->float('subscription_quarterlydiscount')->default(0);
            $table->float('subscription_semiannualdiscount')->default(0);
            $table->float('subscription_yearlydiscount')->default(0);
            $table->enum('subscription_ppntype', ['INCLUDE', 'EXCLUDE'])->default('INCLUDE');
            $table->float('subscription_ppn')->default(0); // in percentage
            $table->enum('subscription_active', [1,0])->default(1);
            $table->enum('subscription_default', [1,0])->default(1);
            $table->timestamp('subscription_created_at')->useCurrent();
            $table->timestamp('subscription_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_subscription');
    }
}
