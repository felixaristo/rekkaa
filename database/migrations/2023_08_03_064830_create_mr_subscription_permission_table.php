<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMrSubscriptionPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mr_subscription_permission', function (Blueprint $table) {
            $table->bigIncrements('subscriptionpermission_id');
            $table->integer('ms_subscription_id');
            $table->text('subscriptionpermission_permissions'); // array object {key: value}
            $table->enum('subscriptionpermission_active', [1,0])->default(1);
            $table->enum('subscriptionpermission_type', ['BADAN','INDIVIDU'])->default(1);
            $table->timestamp('subscriptionpermission_created_at')->useCurrent();
            $table->timestamp('subscriptionpermission_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_subscription_permission');
    }
}
