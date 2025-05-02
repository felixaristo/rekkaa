<?php
namespace App\Http\Controllers\Api\App\Operational;

use App\Http\Controllers\Controller;
use App\Libraries\AppPayslipLibrary;
use App\Libraries\AppPPh21Library;
use App\Model\Master\BpjsRateModel;
use App\Model\Master\PtkpDetailModel;
use App\Model\Master\Tarif21Model;
use App\Model\Master\TarifNonNpwpModel;
use App\Model\Master\TunjanganJabatanModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingHolidayModel;
use App\Model\Setting\SettingModel;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use App\Model\Transaction\PayrollModel;
use App\Model\Transaction\PPh21Model;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;
use Spatie\Activitylog\Models\Activity;

class CalculatePayslipController extends Controller
{   
    public function select_wajibpajak(Request $request)
    {
        $q = $request->get('q');
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $data = WajibPajakModel::
        when($q, function ($query, $q) {
            return $query->where('wajibpajak_name', 'ilike', '%'.$q.'%');
        })
        ->
        where([
            'wajibpajak_active' => 1,
        ])->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    public function select_stpenggajian(Request $request)
    {
        $q = $request->get('q');
        $wajibpajak_id = $request->get('wajibpajak_id');
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $data = SettingPenggajianKaryawanModel::
        // select('stpenggajiankaryawan_id', 'stpenggajiankaryawan_name')
        with(['wajibpajak'])
        ->when($q, function ($query, $q) {
            return $query->where('stpenggajiankaryawan_name', 'ilike', '%'.$q.'%');
        })
        ->where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'stpenggajiankaryawan_active' => 1,
        ])->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    public function calculate(Request $request)
    {
        // dd('ooo');
        $rules = [
            'wajibpajak_id' => 'required',
            'stpenggajian_id' => 'required',
        ];
        if($request->get('payroll_date_period')) { // cronjob
            $rules['payroll_date_period'] = 'required';
        } else {
            $rules['periode'] = 'required';
        }
        $validator = Validator::make($request->all(), $rules, [
            'wajibpajak_id.required' => 'ID Wajib Pajak wajib diisi!',
            'stpenggajian_id.required' => 'ID Pengaturan Penggajian wajib diisi!',
            'periode.required' => 'Periode Penggajian wajib diisi!',
            'payroll_date_period.required' => 'Periode Date Penggajian wajib diisi!',
        ]);

        if ($validator->fails()) {
        //   $response['response'] = $validator->messages();
            return response()->json([
                'success' => false,
                'message' => 'Field wajib diisi.',
                'data' => $validator->errors(),
            ]);
        }
        
        $wajibpajak_id = $request->get('wajibpajak_id');
        $stpenggajian_id = $request->get('stpenggajian_id');

        if($request->get('payroll_date_period')) { // cronjob
            $payroll_date_period = $request->get('payroll_date_period');
            $payroll_period = Carbon::createFromFormat('Y-m-d', $payroll_date_period)->format('Y-m');
            $periode_year = Carbon::createFromFormat('Y-m-d', $payroll_date_period)->format('Y');
            $periode_month = Carbon::createFromFormat('Y-m-d', $payroll_date_period)->format('n');
            if($request->get('periodtype') == 'TANGGAL') {
                $payroll_date = date('d', strtotime("-1 day", strtotime($payroll_date_period)));
            } else {
                $payroll_date = $payroll_date_period;
            }
        } else {
            $payroll_period = Carbon::createFromFormat('!m-Y', $request->get('periode'))->format('Y-m');
            $periode_year = Carbon::createFromFormat('!m-Y', $request->get('periode'))->format('Y');
            $periode_month = Carbon::createFromFormat('!m-Y', $request->get('periode'))->format('n');
            $payroll_period =date('Y-m', strtotime($payroll_period));
            
            // $payroll_date = $payroll_period.'-'.date('d');
            $payroll_date = $payroll_period.'-01';
        }

        // BEGIN GET MAX QUOTA FROM SUBSCRIPTION
        $currentstpenggajian = SettingPenggajianKaryawanModel::where([
            'stpenggajiankaryawan_active' => 1,
            'stpenggajiankaryawan_id' => $stpenggajian_id
        ])->first();
        $decodepermission = json_decode($currentstpenggajian->wajibpajak->wajibpajaksubscription->wajibpajaksubscription_permission);
        $maxquota = 0;
        foreach($decodepermission as $permit) {
            if($permit->menu_id == 3) {
                $maxquota = $permit->Q;
                break;
            }
        }
        // END GET MAX QUOTA FROM SUBSCRIPTION

        $stpenggajian_karyawan = SettingPenggajianKaryawanModel::where([
            // 'stpenggajiankaryawan_id' => 20,
            'stpenggajiankaryawan_active' => 1,
            'stpenggajiankaryawan_id' => $stpenggajian_id
            // 'stpenggajiankaryawan_period' => 'KALENDER'
        ])
        ->with(['karyawan' => function($q) use($stpenggajian_id, $payroll_period, $payroll_date, $maxquota) {
            $q->select('st_penggajian_id', 'ms_karyawan.*'
            , DB::raw("( SELECT row_to_json(ptkptable.*) AS row_to_json
            FROM ( SELECT mpp.ptkp_id,
                     mpp.ptkp_marriage_status,
                     mpp.ptkp_description,
                     mpp.ptkp_rate,
                     mpp.ptkp_active,
                     mpp.ptkp_created_at,
                     mpp.ptkp_updated_at
                    FROM ms_ptkp mpp
                   WHERE mpp.ptkp_active::text = '1'::text AND mpp.ptkp_id = ms_karyawan.ms_ptkp_id
                  LIMIT 1) ptkptable) AS ptkptbl")
            , DB::raw("( SELECT row_to_json(statttable.*) AS row_to_json
            FROM ( SELECT sa.attendance_id,
                     sa.ms_wajibpajak_id,
                     sa.attendance_description,
                     sa.attendance_working_day,
                     sa.attendance_check_in,
                     sa.attendance_check_in_tolerance,
                     sa.attendance_check_out,
                     sa.attendance_start_break,
                     sa.attendance_end_break,
                     sa.attendance_break_status,
                     sa.attendance_break_type,
                     sa.attendance_check_out_status_photo,
                     sa.attendance_check_in_status_photo,
                     sa.attendance_location_status,
                     sa.attendance_location_address,
                     sa.attendance_location_longitude,
                     sa.attendance_location_latitude,
                     sa.attendance_created_at,
                     sa.attendance_updated_at,
                     sa.attendance_break_status_photo,
                     sa.attendance_status_active
                    FROM st_attendance sa
                   WHERE sa.attendance_status_active = true AND sa.ms_wajibpajak_id = ms_karyawan.ms_wajibpajak_id AND sa.attendance_id = ms_karyawan.st_attendance_id
                  LIMIT 1) statttable) AS stattendancetbl")
            , DB::raw("( SELECT json_agg(sttunjtable.*) AS json_agg
            FROM ( SELECT sttk.sttunjangankaryawan_id,
                     sttk.sttunjangankaryawan_code,
                     sttk.sttunjangankaryawan_name,
                     sttk.sttunjangankaryawan_value,
                     sttk.sttunjangankaryawan_formula,
                     sttk.sttunjangankaryawan_calculation,
                     sttk.sttunjangankaryawan_period,
                     sttk.sttunjangankaryawan_paymentperiod,
                     sttk.sttunjangankaryawan_recievelate,
                     sttk.sttunjangankaryawan_recieveabsence,
                     sttk.sttunjangankaryawan_taxable,
                     sttk.st_grouptunjangankaryawan_id,
                     sttk.sttunjangankaryawan_active,
                     sttk.ms_wajibpajak_id,
                     sttk.sttunjangankaryawan_created_at,
                     sttk.sttunjangankaryawan_updated_at,
                     sttk.st_sttunjangankaryawan_id,
                     sgtk.stgrouptunjangankaryawan_name,
                     ( SELECT json_agg(subttable.*) AS json_agg
                            FROM ( SELECT subttk.sttunjangankaryawan_id,
                                     subttk.sttunjangankaryawan_code,
                                     subttk.sttunjangankaryawan_name,
                                     subttk.sttunjangankaryawan_value,
                                     subttk.sttunjangankaryawan_formula,
                                     subttk.sttunjangankaryawan_calculation,
                                     subttk.sttunjangankaryawan_period,
                                     subttk.sttunjangankaryawan_paymentperiod,
                                     subttk.sttunjangankaryawan_recievelate,
                                     subttk.sttunjangankaryawan_recieveabsence,
                                     subttk.sttunjangankaryawan_taxable,
                                     subttk.st_grouptunjangankaryawan_id,
                                     subttk.sttunjangankaryawan_active,
                                     subttk.ms_wajibpajak_id,
                                     subttk.sttunjangankaryawan_created_at,
                                     subttk.sttunjangankaryawan_updated_at,
                                     subttk.st_sttunjangankaryawan_id
                                    FROM st_tunjangan_karyawan subttk
                                   WHERE (subttk.sttunjangankaryawan_id = ANY (string_to_array(sttk.st_sttunjangankaryawan_id::text, ','::text)::integer[])) AND subttk.sttunjangankaryawan_active::text = '1'::text AND subttk.sttunjangankaryawan_calculation::text = 'JUMLAH_TETAP'::text) subttable) AS tunjangan_json
                    FROM st_tunjangan_karyawan sttk
                      JOIN st_group_tunjangan_karyawan sgtk ON sgtk.stgrouptunjangankaryawan_id = sttk.st_grouptunjangankaryawan_id
                   WHERE sttk.sttunjangankaryawan_active::text = '1'::text AND sttk.ms_wajibpajak_id = ms_karyawan.ms_wajibpajak_id AND (sttk.sttunjangankaryawan_id IN ( SELECT stkd.st_tunjangankaryawan_id
                            FROM st_tunjangan_karyawan_detail stkd
                           WHERE stkd.ms_karyawan_id = ms_karyawan.karyawan_id
                           AND stkd.sttunjangankaryawandet_active='1'))) sttunjtable) AS sttunjangantbl")
            , DB::raw("( SELECT json_agg(stpottable.*) AS json_agg
            FROM ( SELECT stpk.stpotongankaryawan_id,
                     stpk.stpotongankaryawan_code,
                     stpk.stpotongankaryawan_name,
                     stpk.stpotongankaryawan_value,
                     stpk.stpotongankaryawan_formula,
                     stpk.stpotongankaryawan_period,
                     stpk.stpotongankaryawan_type,
                     stpk.stpotongankaryawan_method,
                     stpk.stpotongankaryawan_maxtype,
                     stpk.stpotongankaryawan_maxtypevalue,
                     stpk.stpotongankaryawan_taxable,
                     stpk.st_grouppotongankaryawan_id,
                     stpk.stpotongankaryawan_active,
                     stpk.ms_wajibpajak_id,
                     stpk.stpotongankaryawan_created_at,
                     stpk.stpotongankaryawan_updated_at,
                     stpk.stpotongankaryawan_accumulationtime,
                     stpk.stpotongankaryawan_maxtypeformula,
                     sgpk.stgrouppotongankaryawan_name
                    FROM st_potongan_karyawan stpk
                      JOIN st_group_potongan_karyawan sgpk ON sgpk.stgrouppotongankaryawan_id = stpk.st_grouppotongankaryawan_id
                   WHERE stpk.stpotongankaryawan_active::text = '1'::text AND stpk.ms_wajibpajak_id = ms_karyawan.ms_wajibpajak_id AND (stpk.stpotongankaryawan_id IN ( SELECT spkd.st_potongankaryawan_id
                            FROM st_potongan_karyawan_detail spkd
                           WHERE spkd.ms_karyawan_id = ms_karyawan.karyawan_id
                           AND spkd.stpotongankaryawandet_active='1'))) stpottable) AS stpotongantbl"));

            $q->whereRaw("(
                CASE WHEN (karyawan_status IN ('KONTRAK','PERCOBAAN'))
                THEN 
                    TO_CHAR(karyawan_contract_begin, 'YYYY-MM-DD') <= ?
                    AND (
                        CASE WHEN stpenggajiankaryawan_period = 'KALENDER'
                                THEN TO_CHAR(karyawan_contract_end, 'YYYY-MM')  >= ?
                                
                            WHEN stpenggajiankaryawan_period = 'TANGGAL' AND TO_CHAR(karyawan_contract_end, 'DD')::int < stpenggajiankaryawan_enddate
                                THEN TO_CHAR(karyawan_contract_end, 'YYYY-MM')  >= ?

                            WHEN stpenggajiankaryawan_period = 'TANGGAL' and TO_CHAR(karyawan_contract_end, 'DD')::int > stpenggajiankaryawan_enddate
                                THEN TO_CHAR(karyawan_contract_end, 'YYYY-MM')  >= ?
                        END
                    )
                ELSE
                    TO_CHAR(karyawan_contract_begin, 'YYYY-MM-DD') <= ?
                END
            )", [$payroll_date, $payroll_period, $payroll_period, $payroll_period, $payroll_date]);

            $q->whereNotIn('karyawan_status', ['NONKARYAWAN']);
            $q->join('st_penggajian_karyawan', 'st_penggajian_id', 'stpenggajiankaryawan_id');
            $q->orderBy('karyawan_id', 'asc');
            $q->limit($maxquota);
        }, 'karyawan.payroll' => function($q) use ($payroll_period, $maxquota) {
            $q->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = ?)", [$payroll_period]);
            $q->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN']);
            $q->orderBy('ms_karyawan_id', 'asc');
            // $q->limit($maxquota);
        }, 'karyawan.divisi', 'karyawan.jabatan'])
        ->first();
        if(!$stpenggajian_karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Setting Penggajian tidak ditemukan.'
            ]);
        }
        
        $wajibpajak_id = $stpenggajian_karyawan->ms_wajibpajak_id;
        $data_payroll = [];
        $data_pph21 = [];
        if($stpenggajian_karyawan) {
            $updated_stpenggajian_ids = [];
            $stpenggajian = $stpenggajian_karyawan;

            $stpenggajian_period = $stpenggajian->stpenggajiankaryawan_period;
            // $payroll_period = null;
            if($stpenggajian_period == 'KALENDER') {
                $payroll_period_ymd = $payroll_period.'-01';
            } else if($stpenggajian_period == 'TANGGAL') {
                $prev_month = $payroll_period;
                $prev_date = str_pad($stpenggajian->stpenggajiankaryawan_startdate, 2, "0", STR_PAD_LEFT);
                $payroll_period_ymd = $prev_month.'-'.$prev_date;
            }
            
            $karyawans = $stpenggajian->karyawan;

            $appPph21KaryawanLib = new AppPPh21Library();
            $appPph21KaryawanLib->tarif21 = Tarif21Model::where(['tarif21_active' => 1])->orderBy('tarif21_startincome', 'ASC')->get()->toArray();
            // $tarif21 = $this->getTarifPPH21($total_dpp);
            // $calculation = $this->_recalculate($nonkaryawan_pendapatankotor, $karyawan);
            // var $tarif21;
            $bpjsrate = BpjsRateModel::where(['bpjsrate_active' => '1'])->get();
            // $wherestbpjs = ['stbpjskaryawan_active' => '1'];
            // $wherestholiday = ['holiday_status_active' => true];
            // if($wajibpajak_id) {
                // dd($wajibpajak_id);
                $wherestbpjs = ['stbpjskaryawan_active' => '1', 'ms_wajibpajak_id' => $wajibpajak_id];
                $wherestholiday = ['holiday_status_active' => true, 'ms_wajibpajak_id' => $wajibpajak_id];
            // }
            // dd($tunjangan_jabatan);
            $stbpjs_karyawan = SettingBpjsKaryawanModel::where($wherestbpjs)->first();
            // dd($stbpjs_karyawan);
            $holiday_karyawan = SettingHolidayModel::where($wherestholiday)->whereRaw(DB::raw("TO_CHAR(holiday_start_date, 'YYYY') = ?"), [$periode_year])->get();
            $setting = SettingModel::whereIn('setting_key', ['BPJS_TK_MAX_AMOUNT','BPJS_KES_MAX_AMOUNT'])
                ->where(['setting_active' => '1'])->get();
            $tunjangan_jabatan = TunjanganJabatanModel::where(['tunjanganjabatan_active' => '1'])->first();
            
            $bpjs_karyawan_decode = ($stbpjs_karyawan && $stbpjs_karyawan->stbpjskaryawan_value) ? json_decode($stbpjs_karyawan->stbpjskaryawan_value) : [];
            // Get bpjs data
            $bpjs_kes_ditanggung = false;
            $bpjs_tk_ditanggung = false;
            $bpjs_kes_gruptunjangan = [];
            $bpjs_tk_gruptunjangan = [];
            $bpjs_kes_lainnya = null;
            $bpjs_tk_lainnya = null;
            $bpjs_tk_jkkrate_id = null;
            $bpjs_kes_gp = false;
            $bpjs_tk_gp = false;

            // dd($bpjs_karyawan_decode);
            if($bpjs_karyawan_decode) {
                foreach($bpjs_karyawan_decode as $bpjskr) {
                    if($bpjskr->name == 'DITANGGUNG' && $bpjskr->value == 1 && $bpjskr->type == 'KESEHATAN') {
                        $bpjs_kes_ditanggung = true;
                    }
                    if($bpjskr->name == 'GAJI_POKOK' && $bpjskr->value == 1 && $bpjskr->type == 'KESEHATAN') {
                        $bpjs_kes_gp = true;
                    }
                    if($bpjskr->name == 'GROUP_TUNJANGAN' && $bpjskr->type == 'KESEHATAN') {
                        $bpjs_kes_gruptunjangan = explode(',', $bpjskr->value);
                    }
                    if($bpjskr->name == 'LAINNYA' && $bpjskr->type == 'KESEHATAN') {
                        $bpjs_kes_lainnya = $bpjskr->value;
                    }

                    if($bpjskr->name == 'DITANGGUNG' && $bpjskr->value == 1 && $bpjskr->type == 'TENAGA_KERJA') {
                        $bpjs_tk_ditanggung = true;
                    }
                    if($bpjskr->name == 'GAJI_POKOK' && $bpjskr->value == 1 && $bpjskr->type == 'TENAGA_KERJA') {
                        $bpjs_tk_gp = true;
                    }
                    if($bpjskr->name == 'GROUP_TUNJANGAN' && $bpjskr->type == 'TENAGA_KERJA') {
                        $bpjs_tk_gruptunjangan = explode(',', $bpjskr->value);
                    }
                    if($bpjskr->name == 'LAINNYA' && $bpjskr->type == 'TENAGA_KERJA') {
                        $bpjs_tk_lainnya = $bpjskr->value;
                    }
                    if($bpjskr->name == 'JKK_RATE' && $bpjskr->type == 'TENAGA_KERJA') {
                        $bpjs_tk_jkkrate_id = $bpjskr->value;
                    }
                }
            }
            // dd($bpjs_kes_ditanggung, $bpjs_tk_ditanggung);
            $jkkrate = 0;
            foreach($bpjsrate as $brate) {
                if($brate->bpjsrate_code == 'JKK') {
                    if($brate->bpjsrate_id == $bpjs_tk_jkkrate_id) {
                    $jkkrate = $brate->bpjsrate_rate;
                        break;
                    }
                }
            }
            // dd($karyawans);
            $calculation = [];
            $tarif21_nonnpwp = TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first();
            foreach($karyawans as $karyawan) {
                $payroll_method = $karyawan->karyawan_calculation_method;
                if(count($karyawan->payroll) == 0) {
                    // dd($karyawan);
                    $uuid = 'RK-'.$karyawan->ms_wajibpajak_id.'-'.Uuid::uuid4()->toString();
                    $payroll_status = 1;

                    $isnpwp = ($karyawan->karyawan_npwp == '00.000.000.0-000.000') ? 'NO-NPWP' : 'NPWP';
                    $ratenonnpwp = 100;
                    // if($isnpwp == 'NO-NPWP') {
                    //     $ratenonnpwp = $tarif21_nonnpwp->tarifnonnpwp_rate;
                    // }
                    $karyawan->ptkp->ptkp_detail = (object) PtkpDetailModel::where(['ptkpdet_category' => $karyawan->ptkp->ptkp_category, 'ptkpdet_active' => '1'])->orderBy('ptkpdet_rate_month', 'ASC')->get()->toArray();
                    
                    $bpjskes = 0;
                    $bpjstk = 0;
                    if(date('Y-m', strtotime($karyawan->karyawan_bpjskesdate)) <= date('Y-m', strtotime($payroll_period))) {
                        $bpjskes = ($karyawan->karyawan_isbpjskes && count($bpjs_karyawan_decode) > 0) ? 1 : 0; // this should check in st_bpjs_karyawan if provide or not.
                    }
                    if(date('Y-m', strtotime($karyawan->karyawan_bpjstkdate)) <= $payroll_period) {
                        $bpjstk = ($karyawan->karyawan_isbpjstk && count($bpjs_karyawan_decode) > 0) ? 1 : 0; // this should check in st_bpjs_karyawan if provide or not.
                    }
                    $isbpjs = [$bpjskes, $bpjstk, $payroll_period, $karyawan->karyawan_isbpjskes, $bpjs_karyawan_decode];

                    // Get Prorate Salary
                    $appPayslipLib = new AppPayslipLibrary();
                    $appPayslipLib->ratenonnpwp = $ratenonnpwp;
                    $appPayslipLib->payroll_period = $payroll_period_ymd;
                    $appPayslipLib->karyawan = $karyawan;
                    $appPayslipLib->stpenggajian_karyawan = $stpenggajian_karyawan;
                    $stattendance = $karyawan->attendance;
                    $attendance_days = ($stattendance) ? json_decode($stattendance->attendance_working_day) : [];
                    $appPayslipLib->attendance_days = $attendance_days;
                    $appPayslipLib->holiday_karyawan = $holiday_karyawan;
                    $appPayslipLib->bpjs_tk_gruptunjangan = $bpjs_tk_gruptunjangan;
                    $appPayslipLib->bpjs_kes_gruptunjangan = $bpjs_kes_gruptunjangan;
                    $appPayslipLib->bpjs_tk_ditanggung = $bpjs_tk_ditanggung;
                    $appPayslipLib->bpjs_kes_ditanggung = $bpjs_kes_ditanggung;
                    $appPayslipLib->bpjs_tk_gp = $bpjs_tk_gp;
                    $appPayslipLib->bpjs_kes_gp = $bpjs_kes_gp;
                    $appPayslipLib->bpjs_tk_lainnya = $bpjs_tk_lainnya;
                    $appPayslipLib->bpjs_kes_lainnya = $bpjs_kes_lainnya;
                    $appPayslipLib->setting = $setting;
                    $appPayslipLib->bpjsrate = $bpjsrate;
                    $appPayslipLib->jkkrate = $jkkrate;
                    $appPayslipLib->tunjangan_jabatan = $tunjangan_jabatan;
                    
                    $prorate_salary = $appPayslipLib->calculate_prorate_salary();
                    
                    $appPayslipLib->prorate_salary = $prorate_salary;
                    $appPayslipLib->isbpjs = $isbpjs;
                    
                    $calculate_pph21 = $appPayslipLib->calculate();

                    $payroll = $calculate_pph21['payroll'];
                    $pph21 = $calculate_pph21['pph21'];
                    $ptkp = $pph21['ptkp'];
                    // dd($ptkp);
                    $ptkp_detail = $pph21['ptkp_detail'];
                    $temp_data_payroll = [
                        'payroll_uuid' => $uuid,
                        'ms_user_id' => $karyawan->wajibpajak->ms_user_id,
                        'ms_wajibpajak_id' => $karyawan->ms_wajibpajak_id,
                        'ms_karyawan_id' => $karyawan->karyawan_id,
                        'payroll_period' => $payroll_period_ymd,
                        'payroll_total_netto' => $payroll['netto'],
                        'payroll_total_income' => $payroll['income'],
                        'payroll_total_outcome' => $payroll['outcome'],
                        'payroll_basic_salary' => $karyawan->karyawan_salary,
                        'payroll_prorate_salary' => floor($payroll['gaji']),
                        'payroll_allowance_pph21' => $payroll['allowance_pph21'],
                        'payroll_allowance_bpjskes' => $payroll['allowance_bpjskes'],
                        'payroll_allowance_bpjstk' => $payroll['allowance_bpjstk'],
                        
                        'payroll_deduction_pph21' => $payroll['deduction_pph21'],
                        'payroll_deduction_bpjskes' => $payroll['deduction_bpjskes'],
                        'payroll_deduction_bpjstk' => $payroll['deduction_bpjstk'],
                        
                        'payroll_status' => $payroll_status,
                        'payroll_bpjskes_paidbycompany' => ($bpjs_kes_ditanggung) ? 1 : 0,
                        'payroll_bpjstk_paidbycompany' => ($bpjs_tk_ditanggung) ? 1 : 0,
                        'payroll_method' => $payroll_method,
                        'payroll_karyawan_enid' => $karyawan->karyawan_enid,
                        'payroll_karyawan_name' => $karyawan->karyawan_name,
                        'payroll_karyawan_address' => $karyawan->karyawan_address,
                        'payroll_karyawan_nik' => $karyawan->karyawan_nik,
                        'payroll_karyawan_npwp' => $karyawan->karyawan_npwp,
                        'payroll_karyawan_gender' => $karyawan->karyawan_gender,
                        'payroll_karyawan_status' => $karyawan->karyawan_status,
                        'ms_divisi_id' => $karyawan->ms_karyawandivisi_id,
                        'payroll_karyawandivisi_name' => ($karyawan->ms_karyawandivisi_id) ? $karyawan->divisi->karyawandivisi_name : null,
                        'ms_jabatan_id' => $karyawan->ms_karyawanjabatan_id,
                        'payroll_karyawanjabatan_name' => ($karyawan->ms_karyawanjabatan_id) ? $karyawan->jabatan->karyawanjabatan_name : null,
                        'payroll_paid_option' => $stpenggajian_karyawan->stpenggajiankaryawan_weekendoption,
                        'payroll_autoemailslip' => $stpenggajian_karyawan->stpenggajiankaryawan_autoemailpayslip,
                        'payroll_prorate_data' => json_encode($prorate_salary),
                        'payroll_bpjssetting' => ($stbpjs_karyawan) ? $stbpjs_karyawan->stbpjskaryawan_value : null,
                        'payroll_allowance_setting' => ($payroll['tunjangan_karyawan_data']) ? json_encode($payroll['tunjangan_karyawan_data']) : null,
                        'payroll_allowance_addition' => ($payroll['custom_tunjangan_karyawan_data']) ? json_encode($payroll['custom_tunjangan_karyawan_data']) : null,
                        'payroll_deduction_setting' => ($payroll['potongan_karyawan_data']) ? json_encode($payroll['potongan_karyawan_data']) : null,
                        'payroll_deduction_addition' => ($payroll['custom_pengurangan_karyawan_data']) ? json_encode($payroll['custom_pengurangan_karyawan_data']) : null,
                        'payroll_attendancesetting' => $payroll['stattendance_karyawan_data'],
                        'payroll_penggajiansetting' => json_encode($stpenggajian_karyawan),
                    ];
            
                    $temp_data_pph21 = [
                        'tr_payroll_uuid' => $temp_data_payroll['payroll_uuid'],
                        'ms_user_id' => $temp_data_payroll['ms_user_id'],
                        'ms_wajibpajak_id' => $temp_data_payroll['ms_wajibpajak_id'],
                        'ms_karyawan_id' => $temp_data_payroll['ms_karyawan_id'],
                        'ms_ptkp_id' => $karyawan->ms_ptkp_id,
                        'pph21_ptkp_description' => $ptkp['ptkp_description'],
                        'pph21_ptkp_rate' => $ptkp['ptkp_rate'],
                        'pph21_ptkp_category' => $ptkp['ptkp_category'],
                        'ms_ptkpdet_id' => ($ptkp_detail) ? $ptkp_detail['ptkpdet_id'] : 0,
                        'pph21_ptkpdet_rate_percentage' => ($ptkp_detail) ? floatval($ptkp_detail['ptkpdet_rate_percentage']) : null,
                        'pph21_ptkpdet_rate_nominal' => ($ptkp_detail) ? intval($ptkp_detail['ptkpdet_rate_month']) : null,
                        'pph21_method' => $temp_data_payroll['payroll_method'],
                        'pph21_period' => $temp_data_payroll['payroll_period'],
                        'pph21_basic_salary' => $temp_data_payroll['payroll_basic_salary'],
                        'pph21_prorate_salary' => $temp_data_payroll['payroll_prorate_salary'],
            
                        'pph21_deduction_other' => $pph21['nominal_potongan_lain'],
                        'pph21_deduction_position' => $pph21['total_biaya_jabatan'],
                        'pph21_deduction_jamkes' => $pph21['biaya_jamkes'],
                        'pph21_deduction_jamkes_payslip' => $pph21['biaya_jamkes_payslip'],
                        'pph21_deduction_jht' => $pph21['biaya_jht'],
                        'pph21_deduction_jp' => $pph21['biaya_jp'],
            
                        'pph21_deduction_jamkes_rate' => $pph21['biaya_jamkes_rate'],
                        'pph21_deduction_jamkes_payslip_rate' => $pph21['biaya_jamkes_payslip_rate'],
                        'pph21_deduction_jht_rate' => $pph21['biaya_jht_rate'],
                        'pph21_deduction_jp_rate' => $pph21['biaya_jp_rate'],
            
                        'pph21_allowance_other' => $pph21['nominal_tunjangan_lain'],
                        'pph21_allowance_jamkes' => $pph21['penghasilan_jamkes'],
                        'pph21_allowance_jkk' => $pph21['penghasilan_jkk'],
                        'pph21_allowance_jkm' => $pph21['penghasilan_jkm'],
                        'pph21_allowance_jht' => $pph21['penghasilan_jht'],
                        'pph21_allowance_jht_payslip' => $pph21['penghasilan_jht_payslip'],
                        'pph21_allowance_jp' => $pph21['penghasilan_jp'],
                        'pph21_allowance_jp_payslip' => $pph21['penghasilan_jp_payslip'],
                        
                        'pph21_allowance_jamkes_rate' => $pph21['penghasilan_jamkes_rate'],
                        'pph21_allowance_jkk_rate' => $pph21['penghasilan_jkk_rate'],
                        'pph21_allowance_jkm_rate' => $pph21['penghasilan_jkm_rate'],
                        'pph21_allowance_jht_rate' => $pph21['penghasilan_jht_rate'],
                        'pph21_allowance_jht_payslip_rate' => $pph21['penghasilan_jht_payslip_rate'],
                        'pph21_allowance_jp_rate' => $pph21['penghasilan_jp_rate'],
                        'pph21_allowance_jp_payslip_rate' => $pph21['penghasilan_jp_payslip_rate'],
                        
                        'pph21_pkp' => $pph21['total_pkp_pertahun'],
                        'pph21_ptkp' => $pph21['total_ptkp'],
                        
                        'pph21_bruto_month' => $pph21['total_bruto_perbulan'],
                        'pph21_bruto_year' => $pph21['total_bruto_pertahun'],
                        'pph21_netto_month' => $pph21['total_neto_perbulan'],
                        'pph21_netto_year' => $pph21['total_neto_pertahun'],
                        'pph21_total_month' => $pph21['total_pph_terutang_perbulan'],
                        'pph21_total_year' => $pph21['total_pph_terutang_pertahun'],
            
                        'pph21_ptkp_data' => json_encode($ptkp),
                        'pph21_ptkpdet_data' => json_encode($ptkp_detail),
                        'pph21_tarif21_data' => ($pph21['tarif21']) ? json_encode($pph21['tarif21']) : null,
                    ];

                    // dd($temp_data_payroll, $temp_data_pph21);
                    array_push($data_payroll, $temp_data_payroll);
                    array_push($data_pph21, $temp_data_pph21);
                }
            }
            // dd($data_payroll, $data_pph21);

            array_push($updated_stpenggajian_ids, $stpenggajian->stpenggajiankaryawan_id);
        }
        // dd($nonexistpayroll);
        // $save_data_payroll = $this->calculatepayslip($request);
        // dd($data_pph21);
        DB::beginTransaction();
        try {
            if(count($data_payroll) > 0) {
                PayrollModel::upsert($data_payroll, 'payroll_uuid');

                // begin for log only
                $payroll_uuids = [];
                foreach($data_payroll as $py) {
                    array_push($payroll_uuids, $py['payroll_uuid']);
                }
                
                $payrollforlogs = PayrollModel::whereIn('payroll_uuid', $payroll_uuids)->get();
                $this->logs($payrollforlogs, $data_payroll, 'tr_payroll', 'created');
                // end for log only
            }
            if(count($data_pph21) > 0) {
                PPh21Model::upsert($data_pph21, 'tr_payroll_uuid');

                // begin for log only
                $payroll_uuids = [];
                foreach($data_pph21 as $py) {
                    array_push($payroll_uuids, $py['tr_payroll_uuid']);
                }
                $pph21forlogs = PPh21Model::whereIn('tr_payroll_uuid', $payroll_uuids)->get();
                $this->logs($pph21forlogs, $data_pph21, 'tr_pph21', 'created');
                // end for log only
            }

            if($updated_stpenggajian_ids)
                SettingPenggajianKaryawanModel::whereIn('stpenggajiankaryawan_id', $updated_stpenggajian_ids)
                ->update(['stpenggajiankaryawan_calculationtime' => $payroll_period.date('-d H:i:s')]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kalkulasi penggajian karyawan berhasil.'
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // logs the data
    private function logs($dataforlogs, $updateddata, $table = '', $eventname = 'created')
    {
        $datalogs = [];
        // dd($dataforlogs);
        if($table == 'tr_payroll') {
            $payrollModel = new PayrollModel();

            // $payroll_uuids = [];
            // foreach($dataforlogs as $dt) {
            //     array_push($payroll_uuids, $dt->payroll_uuid);
            // }

            // $updatedpayrolllogs = [];
            // $createdpayrolllogs = [];
            // foreach($updateddata as $dt) {
            //     if(in_array($dt['payroll_uuid'], $payroll_uuids)) {
            //         array_push($updatedpayrolllogs, $dt);
            //     } else {
            //         array_push($createdpayrolllogs, $dt);
            //     }
            // }
            // dd($createdpayrolllogs);
            // foreach($createdpayrolllogs as $dt) {
            //     $activitylog_properties["attributes"] = $dt;
            //     array_push($datalogs, [
            //         "log_name" => $payrollModel->getLogName(),
            //         "description" => $payrollModel->getDescriptionForEvent($eventname),
            //         "subject_type" => $payrollModel->getCauserTypeModel(),
            //         "subject_id" => $dt['payroll_id'],
            //         "causer_id" => $payrollModel->getCauserId(),
            //         "causer_type" => $payrollModel->getCauserTypeModel(),
            //         "properties" => json_encode($activitylog_properties),
            //         "created_at" => date('Y-m-d H:i:s'),
            //         "updated_at" => date('Y-m-d H:i:s')
            //     ]);
            // }

            foreach($dataforlogs as $dt) {
                $activitylog_properties["old"] = $dt;
                $activitylog_properties["attributes"] = $dt;
                array_push($datalogs, [
                    "log_name" => $payrollModel->getLogName(),
                    "description" => $payrollModel->getDescriptionForEvent($eventname),
                    "subject_type" => $payrollModel->getCauserTypeModel(),
                    "subject_id" => $dt->payroll_id,
                    "causer_id" => $payrollModel->getCauserId(),
                    "causer_type" => $payrollModel->getCauserTypeModel(),
                    "properties" => json_encode($activitylog_properties),
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s')
                ]);
            }
        } else if($table == 'tr_pph21') {
            $pph21Model = new PPh21Model();

            // $payroll_uuids = [];
            // foreach($dataforlogs as $dt) {
            //     array_push($payroll_uuids, $dt->payroll_uuid);
            // }

            // $updatedpayrolllogs = [];
            // $createdpayrolllogs = [];
            // foreach($updateddata as $dt) {
            //     if(in_array($dt['payroll_uuid'], $payroll_uuids)) {
            //         array_push($updatedpayrolllogs, $dt);
            //     } else {
            //         array_push($createdpayrolllogs, $dt);
            //     }
            // }
            // dd($createdpayrolllogs);
            // foreach($createdpayrolllogs as $dt) {
            //     $activitylog_properties["attributes"] = $dt;
            //     array_push($datalogs, [
            //         "log_name" => $payrollModel->getLogName(),
            //         "description" => $payrollModel->getDescriptionForEvent($eventname),
            //         "subject_type" => $payrollModel->getCauserTypeModel(),
            //         "subject_id" => $dt['payroll_id'],
            //         "causer_id" => $payrollModel->getCauserId(),
            //         "causer_type" => $payrollModel->getCauserTypeModel(),
            //         "properties" => json_encode($activitylog_properties),
            //         "created_at" => date('Y-m-d H:i:s'),
            //         "updated_at" => date('Y-m-d H:i:s')
            //     ]);
            // }

            foreach($dataforlogs as $dt) {
                $activitylog_properties["old"] = $dt;
                $activitylog_properties["attributes"] = $dt;
                array_push($datalogs, [
                    "log_name" => $pph21Model->getLogName(),
                    "description" => $pph21Model->getDescriptionForEvent($eventname),
                    "subject_type" => $pph21Model->getCauserTypeModel(),
                    "subject_id" => $dt->pph21_id,
                    "causer_id" => $pph21Model->getCauserId(),
                    "causer_type" => $pph21Model->getCauserTypeModel(),
                    "properties" => json_encode($activitylog_properties),
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s')
                ]);
            }
        }

        if($datalogs)
            Activity::insert($datalogs);
    }
}