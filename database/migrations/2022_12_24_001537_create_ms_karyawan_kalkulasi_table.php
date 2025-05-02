<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsKaryawanKalkulasiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_karyawan_kalkulasi', function (Blueprint $table) {
            $table->bigIncrements('karyawankalkulasi_id');
            $table->integer('ms_karyawan_id');
            $table->integer('ms_karyawanmasakerja_id');
            $table->string('karyawankalkulasi_month');
            $table->year('karyawankalkulasi_year');
            $table->bigInteger('karyawankalkulasi_salary');
            $table->bigInteger('karyawankalkulasi_bruto');
            $table->bigInteger('karyawankalkulasi_pph21');
            $table->string('karyawankalkulasi_status');
            $table->string('karyawankalkulasi_method');
            $table->string('karyawankalkulasi_position');
            $table->text('karyawankalkulasi_bpjs')->nullable();
            $table->text('karyawankalkulasi_tunjangan')->nullable();
            $table->text('karyawankalkulasi_tunjanganjabatan')->nullable();
            $table->string('ms_objekpajak_code')->nullable();
            $table->string('ms_ptkp_id')->nullable();
            $table->text('karyawankalkulasi_data');
            $table->boolean('karyawankalkulasi_lock')->default(FALSE);
            $table->timestamp('karyawankalkulasi_lock_at')->nullable();
            $table->enum('karyawankalkulasi_active', [1,0])->default(1);
            $table->timestamp('karyawankalkulasi_created_at')->useCurrent();
            $table->timestamp('karyawankalkulasi_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_karyawan_kalkulasi');
    }
}
