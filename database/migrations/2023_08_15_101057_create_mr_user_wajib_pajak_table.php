<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMrUserWajibPajakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mr_user_wajib_pajak', function (Blueprint $table) {
            $table->bigIncrements('userwajibpajak_id');
            $table->bigInteger('ms_user_id');
            $table->bigInteger('ms_wajibpajak_id');
            $table->string('userwajibpajak_name');
            $table->string('userwajibpajak_phone')->nullable();
            $table->string('userwajibpajak_address')->nullable();
            
            $table->enum('userwajibpajak_owner', [1,0])->default(0);
            $table->enum('userwajibpajak_active', [1,0])->default(1);
            $table->timestamp('userwajibpajak_created_at')->useCurrent();
            $table->timestamp('userwajibpajak_updated_at')->nullable()->useCurrentOnUpdate();

            $table->foreign('ms_user_id')->references('user_id')->on('ms_user');
            $table->foreign('ms_wajibpajak_id')->references('wajibpajak_id')->on('ms_wajib_pajak');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mr_user_wajib_pajak');
    }
}
