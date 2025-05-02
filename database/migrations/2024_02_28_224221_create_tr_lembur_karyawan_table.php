<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrLemburKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_lembur_karyawan', function (Blueprint $table) {
            $table->bigIncrements('lemburkaryawan_id');
            $table->integer('ms_karyawan_id');
            $table->foreign('ms_karyawan_id')->references('karyawan_id')->on('ms_karyawan');
            $table->integer('ms_wajibpajak_id');
            $table->foreign('ms_wajibpajak_id')->references('wajibpajak_id')->on('ms_wajib_pajak');
            $table->string('tr_payroll_uuid')->nullable();
            $table->foreign('tr_payroll_uuid')->references('payroll_uuid')->on('tr_payroll');
            $table->integer('lemburkaryawan_manager_id')->nullable();
            $table->integer('lemburkaryawan_approval_by')->nullable();
            $table->integer('lemburkaryawan_cancel_by')->nullable();
            $table->integer('lemburkaryawan_canceladmin_by')->nullable();
            $table->timestamp('lemburkaryawan_start_time');
            $table->timestamp('lemburkaryawan_end_time');
            $table->integer('lemburkaryawan_total_time')->default(0); // in minutes
            $table->integer('lemburkaryawan_prorate_days')->default(0); // in days
            $table->integer('lemburkaryawan_working_days')->default(0); // in days
            $table->integer('lemburkaryawan_baseovertime')->default(0); // in rupiahs
            $table->integer('lemburkaryawan_salary')->default(0); // in rupiahs
            $table->bigInteger('lemburkaryawan_overtime_amount')->default(0);
            $table->bigInteger('lemburkaryawan_total_overtime_amount')->default(0);
            $table->string('lemburkaryawan_request_note');
            $table->enum('lemburkaryawan_status', ['WAITING', 'APPROVEDMANAGER','APPROVED', 'REJECTED', 'CANCEL', 'WAITINGCANCEL'])->default('WAITING');
            $table->timestamp('lemburkaryawan_cancel_date')->nullable();
            $table->string('lemburkaryawan_cancel_note')->nullable();
            $table->timestamp('lemburkaryawan_approval_date')->nullable();
            $table->string('lemburkaryawan_approval_note')->nullable();
            $table->integer('lemburkaryawan_user_id')->nullable();
            $table->foreign('lemburkaryawan_user_id')->references('user_id')->on('ms_user');
            $table->integer('lemburkaryawan_approval_adminby')->nullable();
            $table->foreign('lemburkaryawan_approval_adminby')->references('user_id')->on('ms_user');
            $table->timestamp('lemburkaryawan_canceladmin_date')->nullable();
            $table->string('lemburkaryawan_canceladmin_note')->nullable();
            $table->timestamp('lemburkaryawan_approvaladmin_date')->nullable();
            $table->string('lemburkaryawan_approvaladmin_note')->nullable();
            $table->text('lemburkaryawan_stlemburkaryawan_data');
            $table->text('lemburkaryawan_sttunjangankaryawan_data')->nullable();
            $table->text('lemburkaryawan_stpenggajiankaryawan_data');
            $table->text('lemburkaryawan_stattendancekaryawan_data');
            $table->timestamp('lemburkaryawan_created_at')->useCurrent();
            $table->timestamp('lemburkaryawan_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_lembur_karyawan');
    }
}
