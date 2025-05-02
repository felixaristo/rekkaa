<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStPotonganKaryawanDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_potongan_karyawan_detail', function (Blueprint $table) {
            $table->increments('stpotongankaryawandet_id');
            $table->integer('st_potongankaryawan_id');
            $table->bigInteger('ms_karyawan_id');
            $table->enum('stpotongankaryawandet_active', [1,0])->default(1);
            $table->timestamp('stpotongankaryawandet_created_at')->useCurrent();
            $table->timestamp('stpotongankaryawandet_updated_at')->nullable()->useCurrentOnUpdate();

            $table->foreign('st_potongankaryawan_id')->references('stpotongankaryawan_id')->on('st_potongan_karyawan'); 
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
        Schema::dropIfExists('st_potongan_karyawan_detail');
    }
}
