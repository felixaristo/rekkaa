<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsTunjanganJabatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_tunjangan_jabatan', function (Blueprint $table) {
            $table->increments('tunjanganjabatan_id');
            $table->year('tunjanganjabatan_year');
            $table->float('tunjanganjabatan_rate')->default(0);
            $table->bigInteger('tunjanganjabatan_maximum_allowance')->default(0);
            $table->enum('tunjanganjabatan_active', [1,0])->default(1);
            $table->timestamp('tunjanganjabatan_created_at')->useCurrent();
            $table->timestamp('tunjanganjabatan_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_tunjangan_jabatan');
    }
}
