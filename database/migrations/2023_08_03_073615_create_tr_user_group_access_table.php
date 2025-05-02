<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrUserGroupAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_user_group_access', function (Blueprint $table) {
            $table->bigIncrements('usergroupaccess_id');
            $table->bigInteger('ms_usergroup_id');
            $table->string('usergroupaccess_permissions'); // 1, 2, 4, 5 ms_permission
            $table->enum('usergroupaccess_active', [1,0])->default(1);
            $table->timestamp('usergroupaccess_created_at')->useCurrent();
            $table->timestamp('usergroupaccess_updated_at')->nullable()->useCurrentOnUpdate();

            $table->foreign('ms_usergroup_id')->references('usergroup_id')->on('ms_user_group'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_user_group_access');
    }
}
