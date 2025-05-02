<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrLeaveKaryawanModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_leave_karyawan', function (Blueprint $table) {
            $table->bigIncrements('leavekaryawan_id');
            $table->integer('ms_karyawan_id');
            $table->foreign('ms_karyawan_id')->references('karyawan_id')->on('ms_karyawan');
            $table->integer('leavekaryawan_manager_id')->nullable();
            $table->foreign('leavekaryawan_manager_id')->references('karyawan_id')->on('ms_karyawan');
            $table->integer('leavekaryawan_approval_by')->nullable();
            $table->foreign('leavekaryawan_approval_by')->references('karyawan_id')->on('ms_karyawan');
            $table->integer('st_leave_id');
            $table->foreign('st_leave_id')->references('leave_id')->on('st_leave');
            $table->date('leavekaryawan_start_date');
            $table->date('leavekaryawan_end_date');
            $table->boolean('leavekaryawan_half_leave_status')->default(false);
            $table->date('leavekaryawan_request_date');
            $table->string('leavekaryawan_request_note');
            $table->enum('leavekaryawan_status', ['WAITING', 'APPROVED', 'REJECTED', 'CANCEL', 'WAITINGCANCEL'])->default('WAITING');
            $table->string('leavekaryawan_cancel_note')->nullable();
            $table->date('leavekaryawan_approval_date')->nullable();
            $table->string('leavekaryawan_approval_note')->nullable();
            $table->integer('leavekaryawan_quota_use')->nullable();
            $table->integer('leavekaryawan_another_quota_use')->nullable();
            $table->integer('leavekaryawan_another_id')->nullable();
            $table->integer('leavekaryawan_another_st_id')->nullable();
            $table->boolean('leavekaryawan_is_hide')->default(false);
            $table->timestamp('leavekaryawan_created_at')->useCurrent();
            $table->timestamp('leavekaryawan_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_leave_karyawan');
    }
}
