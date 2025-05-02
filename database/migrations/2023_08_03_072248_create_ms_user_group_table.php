<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsUserGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_user_group', function (Blueprint $table) {
            $table->bigIncrements('usergroup_id');
            $table->string('usergroup_name');
            $table->string('usergroup_code');
            $table->bigInteger('ms_wajibpajak_id');
            
            $table->enum('usergroup_active', [1,0])->default(1);
            $table->timestamp('usergroup_created_at')->useCurrent();
            $table->timestamp('usergroup_updated_at')->nullable()->useCurrentOnUpdate();

            $table->foreign('ms_wajibpajak_id')->references('wajibpajak_id')->on('ms_wajib_pajak'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_user_group');
    }
}
