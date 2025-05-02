<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsAkunAruskasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_akun_aruskas', function (Blueprint $table) {
            $table->increments('akunaruskas_id');
            $table->integer('ms_akunkategoriaruskas_id');
            $table->string('akunaruskas_name');
            $table->enum('akunaruskas_faktor', ['+', '-']);
            $table->enum('akunaruskas_active', [1,0])->default(1);
            $table->timestamp('akunaruskas_created_at')->useCurrent();
            $table->timestamp('akunaruskas_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_akun_aruskas');
    }
}
