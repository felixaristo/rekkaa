<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStLeaveDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_leave_detail', function (Blueprint $table) {
            $table->increments('leavedetail_id');
            $table->integer('st_leave_id');
            $table->foreign('st_leave_id')->references('leave_id')->on('st_leave');
            $table->integer('ms_karyawan_id');
            $table->foreign('ms_karyawan_id')->references('karyawan_id')->on('ms_karyawan');
            $table->boolean('leavedetail_active')->default(TRUE);
            $table->timestamp('leavedetail_created_at')->useCurrent();
            $table->timestamp('leavedetail_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_leave_detail');
    }
}
