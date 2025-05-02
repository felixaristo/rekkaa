<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrUserGroupMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_user_group_member', function (Blueprint $table) {
            $table->bigIncrements('usergroupmember_id');
            $table->bigInteger('ms_usergroup_id');
            $table->string('usergroupmember_members'); // 1, 2, 4, 5 relation to ms_user
            $table->enum('usergroupmember_active', [1,0])->default(1);
            $table->timestamp('usergroupmember_created_at')->useCurrent();
            $table->timestamp('usergroupmember_updated_at')->nullable()->useCurrentOnUpdate();

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
        Schema::dropIfExists('tr_user_group_member');
    }
}
