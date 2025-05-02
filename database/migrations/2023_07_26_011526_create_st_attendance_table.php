<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_attendance', function (Blueprint $table) {
            $table->increments('attendance_id');
            $table->integer('ms_wajibpajak_id');
            $table->foreign('ms_wajibpajak_id')->references('wajibpajak_id')->on('ms_wajib_pajak');
            $table->string('attendance_description');
            $table->string('attendance_working_day')->nullable();
            $table->time('attendance_check_in')->nullable();
            $table->time('attendance_check_in_tolerance')->nullable();
            $table->time('attendance_check_out')->nullable();
            $table->time('attendance_start_break')->nullable();
            $table->time('attendance_end_break')->nullable();
            $table->boolean('attendance_break_status')->default(FALSE);
            $table->enum('attendance_break_type', ['SPECIFIC', 'DURATION']);
            $table->boolean('attendance_check_out_status_photo')->default(FALSE);
            $table->boolean('attendance_check_in_status_photo')->default(FALSE);
            $table->boolean('attendance_break_status_photo')->default(FALSE);
            $table->boolean('attendance_location_status')->default(FALSE);
            $table->boolean('attendance_status_active')->default(TRUE);
            $table->boolean('attendance_is_all')->default(FALSE);
            $table->integer('attendance_is_used')->default(0);
            
            $table->string('attendance_location_address')->nullable();
            $table->string('attendance_location_longitude')->nullable();
            $table->string('attendance_location_latitude')->nullable();
            $table->timestamp('attendance_created_at')->useCurrent();
            $table->timestamp('attendance_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_attendance');
    }
}
