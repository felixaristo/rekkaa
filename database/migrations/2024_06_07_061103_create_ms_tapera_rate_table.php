<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsTaperaRateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_tapera_rate', function (Blueprint $table) {
            $table->increments('taperarate_id');
            $table->string('taperarate_category');
            $table->string('taperarate_code');
            $table->string('taperarate_description')->nullable();
            $table->float('taperarate_rate')->default(0);
            // $table->enum('taperarate_taxable', [1,0])->default(1); // 1=objek kena pajak, 0=objek tidak kena pajak
            $table->enum('taperarate_active', [1,0])->default(1);
            $table->enum('taperarate_calculationtype', ['PENAMBAH', 'PENGURANG'])->default('PENGURANG');
            $table->timestamp('taperarate_created_at')->useCurrent();
            $table->timestamp('taperarate_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_tapera_rate');
    }
}
