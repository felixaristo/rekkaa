<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDashboardClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_dashboard_client', function (Blueprint $table) {
            $table->increments('dashboradclient_id');
            $table->string('dashboardclient_month');
            $table->string('dashboardclient_year');
            $table->integer('dashboardclient_total_client')->nullable();
            $table->integer('dashboardclient_total_retainer')->nullable();
            $table->integer('dashboardclient_total_onetime')->nullable();
            $table->integer('dashboardclient_total_register')->nullable();
            $table->integer('dashboardclient_total_not_register')->nullable();
            $table->integer('dashboardclient_total_not_extend')->nullable();
            $table->integer('dashboardclient_total_not_complete')->nullable();
            $table->text('dashboardclient_rasio_klu')->nullable();
            $table->text('dashboardclient_rasio_user')->nullable();
            $table->integer('dashboardclient_total_client_new')->nullable();
            $table->integer('dashboardclient_total_retainer_new')->nullable();
            $table->integer('dashboardclient_total_onetime_new')->nullable();
            $table->integer('dashboardclient_total_register_new')->nullable();
            $table->integer('dashboardclient_total_not_register_new')->nullable();
            $table->integer('dashboardclient_total_not_extend_new')->nullable();
            $table->integer('dashboardclient_total_not_complete_new')->nullable();
            $table->text('dashboardclient_rasio_klu_new')->nullable();
            $table->text('dashboardclient_rasio_user_new')->nullable();
            $table->timestamp('dashboradclient_created_at')->useCurrent();
            $table->timestamp('dashboradclient_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_dashboard_client');
    }
}
