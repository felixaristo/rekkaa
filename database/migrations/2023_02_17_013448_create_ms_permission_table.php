<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_permission', function (Blueprint $table) {
            $table->bigIncrements('permission_id');
            $table->string('permission_parent');
            $table->string('permission_group');
            $table->string('permission_group_name');
            $table->string('permission_code');
            $table->string('permission_code_name');
            $table->string('permission_value');
            $table->string('permission_text')->nullable();
            $table->integer('permission_order');
            $table->integer('permission_price');
            $table->enum('permission_entity_type', ['BADAN','INDIVIDU','SEMUA']);
            $table->enum('permission_visibility', ['PRICING_PAGE','GROUP_ACCESS', 'NONE'])->default('NONE');
            $table->string('permission_default_group')->nullable(); // MANAJER,KARYAWAN
            $table->enum('permission_period', ['HARI','MINGGU','BULAN','PERIODE']);
            $table->enum('permission_confidential', [1,0])->default(0);
            $table->enum('permission_active', [1,0])->default(1);
            $table->integer('ms_menu_id');
            $table->timestamp('permission_created_at')->useCurrent();
            $table->timestamp('permission_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_permission');
    }
}
