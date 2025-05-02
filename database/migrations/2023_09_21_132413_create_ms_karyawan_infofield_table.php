<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsKaryawanInfoFieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_karyawan_infofield', function (Blueprint $table) {
            $table->bigIncrements('karyawaninfofield_id');
            $table->bigInteger('ms_wajibpajak_id'); // id wajib pajak
            $table->bigInteger('ms_user_id'); // id user
            $table->text('karyawaninfofield_fields')->nullable();
            $table->timestamp('karyawaninfofield_created_at')->useCurrent();
            $table->timestamp('karyawaninfofield_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_karyawan_infofield');
    }
}
