<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStTunjanganKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_tunjangan_karyawan', function (Blueprint $table) {
            $table->increments('sttunjangankaryawan_id');
            $table->string('sttunjangankaryawan_code');
            $table->string('sttunjangankaryawan_name');
            $table->bigInteger('sttunjangankaryawan_value')->default(0);
            $table->text('sttunjangankaryawan_formula')->nullable();
            $table->string('st_sttunjangankaryawan_id')->nullable(); // for FORMULA e.g 1,2,3 
            $table->enum('sttunjangankaryawan_calculation', ['JUMLAH_TETAP','FORMULA'])->default('JUMLAH_TETAP');
            $table->enum('sttunjangankaryawan_period', ['HARI','MINGGU','BULAN','TAHUN'])->default('HARI');
            $table->integer('sttunjangankaryawan_paymentperiod')->default(0); // in months 1=January
            // $table->enum('sttunjangankaryawan_recievelate', [1,0])->default(0);
            // $table->enum('sttunjangankaryawan_recieveabsence', [1,0])->default(0);
            $table->enum('sttunjangankaryawan_taxable', [1,0])->default(0);
            $table->integer('st_grouptunjangankaryawan_id');
            $table->enum('sttunjangankaryawan_type', ['THR','BONUS'])->nullable(); // for TAHUN
            $table->enum('sttunjangankaryawan_active', [1,0])->default(1);
            $table->integer('ms_wajibpajak_id'); // id wajib pajak
            $table->boolean('sttunjangankaryawan_is_all')->default(FALSE);
            $table->integer('sttunjangankaryawan_used_for')->default(0); // 0 => Karyawan Tertentu, 1 => Semua Karyawan, 2 => Hanya Perempuan, 3 => Hanya Laki-laki, 4 => Berdasarkan Divisi, 5 => Berdasarkan Jabatan
            $table->string('sttunjangankaryawan_division_jabatan')->nullable(); // for divisi or jabatan 
            $table->timestamp('sttunjangankaryawan_created_at')->useCurrent();
            $table->timestamp('sttunjangankaryawan_updated_at')->nullable()->useCurrentOnUpdate();
        });
        // [
        //     [
        //         'type' => 'NUMERIC',
        //         'value' => '1',
        //     ],
        //     [
        //         'type' => 'OPERATOR',
        //         'value' => '+',
        //     ],
        //     [
        //         'type' => 'ID',
        //         'value' => '1',
        //     ]
        // ];
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_tunjangan_karyawan');
    }
}
