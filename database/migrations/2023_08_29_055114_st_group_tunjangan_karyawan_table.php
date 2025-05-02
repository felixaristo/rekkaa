<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StGroupTunjanganKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_group_tunjangan_karyawan', function (Blueprint $table) {
            $table->increments('stgrouptunjangankaryawan_id');
            $table->string('stgrouptunjangankaryawan_name');
            $table->enum('stgrouptunjangankaryawan_active', [1,0])->default(1);
            $table->integer('ms_wajibpajak_id'); // id wajib pajak
            $table->timestamp('stgrouptunjangankaryawan_created_at')->useCurrent();
            $table->timestamp('stgrouptunjangankaryawan_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_group_tunjangan_karyawan');
    }
}
