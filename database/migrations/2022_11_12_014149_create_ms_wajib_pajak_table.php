<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsWajibPajakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_wajib_pajak', function (Blueprint $table) {
            $table->bigIncrements('wajibpajak_id');
            $table->string('wajibpajak_name');
            $table->enum('wajibpajak_type', ['BADAN', 'INDIVIDU']);
            $table->string('wajibpajak_npwp');
            $table->string('wajibpajak_nik');
            $table->string('ms_klu_id')->nullable();
            $table->string('wajibpajak_address')->nullable();
            $table->string('wajibpajak_postal_code')->nullable();
            $table->string('wajibpajak_email')->nullable();
            $table->string('wajibpajak_phone');
            $table->integer('ms_regency_id')->nullable();
            $table->integer('ms_country_id')->nullable();
            $table->string('wajibpajak_intercity')->nullable();
            $table->integer('ms_user_id'); // pemilik
            $table->enum('wajibpajak_active', [1,0])->default(0);
            $table->timestamp('wajibpajak_created_at')->useCurrent();
            $table->timestamp('wajibpajak_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_wajib_pajak');
    }
}
