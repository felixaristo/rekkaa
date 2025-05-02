<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class StGroupPotonganKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_group_potongan_karyawan', function (Blueprint $table) {
            $table->increments('stgrouppotongankaryawan_id');
            $table->string('stgrouppotongankaryawan_name');
            $table->enum('stgrouppotongankaryawan_active', [1,0])->default(1);
            $table->integer('ms_wajibpajak_id'); // id wajib pajak
            $table->timestamp('stgrouppotongankaryawan_created_at')->useCurrent();
            $table->timestamp('stgrouppotongankaryawan_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_group_potongan_karyawan');
    }
}
