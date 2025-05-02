<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsAkunKategoriaruskasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_akun_kategoriaruskas', function (Blueprint $table) {
            $table->increments('akunkategoriaruskas_id');
            $table->string('akunkategoriaruskas_name');
            $table->enum('akunkategoriaruskas_active', [1,0])->default(1);
            $table->timestamp('akunkategoriaruskas_created_at')->useCurrent();
            $table->timestamp('akunkategoriaruskas_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_akun_kategoriaruskas');
    }
}
