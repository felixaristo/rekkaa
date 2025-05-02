<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStTunjanganKaryawanDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_tunjangan_karyawan_detail', function (Blueprint $table) {
            $table->increments('sttunjangankaryawandet_id');
            $table->integer('st_tunjangankaryawan_id');
            $table->bigInteger('ms_karyawan_id');
            $table->enum('sttunjangankaryawandet_active', [1,0])->default(1);
            $table->timestamp('sttunjangankaryawandet_created_at')->useCurrent();
            $table->timestamp('sttunjangankaryawandet_updated_at')->nullable()->useCurrentOnUpdate();

            $table->foreign('st_tunjangankaryawan_id')->references('sttunjangankaryawan_id')->on('st_tunjangan_karyawan'); 
            $table->foreign('ms_karyawan_id')->references('karyawan_id')->on('ms_karyawan'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_tunjangan_karyawan_detail');
    }
}
