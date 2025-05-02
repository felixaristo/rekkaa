<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrPph21Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tr_pph21', function (Blueprint $table) {
            $table->increments('pph21_id');
            // $table->bigInteger('pph21_parent_id')->default(0);
            // $table->text('tr_payroll_uuid')->unique();
            $table->text('tr_payroll_uuid');
            $table->bigInteger('ms_user_id');
            $table->bigInteger('ms_wajibpajak_id');
            $table->bigInteger('ms_karyawan_id');
            $table->integer('ms_ptkp_id')->default(0);
            $table->string('pph21_ptkp_description')->nullable();
            $table->string('pph21_ptkp_rate')->nullable();
            $table->string('pph21_ptkp_category')->nullable();
            $table->integer('ms_ptkpdet_id')->default(0);
            $table->float('pph21_ptkpdet_rate_percentage')->nullable();
            $table->bigFloat('pph21_ptkpdet_rate_nominal')->nullable();
            $table->string('pph21_objekpajak_code')->nullable();
            $table->string('pph21_objekpajak_description')->nullable();
            $table->string('pph21_tarif21_rate_label')->nullable();
            $table->float('pph21_objekpajak_rate')->nullable();
            $table->float('pph21_tarif21_rate')->nullable();
            $table->enum('pph21_method', ['GROSS','GROSS_UP','NETT','MIXED']);
            $table->date('pph21_period');
            $table->bigFloat('pph21_basic_salary')->default(0);
            $table->bigFloat('pph21_prorate_salary')->default(0);
            $table->bigFloat('pph21_deduction_position')->default(0);

            $table->bigFloat('pph21_deduction_other')->default(0);
            $table->bigFloat('pph21_deduction_jamkes')->default(0);
            $table->bigFloat('pph21_deduction_jamkes_payslip')->default(0);
            $table->bigFloat('pph21_deduction_jht')->default(0);
            $table->bigFloat('pph21_deduction_jht_payslip')->default(0);
            $table->bigFloat('pph21_deduction_jp')->default(0);
            $table->bigFloat('pph21_deduction_tapera_kj')->default(0);
            $table->bigFloat('pph21_deduction_tapera_pkj')->default(0);

            $table->float('pph21_deduction_jamkes_rate')->default(0);
            $table->float('pph21_deduction_jamkes_payslip_rate')->default(0);
            $table->float('pph21_deduction_jht_rate')->default(0);
            $table->float('pph21_deduction_jp_rate')->default(0);
            $table->float('pph21_deduction_tapera_kj_rate')->default(0);
            $table->float('pph21_deduction_tapera_pkj_rate')->default(0);

            $table->bigFloat('pph21_allowance_other')->default(0);
            $table->bigFloat('pph21_allowance_jamkes')->default(0);
            $table->bigFloat('pph21_allowance_jkk')->default(0);
            $table->bigFloat('pph21_allowance_jkm')->default(0);
            $table->bigFloat('pph21_allowance_jht')->default(0);
            $table->bigFloat('pph21_allowance_jht_payslip')->default(0);
            $table->bigFloat('pph21_allowance_jp')->default(0);
            $table->bigFloat('pph21_allowance_jp_payslip')->default(0);
            $table->bigFloat('pph21_allowance_overtime')->default(0);
            
            $table->float('pph21_allowance_jamkes_rate')->default(0);
            $table->float('pph21_allowance_jkk_rate')->default(0);
            $table->float('pph21_allowance_jkm_rate')->default(0);
            $table->float('pph21_allowance_jht_rate')->default(0);
            $table->float('pph21_allowance_jht_payslip_rate')->default(0);
            $table->float('pph21_allowance_jp_rate')->default(0);
            $table->float('pph21_allowance_jp_payslip_rate')->default(0);
            
            $table->bigFloat('pph21_pkp')->default(0);
            $table->bigFloat('pph21_ptkp')->default(0);
            $table->bigFloat('pph21_dpp')->default(0);
            $table->bigFloat('pph21_dpp_kumulatif')->default(0);
            $table->bigFloat('pph21_bruto_month')->default(0);
            $table->bigFloat('pph21_bruto_year')->default(0);
            $table->bigFloat('pph21_netto_month')->default(0);
            $table->bigFloat('pph21_netto_year')->default(0);
            // for previous company
            $table->bigFloat('pph21_prevcp_pph21_total')->default(0);
            $table->bigFloat('pph21_prevcp_netto')->default(0);

            $table->bigFloat('pph21_total_month')->default(0);
            $table->bigFloat('pph21_total_year')->default(0);

            $table->text('pph21_ptkp_data')->nullable()->comment("json object from ms_ptkp");
            $table->text('pph21_ptkpdet_data')->nullable()->comment("json object from ms_ptkp_detail");
            $table->text('pph21_tarif21_data')->nullable()->comment("json object from ms_tarif21");
            // $table->text('pph21_objekpajak_data')->nullable()->comment("json object in objek pajak data");
            $table->text('pph21_pajakpph21_data')->nullable()->comment("json object from st_pajak_pph21");
            
            $table->timestamp('pph21_trx_at')->nullable();
            $table->integer('pph21_nobuktipotong')->default(0);
            $table->integer('pph21_pembetulan');
            $table->enum('pph21_active', [1,0])->default(1);
            $table->timestamp('pph21_created_at')->useCurrent();
            $table->timestamp('pph21_updated_at')->nullable()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tr_pph21');
    }
}
