<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBpjsRateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_bpjs_rate', function (Blueprint $table) {
            $table->increments('bpjsrate_id');
            $table->string('bpjsrate_category');
            $table->string('bpjsrate_code');
            $table->string('bpjsrate_description')->nullable();
            $table->string('bpjsrate_label')->nullable();
            $table->string('bpjsrate_notes')->nullable();
            $table->float('bpjsrate_rate')->default(0);
            $table->enum('bpjsrate_taxable', [1,0])->default(1); // 1=objek kena pajak, 0=objek tidak kena pajak
            $table->enum('bpjsrate_active', [1,0])->default(1);
            $table->enum('bpjsrate_calculationtype', ['PENAMBAH', 'PENGURANG'])->default('PENAMBAH');
            $table->timestamp('bpjsrate_created_at')->useCurrent();
            $table->timestamp('bpjsrate_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_bpjs_rate');
    }
}
