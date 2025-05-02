<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStPenggajianKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('st_penggajian_karyawan', function (Blueprint $table) {
            $table->increments('stpenggajiankaryawan_id');
            $table->string('stpenggajiankaryawan_name');
            $table->string('stpenggajiankaryawan_location');
            $table->string('stpenggajiankaryawan_pic');
            $table->text('stpenggajiankaryawan_logo');
            $table->text('stpenggajiankaryawan_logo_base64');
            $table->enum('stpenggajiankaryawan_period', ['KALENDER', 'TANGGAL']);
            $table->integer('stpenggajiankaryawan_startdate')->nullable(); // if period is TANGGAL 1-31
            $table->integer('stpenggajiankaryawan_enddate')->nullable(); // if period is TANGGAL 1-31
            $table->integer('stpenggajiankaryawan_paymentdate'); // 1-31
            $table->enum('stpenggajiankaryawan_method', ['KALENDER', 'KERJA', 'TETAP']);
            $table->enum('stpenggajiankaryawan_weekendoption', ['MAJU', 'MUNDUR']);
            $table->integer('stpenggajiankaryawan_day')->nullable(); // if method is TETAP
            $table->enum('stpenggajiankaryawan_autoemailpayslip', [1,0])->default(0);
            $table->enum('stpenggajiankaryawan_active', [1,0])->default(1);
            $table->timestamp('stpenggajiankaryawan_calculationtime')->nullable();
            $table->integer('ms_wajibpajak_id'); // id wajib pajak
            $table->boolean('stpenggajiankaryawan_is_all')->default(FALSE);
            $table->boolean('stpenggajiankaryawan_isnonemployee')->default(FALSE);
            $table->timestamp('stpenggajiankaryawan_created_at')->useCurrent();
            $table->timestamp('stpenggajiankaryawan_updated_at')->nullable()->useCurrentOnUpdate();

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
        Schema::dropIfExists('st_penggajian_karyawan');
    }
}
