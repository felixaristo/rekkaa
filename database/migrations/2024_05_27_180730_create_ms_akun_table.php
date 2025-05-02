<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsAkunTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_akun', function (Blueprint $table) {
            $table->increments('akun_id');
            $table->enum('akun_header', ['y','t'])->default('t');
            $table->integer('akun_parent')->nullable();
            $table->integer('akun_level');
            $table->string('akun_kode');
            $table->string('akun_name');
            $table->enum('akun_saldo_normal', ['DEBET', 'KREDIT']);
            $table->enum('akun_type', ['ASET', 'KEWAJIBAN', 'MODAL', 'PENDAPATAN', 'BEBAN']);
            $table->enum('akun_laporan', ['NERACA', 'LABA RUGI']);
            $table->string('akun_description')->nullable();
            $table->enum('akun_active', [1,0])->default(1);
            $table->timestamp('akun_created_at')->useCurrent();
            $table->timestamp('akun_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_akun');
    }
}
