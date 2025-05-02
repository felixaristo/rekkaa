<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrHistoryImportModel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_history_import', function (Blueprint $table) {
            $table->increments('historyimport_id');
            $table->integer('ms_wajibpajak_id');
            $table->foreign('ms_wajibpajak_id')->references('wajibpajak_id')->on('ms_wajib_pajak');
            $table->integer('ms_karyawan_id');
            $table->foreign('ms_karyawan_id')->references('karyawan_id')->on('ms_karyawan');
            $table->integer('ms_user_id');
            $table->foreign('ms_user_id')->references('ms_user_id')->on('mr_userwajibpajak');
            $table->string('historyimport_type')->nullable();
            $table->timestamp('historyimport_date')->nullable();
            $table->string('historyimport_status')->nullable();
            $table->string('historyimport_detail_import')->nullable();
            $table->string('historyimport_file_name')->nullable();
            $table->string('historyimport_originfile_name')->nullable();
            $table->text('historyimport_content')->nullable();
            $table->timestamp('historyimport_created_at')->useCurrent();
            $table->timestamp('historyimport_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_history_import');
    }
}
