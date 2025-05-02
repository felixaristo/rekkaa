<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStLemburKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_lembur_karyawan', function (Blueprint $table) {
            $table->increments('stlemburkaryawan_id');
            $table->enum('stlemburkaryawan_taxable', [1,0])->default(0);
            $table->enum('stlemburkaryawan_active', [1,0])->default(1);
            $table->integer('ms_wajibpajak_id'); // id wajib pajak
            $table->timestamp('stlemburkaryawan_created_at')->useCurrent();
            $table->timestamp('stlemburkaryawan_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_lembur_karyawan');
    }
}
