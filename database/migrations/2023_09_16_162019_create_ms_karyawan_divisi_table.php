<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsKaryawanDivisiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_karyawan_divisi', function (Blueprint $table) {
            $table->increments('karyawandivisi_id');
            $table->bigInteger('ms_user_id');
            $table->bigInteger('ms_wajibpajak_id');
            $table->string('karyawandivisi_name');
            $table->enum('karyawandivisi_active', [1,0])->default(1);
            $table->timestamp('karyawandivisi_created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_karyawan_divisi');
    }
}
