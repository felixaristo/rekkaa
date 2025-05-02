<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_user', function (Blueprint $table) {
            $table->bigIncrements('user_id');
            $table->string('user_name');
            $table->string('user_phone');
            $table->string('user_email')->unique();
            $table->timestamp('user_email_verified_at')->nullable();
            $table->string('user_password');
            $table->enum('user_active', [1,0])->default(1); // 1 = active, 0 = pending
            $table->rememberToken('user_remember_token');
            $table->string('user_verified_token');
            $table->string('user_forgot_token');
            $table->timestamp('user_created_at')->useCurrent();
            $table->timestamp('user_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_user');
    }
}
