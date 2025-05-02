<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsJenisFasilitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_jenis_fasilitas', function (Blueprint $table) {
            $table->increments('jenisfasilitas_id');
            $table->string('jenisfasilitas_description')->nullable();
            $table->string('jenisfasilitas_tax_type');
            $table->enum('jenisfasilitas_active', [1,0])->default(1);
            $table->timestamp('jenisfasilitas_created_at')->useCurrent();
            $table->timestamp('jenisfasilitas_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_jenis_fasilitas');
    }
}
