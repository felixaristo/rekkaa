<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrAkunBiayaDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_akun_biaya_detail', function (Blueprint $table) {
            $table->bigIncrements('akunbiayadet_id');
            $table->integer('tr_akunbiaya_id')->references('akunbiaya_id')->on('tr_akun_biaya');
            $table->integer('ms_akun_id')->references('akun_id')->on('ms_akun');
            $table->bigInteger('akunbiayadet_total');
            $table->string('akunbiayadet_description');
            $table->integer('ms_wajibpajak_id');
            $table->foreign('ms_wajibpajak_id')->references('wajibpajak_id')->on('ms_wajib_pajak');
            $table->integer('ms_user_id');
            $table->foreign('ms_user_id')->references('user_id')->on('ms_user');
            $table->enum('akunbiayadet_active', [1,0])->default(1);
            $table->timestamp('akunbiayadet_created_at')->useCurrent();
            $table->timestamp('akunbiayadet_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_akun_biaya_detail');
    }
}
