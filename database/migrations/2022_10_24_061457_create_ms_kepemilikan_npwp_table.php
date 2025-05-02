<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsKepemilikanNpwpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_kepemilikan_npwp', function (Blueprint $table) {
            $table->increments('kepemilikannpwp_id');
            $table->string('kepemilikannpwp_code');
            $table->string('kepemilikannpwp_name');
            $table->integer('kepemilikannpwp_value');
            $table->enum('kepemilikannpwp_active', [1,0])->default(1);
            $table->timestamp('kepemilikannpwp_created_at')->useCurrent();
            $table->timestamp('kepemilikannpwp_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_kepemilikan_npwp');
    }
}
