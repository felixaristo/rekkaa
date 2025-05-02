<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsTarif21Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_tarif21', function (Blueprint $table) {
            $table->increments('tarif21_id');
            $table->year('tarif21_year');
            $table->string('tarif21_description');
            $table->float('tarif21_rate')->default(0);
            $table->bigInteger('tarif21_startincome')->default(0);
            $table->bigInteger('tarif21_endincome')->default(0);
            $table->enum('tarif21_active', [1,0])->default(1);
            $table->timestamp('tarif21_created_at')->useCurrent();
            $table->timestamp('tarif21_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_tarif21');
    }
}
