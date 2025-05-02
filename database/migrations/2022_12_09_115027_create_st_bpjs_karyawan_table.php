<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStBpjsKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()

    {
        Schema::create('st_bpjs_karyawan', function (Blueprint $table) {
            $table->increments('stbpjskaryawan_id');
            $table->text('stbpjskaryawan_value');
            $table->enum('stbpjskaryawan_active', [1,0])->default(1);
            $table->integer('ms_wajibpajak_id'); // id wajib pajak
            $table->timestamp('stbpjskaryawan_created_at')->useCurrent();
            $table->timestamp('stbpjskaryawan_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_bpjs_karyawan');
    }
}
