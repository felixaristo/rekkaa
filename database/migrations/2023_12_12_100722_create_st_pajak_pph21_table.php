<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStPajakPph21Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_pajak_pph21', function (Blueprint $table) {
            $table->increments('stpajakpph21_id');
            $table->string('stpajakpph21_name');
            $table->string('stpajakpph21_companyname');
            $table->string('stpajakpph21_npwp');
            $table->string('stpajakpph21_posisi');
            $table->string('stpajakpph21_lokasi');
            $table->text('stpajakpph21_ttd');
            $table->text('stpajakpph21_ttd_base64');
            $table->integer('ms_wajibpajak_id'); // id wajib pajak
            $table->enum('stpajakpph21_active', [1,0])->default(1);
            $table->timestamp('stpajakpph21_created_at')->useCurrent();
            $table->timestamp('stpajakpph21_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_pajak_pph21');
    }
}
