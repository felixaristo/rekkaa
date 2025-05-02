<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrNotificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_notification', function (Blueprint $table) {
            $table->increments('notification_id');
            $table->enum('notification_type', ['EMAIL', 'WHATSAPP'])->default('EMAIL');
            $table->string('notification_from');
            $table->string('notification_to');
            $table->bigInteger('ms_user_id');
            $table->bigInteger('ms_wajibpajak_id')->nullable();
            $table->string('notification_title');
            $table->string('notification_text')->nullable();
            $table->string('notification_view')->nullable();
            $table->string('notification_data')->nullable();
            $table->string('notification_attachment')->nullable();
            $table->string('ms_permission_code')->nullable();
            $table->enum('notification_status', ['SEND', 'PENDING'])->default('PENDING');
            $table->timestamp('notification_created_at')->useCurrent();
            $table->timestamp('notification_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_notification');
    }
}
