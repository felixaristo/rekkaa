<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStAnnouncement extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_announcement', function (Blueprint $table) {
            $table->increments('announcement_id');
            $table->integer('ms_wajibpajak_id');
            $table->foreign('ms_wajibpajak_id')->references('wajibpajak_id')->on('ms_wajib_pajak');
            $table->string('announcement_title');
            $table->text('announcement_description');
            $table->text('announcement_karyawan')->default('[]')->nullable();
            $table->text('announcement_karyawan_is_read')->default('[]')->nullable();
            $table->text('announcement_karyawan_is_clear')->default('[]')->nullable();
            $table->date('announcement_start_date');
            $table->date('announcement_end_date');
            $table->boolean('announcement_is_all')->default(TRUE);
            $table->boolean('announcement_active')->default(TRUE);
            $table->timestamp('announcement_created_at')->useCurrent();
            $table->timestamp('announcement_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_announcement');
    }
}
