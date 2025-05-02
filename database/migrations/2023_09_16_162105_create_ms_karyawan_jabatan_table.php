<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsKaryawanJabatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_karyawan_jabatan', function (Blueprint $table) {
            $table->increments('karyawanjabatan_id');
            $table->bigInteger('ms_user_id');
            $table->bigInteger('ms_wajibpajak_id');
            $table->string('karyawanjabatan_name');
            $table->enum('karyawanjabatan_active', [1,0])->default(1);
            $table->timestamp('karyawanjabatan_created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_karyawan_jabatan');
    }
}
