<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrAkunBiayaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_akun_biaya', function (Blueprint $table) {
            $table->bigIncrements('akunbiaya_id');
            $table->string('akunbiaya_nomor');
            $table->string('akunbiaya_nobukti');
            $table->timestamp('akunbiaya_trx_at');
            $table->integer('ms_akuncustomer_id');
            $table->integer('ms_akun_id')->references('akun_id')->on('ms_akun');
            $table->integer('ms_akunaruskas_id')->references('akunaruskas_id')->on('ms_akun_aruskas');
            $table->bigInteger('akunbiaya_total');
            $table->string('akunbiaya_description');
            $table->string('akunbiaya_file');
            $table->enum('akunbiaya_carapembayaran', ['CASH','DEBET','KREDIT','TRANSFER']);
            $table->integer('ms_wajibpajak_id');
            $table->foreign('ms_wajibpajak_id')->references('wajibpajak_id')->on('ms_wajib_pajak');
            $table->integer('ms_user_id');
            $table->foreign('ms_user_id')->references('user_id')->on('ms_user');
            $table->enum('akunbiaya_active', [1,0])->default(1);
            $table->timestamp('akunbiaya_created_at')->useCurrent();
            $table->timestamp('akunbiaya_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_akun_biaya');
    }
}
