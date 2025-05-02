<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStLeave extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_leave', function (Blueprint $table) {
            $table->increments('leave_id');
            $table->integer('ms_wajibpajak_id');
            $table->foreign('ms_wajibpajak_id')->references('wajibpajak_id')->on('ms_wajib_pajak');
            $table->string('leave_description');
            $table->enum('leave_type', ['PAID', 'UNPAID']);
            $table->date('leave_active_start_date');
            $table->date('leave_active_end_date');
            $table->date('leave_grace_period')->nullable();
            $table->integer('leave_quota');
            $table->boolean('leave_status_active')->default(TRUE);
            $table->boolean('leave_repeat_status')->default(FALSE);
            $table->boolean('leave_probation_status')->default(FALSE);
            $table->boolean('leave_base_month')->default(FALSE);
            $table->boolean('leave_is_hide')->default(FALSE);
            $table->boolean('leave_is_all')->default(FALSE);
            $table->integer('leave_used_for')->default(0); // 0 => Karyawan Tertentu, 1 => Semua Karyawan, 2 => Hanya Perempuan, 3 => Hanya Laki-laki, 4 => Berdasarkan Divisi, 5 => Berdasarkan Jabatan
            $table->string('leave_division_jabatan')->nullable(); // for divisi or jabatan 
            $table->timestamp('leave_created_at')->useCurrent();
            $table->timestamp('leave_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_leave');
    }
}
