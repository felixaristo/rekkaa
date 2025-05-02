<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsKaryawanMasakerjaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_karyawan_masakerja', function (Blueprint $table) {
            $table->bigIncrements('karyawanmasakerja_id');
            $table->integer('ms_karyawan_id');
            $table->enum('karyawanmasakerja_status', ['TETAP','KONTRAK','NONKARYAWAN']);
            $table->date('karyawanmasakerja_contract_begin');
            $table->date('karyawanmasakerja_contract_end')->nullable();
            // $table->enum('karyawanmasakerja_contract_now', [1,0])->default(0);
            $table->enum('karyawanmasakerja_calculation_method', ['GROSS','GROSS_UP','NETT','MIX']);
            $table->bigInteger('karyawanmasakerja_salary')->default(0);
            $table->enum('karyawanmasakerja_end_type', ['RESIGN', 'LAINNYA'])->nullable();
            $table->string('karyawanmasakerja_end_reason')->nullable();
            $table->enum('karyawanmasakerja_contracttype', ['PERPANJANG', 'BARU'])->default('BARU');
            $table->string('karyawanmasakerja_position');
            $table->string('karyawanmasakerja_bpjs')->nullable();
            $table->string('karyawanmasakerja_tunjangan')->nullable();
            $table->string('karyawanmasakerja_nik');
            $table->string('karyawanmasakerja_npwp');
            $table->string('ms_objekpajak_code')->nullable(); // jenis pajak jika NONKARYAWAN
            $table->text('karyawanmasakerja_data')->nullable(); // data from ms_karyawan
            $table->integer('ms_ptkp_id'); // status kawin
            $table->integer('ms_user_id'); // id user
            $table->integer('ms_wajibpajak_id'); // id wajib pajak
            $table->enum('karyawanmasakerja_active', [1,0])->default(1);
            $table->timestamp('karyawanmasakerja_created_at')->useCurrent();
            $table->timestamp('karyawanmasakerja_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_karyawan_masakerja');
    }
}
