<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTarifNonnpwpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_tarif_nonnpwp', function (Blueprint $table) {
            $table->increments('tarifnonnpwp_id');
            $table->string('tarifnonnpwp_taxtype');
            $table->float('tarifnonnpwp_rate')->default(0);
            $table->enum('tarifnonnpwp_active', [1,0])->default(1);
            $table->timestamp('tarifnonnpwp_created_at')->useCurrent();
            $table->timestamp('tarifnonnpwp_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_tarif_nonnpwp');
    }
}
