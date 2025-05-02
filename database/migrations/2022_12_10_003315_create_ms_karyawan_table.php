<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_karyawan', function (Blueprint $table) {
            $table->bigIncrements('karyawan_id');
            $table->bigInteger('ms_wajibpajak_id'); // id wajib pajak
            $table->bigInteger('ms_user_id'); // id user
            $table->integer('ms_country_id')->nullable();
            $table->string('st_tunjangan_id')->nullable(); // relasi ke id st_tunjangan_karyawan eg. 1,2,3,4,6
            $table->string('st_potongan_id')->nullable(); // relasi ke id st_potongan_karyawan eg. 1,2,3,4,6
            $table->integer('st_penggajian_id')->default(0); // relasi ke id st_penggajian_karyawan eg. 1
            $table->integer('st_attendance_id')->default(0); // relasi ke id st_attendance eg. 1
            $table->string('st_leave_id')->nullable(); // relasi ke id st_leave eg. 1,2,3,4,6
            $table->integer('ms_bank_id')->default(0);
            $table->integer('ms_karyawandivisi_id')->nullable();
            $table->integer('ms_karyawanjabatan_id')->nullable();
            $table->string('karyawan_enid');
            $table->string('karyawan_nik');
            $table->string('karyawan_npwp')->nullable();
            $table->string('karyawan_name');
            $table->date('karyawan_birthdate')->nullable();
            $table->string('karyawan_birthplace')->nullable();
            $table->string('karyawan_phone')->nullable();
            $table->string('karyawan_email')->nullable();
            $table->string('karyawan_password')->nullable();
            $table->string('karyawan_forgot_password')->nullable();
            $table->enum('karyawan_citizenship', ['WNI','WNA']);
            $table->enum('karyawan_gender', ['L','P']);
            $table->string('karyawan_address')->nullable();
            $table->enum('karyawan_status', ['TETAP','KONTRAK','NONKARYAWAN','PERCOBAAN']);
            $table->enum('karyawan_calculation_method', ['GROSS','GROSS_UP','NETT','MIX']);
            $table->date('karyawan_contract_begin');
            $table->date('karyawan_contract_end')->nullable();
            $table->date('karyawan_probation_end')->nullable();
            $table->bigInteger('karyawan_manager_id')->default(0);
            $table->enum('karyawan_ismanager', [1,0])->default(0);
            $table->enum('karyawan_isuser', [1,0])->default(0);
            $table->bigInteger('karyawan_salary')->default(0);
            $table->enum('karyawan_end_type', ['RESIGN', 'LAINNYA'])->nullable();
            $table->string('karyawan_end_reason')->nullable();
            $table->string('karyawan_photo')->nullable();
            $table->string('karyawan_bankno');
            $table->enum('karyawan_isbpjskes', [1,0]);
            $table->date('karyawan_bpjskesdate');
            $table->string('karyawan_bpjskesno');
            $table->enum('karyawan_isbpjstk', [1,0]);
            $table->date('karyawan_bpjstkdate');
            $table->string('karyawan_bpjstkno');
            $table->string('karyawan_code_objekpajak');
            $table->bigInteger('karyawan_bpjskeslainnya')->default(0);
            $table->bigInteger('karyawan_bpjstklainnya')->default(0);
            $table->text('karyawan_additionalinfo')->nullable();
            $table->enum('karyawan_active', [1,0])->default(1);
            $table->enum('karyawan_ismultiple', [1,0])->default(0);
            $table->timestamp('karyawan_created_at')->useCurrent();
            $table->timestamp('karyawan_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_karyawan');
    }
}
