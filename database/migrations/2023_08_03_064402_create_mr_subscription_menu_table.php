<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMrSubscriptionMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mr_subscription_menu', function (Blueprint $table) {
            $table->bigIncrements('subscriptionmenu_id');
            $table->integer('ms_subscription_id');
            $table->string('subscriptionmenu_menus'); // 1.2.3.4,5 ms_menu
            $table->enum('subscriptionmenu_active', [1,0])->default(1);
            $table->enum('subscriptionmenu_type', ['BADAN','INDIVIDU'])->default(1);
            $table->timestamp('subscriptionmenu_created_at')->useCurrent();
            $table->timestamp('subscriptionmenu_updated_at')->nullable()->useCurrentOnUpdate();

            $table->foreign('ms_subscription_id')->references('subscription_id')->on('ms_subscription'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_subscription_menu');
    }
}
