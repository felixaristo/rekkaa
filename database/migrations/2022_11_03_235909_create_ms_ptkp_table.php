<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsPtkpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_ptkp', function (Blueprint $table) {
            $table->increments('ptkp_id');
            $table->string('ptkp_marriage_status');
            $table->string('ptkp_description')->nullable();
            $table->string('ptkp_code');
            $table->float('ptkp_rate')->default(0);
            $table->float('ptkp_rate_month')->default(0);
            $table->integer('ptkp_year')->default(0);
            $table->float('ptkp_rate_percentage')->default(0);
            $table->enum('ptkp_category', ['A','B','C'])->default('A');
            $table->enum('ptkp_active', [1,0])->default(1);
            $table->timestamp('ptkp_created_at')->useCurrent();
            $table->timestamp('ptkp_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_ptkp');
    }
}
