<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStTaperaKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_tapera_karyawan', function (Blueprint $table) {
            $table->increments('sttaperakaryawan_id');
            $table->text('sttaperakaryawan_value');
            $table->enum('sttaperakaryawan_active', [1,0])->default(1);
            $table->integer('ms_wajibpajak_id'); // id wajib pajak
            $table->timestamp('sttaperakaryawan_created_at')->useCurrent();
            $table->timestamp('sttaperakaryawan_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_tapera_karyawan');
    }
}
