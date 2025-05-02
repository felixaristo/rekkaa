<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsBankTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_bank', function (Blueprint $table) {
            $table->increments('bank_id');
            $table->string('bank_code');
            $table->string('bank_name');
            $table->enum('bank_active', [1,0])->default(1);
            $table->timestamp('bank_created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_bank');
    }
}
