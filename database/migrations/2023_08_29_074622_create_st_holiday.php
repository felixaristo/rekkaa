<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStHoliday extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_holiday', function (Blueprint $table) {
            $table->increments('holiday_id');
            $table->integer('ms_wajibpajak_id');
            $table->foreign('ms_wajibpajak_id')->references('wajibpajak_id')->on('ms_wajib_pajak');
            $table->string('holiday_description');
            $table->date('holiday_start_date');
            $table->date('holiday_end_date');
            $table->string('holiday_remark')->nullable();
            $table->boolean('holiday_is_cut_leave')->default(false);
            $table->boolean('holiday_status_active')->default(true);
            $table->timestamp('holiday_created_at')->useCurrent();
            $table->timestamp('holiday_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('st_holiday');
    }
}
