<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrPayrollTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_payroll', function (Blueprint $table) {
            $table->increments('payroll_id');
            $table->text('payroll_uuid')->unique();
            $table->bigInteger('ms_user_id');
            $table->bigInteger('ms_wajibpajak_id');
            $table->bigInteger('ms_karyawan_id');
            $table->date('payroll_period');
            $table->bigInteger('payroll_total_netto')->default(0)->comment("payroll_total_income - payroll_total_outcome");
            $table->bigInteger('payroll_total_income')->default(0);
            $table->bigInteger('payroll_total_outcome')->default(0);
            $table->bigInteger('payroll_basic_salary')->default(0);
            $table->bigInteger('payroll_prorate_salary')->default(0);
            $table->bigInteger('payroll_allowance_pph21')->default(0);
            $table->bigInteger('payroll_allowance_bpjskes')->default(0);
            $table->bigInteger('payroll_allowance_bpjstk')->default(0);
            $table->bigInteger('payroll_deduction_pph21')->default(0);
            $table->bigInteger('payroll_allowance_overtime')->default(0);
            $table->bigInteger('payroll_deduction_bpjskes')->default(0);
            $table->bigInteger('payroll_deduction_bpjstk')->default(0);
            $table->bigInteger('payroll_total_stbpjskes')->default(0)->comment("accumulation from st_bpjs");
            $table->bigInteger('payroll_total_stbpjstk')->default(0)->comment("accumulation from st_bpjs");
            // $table->bigInteger('payroll_pph21')->default(0)->comment("monthly pph21");
            
            $table->string('payroll_karyawan_enid');
            $table->string('payroll_karyawan_name');
            $table->string('payroll_karyawan_nik');
            $table->string('payroll_karyawan_npwp');
            $table->string('payroll_karyawan_gender');
            $table->string('payroll_karyawan_status');
            $table->string('payroll_karyawan_address');
            $table->string('ms_divisi_id')->nullable();
            $table->string('payroll_karyawandivisi_name')->nullable();
            $table->string('ms_jabatan_id')->nullable();
            $table->string('payroll_karyawanjabatan_name')->nullable();
            $table->enum('payroll_paid_option', ['MAJU','MUNDUR'])->default('MUNDUR'); // relate to st_penggajian_karyawan stpenggajiankaryawan_weekendoption
            $table->enum('payroll_autoemailslip', [1,0])->default(0); // relate to st_penggajian_karyawan stpenggajiankaryawan_autoemailpayslip
            $table->enum('payroll_method', ['GROSS','GROSS_UP','NETT','MIXED']);
            $table->enum('payroll_bpjskes_paidbycompany', [1,0])->default(0)->comment("is bpjs kesehatan all paid by company");
            $table->enum('payroll_bpjstk_paidbycompany', [1,0])->default(0)->comment("is bpjs kesehatan all paid by company");
            $table->enum('payroll_status', [0,1,2,3])->default(0)->comment("0=Menunggu Perhitungan, 1=Menunggu Konfirmasi, 2=Menunggu Pembayaran, 3=Dibayarkan");
            $table->enum('payroll_lock', [1,0])->default(0);
            $table->timestamp('payroll_trx_at')->nullable();
            $table->timestamp('payroll_lock_at')->nullable();
            $table->timestamp('payroll_paid_at')->nullable();
            $table->enum('payroll_active', [1,0])->default(1);
            
            $table->text('payroll_allowance_setting')->nullable()->comment("json object from st_tunjangan");
            $table->text('payroll_allowance_addition')->nullable()->comment("json object added manualy in payroll detail");
            $table->text('payroll_deduction_setting')->nullable()->comment("json object from st_potongan");
            $table->text('payroll_deduction_addition')->nullable()->comment("json object added manualy");
            $table->text('payroll_bpjssetting')->nullable()->comment("json object from st_bpjs_karyawan");
            $table->text('payroll_taperasetting')->nullable()->comment("json object from st_tapera_karyawan");
            
            $table->text('payroll_attendancesetting')->nullable()->comment("json object in st_attendance");
            $table->text('payroll_penggajiansetting')->nullable()->comment("json object in st_penggajian");

            $table->text('payroll_prorate_data')->nullable()->comment("json object in prorate data");
            
            $table->timestamp('payroll_created_at')->useCurrent();
            $table->timestamp('payroll_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_payroll');
    }
}
