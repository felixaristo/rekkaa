<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_admin', function (Blueprint $table) {
            $table->increments('admin_id');
            $table->string('admin_name');
            $table->string('admin_phone')->nullable();
            $table->string('admin_email')->unique();
            $table->timestamp('admin_email_verified_at')->nullable();
            $table->string('admin_password');
            $table->enum('admin_active', [1,0])->default(1);
            $table->rememberToken('admin_remember_token');
            $table->string('admin_forgot_token');
            $table->enum('admin_type', ['ADMINISTRATOR', 'FINANCE', 'HR']);
            $table->timestamp('admin_created_at')->useCurrent();
            $table->timestamp('admin_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_admin');
    }
}
