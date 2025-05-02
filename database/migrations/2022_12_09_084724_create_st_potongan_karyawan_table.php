<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStPotonganKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_potongan_karyawan', function (Blueprint $table) {
            $table->increments('stpotongankaryawan_id');
            $table->string('stpotongankaryawan_code');
            $table->string('stpotongankaryawan_name');
            $table->bigInteger('stpotongankaryawan_value')->default(0);
            $table->text('stpotongankaryawan_formula')->nullable();
            $table->enum('stpotongankaryawan_type', ['TELAT','ABSEN','TETAP']);
            $table->enum('stpotongankaryawan_method', ['HARI','BULAN','KELIPATAN_HARI','KELIPATAN_BULAN','PRORATA','TETAP']);
            $table->integer('stpotongankaryawan_accumulationtime')->default(0); // kelipatan hari / bulan
            $table->string('stpotongankaryawan_period')->default('HARI');
            // $table->enum('stpotongankaryawan_maxtype', ['TETAP','PERSEN','TIDAK_BERLAKU']);
            // $table->float('stpotongankaryawan_maxtypevalue')->default(0); // limit by max type
            // $table->string('stpotongankaryawan_maxtypevalueformula')->nullable();
            $table->enum('stpotongankaryawan_taxable', [1,0])->default(0);
            $table->integer('st_grouppotongankaryawan_id');
            $table->enum('stpotongankaryawan_active', [1,0])->default(1);
            $table->integer('ms_wajibpajak_id'); // id wajib pajak
            $table->boolean('stpotongankaryawan_is_all')->default(FALSE);
            $table->integer('stpotongankaryawan_used_for')->default(0); // 0 => Karyawan Tertentu, 1 => Semua Karyawan, 2 => Hanya Perempuan, 3 => Hanya Laki-laki, 4 => Berdasarkan Divisi, 5 => Berdasarkan Jabatan
            $table->string('stpotongankaryawan_division_jabatan')->nullable(); // for divisi or jabatan 
            $table->timestamp('stpotongankaryawan_created_at')->useCurrent();
            $table->timestamp('stpotongankaryawan_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_potongan_karyawan');
    }
}
