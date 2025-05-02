<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_setting', function (Blueprint $table) {
            $table->increments('setting_id');
            $table->string('setting_key');
            $table->string('setting_value');
            $table->string('setting_description')->nullable();
            $table->enum('setting_active', [1,0])->default(1);
            $table->timestamp('setting_created_at')->useCurrent();
            $table->timestamp('setting_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_setting');
    }
}
