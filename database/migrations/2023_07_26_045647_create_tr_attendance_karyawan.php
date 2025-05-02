<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrAttendanceKaryawanModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_attendance_karyawan', function (Blueprint $table) {
            $table->bigIncrements('attendancekaryawan_id');
            $table->integer('ms_karyawan_id');
            $table->foreign('ms_karyawan_id')->references('karyawan_id')->on('ms_karyawan');
            $table->integer('st_attendance_id');
            $table->foreign('st_attendance_id')->references('attendance_id')->on('st_attendance');
            $table->timestamp('attendancekaryawan_check_in');
            $table->string('attendancekaryawan_check_in_photo')->nullable();
            $table->string('attendancekaryawan_check_in_note')->nullable();
            $table->string('attendancekaryawan_check_in_late')->nullable();
            $table->timestamp('attendancekaryawan_check_out')->nullable();
            $table->string('attendancekaryawan_check_out_photo')->nullable();
            $table->timestamp('attendancekaryawan_break_start')->nullable();
            $table->string('attendancekaryawan_break_start_photo')->nullable();
            $table->timestamp('attendancekaryawan_break_start_note')->nullable();
            $table->timestamp('attendancekaryawan_break_end')->nullable();
            $table->string('attendancekaryawan_break_end_photo')->nullable();
            $table->string('attendancekaryawan_break_end_late')->nullable();
            $table->string('attendancekaryawan_break_end_note')->nullable();
            $table->string('attendancekaryawan_check_in_location')->nullable();
            $table->string('attendancekaryawan_break_start_early')->nullable();
            $table->string('attendancekaryawan_break_start_late')->nullable();
            $table->string('attendancekaryawan_break_start_location')->nullable();
            $table->string('attendancekaryawan_break_end_location')->nullable();
            $table->string('attendancekaryawan_check_out_location')->nullable();
            $table->string('attendancekaryawan_check_out_note')->nullable();
            $table->string('attendancekaryawan_check_out_early')->nullable();
            $table->string('attendancekaryawan_check_in_late_note')->nullable();
            $table->integer('attendancekaryawan_manager_id')->nullable();
            $table->timestamp('attendancekaryawan_created_at')->useCurrent();
            $table->timestamp('attendancekaryawan_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_attendance_karyawan');
    }
}
