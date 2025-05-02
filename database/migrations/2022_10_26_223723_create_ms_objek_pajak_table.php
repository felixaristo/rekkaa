<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsObjekPajakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_objek_pajak', function (Blueprint $table) {
            $table->increments('objekpajak_id');
            $table->string('objekpajak_code')->unique();
            $table->text('objekpajak_description');
            $table->string('objekpajak_type')->default('PPH4-2');
            $table->float('objekpajak_rate');
            $table->string('objekpajak_category')->nullable();
            $table->enum('objekpajak_calculator', [1, 0])->default(0);
            $table->enum('objekpajak_bukanpegawai', [1, 0])->default(0);
            $table->enum('objekpajak_active', [1,0])->default(1);
            // $table->timestamp('objekpajak_created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            // $table->timestamp('objekpajak_updated_at')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            $table->timestamp('objekpajak_created_at')->useCurrent();
            $table->timestamp('objekpajak_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_objek_pajak');
    }
}
