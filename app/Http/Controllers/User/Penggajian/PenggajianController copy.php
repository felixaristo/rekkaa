<?php
namespace App\Http\Controllers\User\Penggajian;

use App\Http\Controllers\Controller;
use App\Libraries\AppDurationLibrary;
use App\Libraries\AppPayslipLibrary;
use App\Libraries\AppPPh21Library;
use App\Model\Master\BpjsRateModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PtkpModel;
use App\Model\Master\Tarif21Model;
use App\Model\Master\TunjanganJabatanModel;
use App\Model\Mview\VwKaryawanPayrollModel;
use App\Model\Setting\SettingAttendanceModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingHolidayModel;
use App\Model\Setting\SettingModel;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use App\Model\Setting\SettingPotonganKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use App\Model\Transaction\AttendanceKaryawanModel;
use App\Model\Transaction\NotificationModel;
use App\Model\Transaction\PayrollModel;
use App\Model\Transaction\PPh21Model;
use Carbon\Carbon;
use Error;
use Exception;
use Faker\Generator;
use Generator as GlobalGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;
use Spatie\Activitylog\Models\Activity;

class PenggajianController extends Controller
{
    var $maksemaildata = 10;
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Penggajian',
            'content' => 'user.penggajian.karyawan.index',
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

     /**
     * Display a detail of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function detail($payrollId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $payroll = PayrollModel::with(['karyawan'])->where([
            'payroll_id' => $payrollId,
            'ms_wajibpajak_id' => $wajibpajak_id,
            'payroll_active' => '1'
        ])
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->first();

        if(!$payroll) {
            abort(404);
        }

        // dd($payroll);
        // dd($payroll->karyawan->jabatan->karyawanjabatan_name);
        $data = [
            'title' => 'Penggajian',
            'payroll' => $payroll,
            'content' => 'user.penggajian.karyawan.detail',
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $limit = ($request->get('length')) ? intval($request->get('length')) : 10;
        $offset = ($request->get('start')) ? intval($request->get('start')) : 0;
        $order_columns = ['ms_karyawan_id','payroll_karyawan_name','ms_karyawan_id','payroll_total_netto','payroll_status'];
        $order_col = $order_columns[0];
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $periode = ($request->get('periode')) ? $request->get('periode') : date('m-Y');
        // dd($periode);
        $status = $request->get('status');
        $karyawan_ids = $request->get('karyawan_ids');

        $where = [
            // 'karyawan_active' => 1,
            // 'karyawan_contract_end' => null,
            // 'karyawan_contract_now' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ];
        if($status != '-1') {
            $where['payroll_status'] = intval($status);
        }
        $list = PayrollModel::with(['karyawan', 'pph21'])
        // with(['currentperiod_payroll'])
        ->when($karyawan_ids, function($q, $karyawan_ids) {
            return $q->whereIn('ms_karyawan_id', $karyawan_ids);
        })
        ->where($where)
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$periode])
        // ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
        ->take($limit)->skip($offset)->orderBy($order_col, $order_type)->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = PayrollModel::
        when($karyawan_ids, function($q, $karyawan_ids) {
            return $q->whereIn('ms_karyawan_id', $karyawan_ids);
        })
        ->where($where)->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$periode])
        // ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
        ->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function confirmation(Request $request)
    {
        // INFO, uuid SHOULD REQUIRED
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $payroll_uuids = $request->get('payroll_uuids');
        $payrolls = PayrollModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id, 
            'payroll_status' => 1, 
            'payroll_lock' => 0
        ])
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereIn('payroll_uuid', $payroll_uuids)->get();

        if(count($payrolls) <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Perhitungan penggajian tidak ditemukan!',
            ]);
        }

        $uuids = [];
        foreach($payrolls as $py) {
            array_push($uuids, $py->payroll_uuid);
        }
        DB::beginTransaction();
        try {
            if($uuids) {
                PayrollModel::whereIn('payroll_uuid', $uuids)
                ->update(['payroll_lock' => 1, 'payroll_status' => 2, 'payroll_lock_at' => date('Y-m-d H:i:s')]);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Konfirmasi penggajian karyawan berhasil.'
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function paid(Request $request)
    {
        // INFO, uuid SHOULD REQUIRED
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $payroll_uuids = $request->get('payroll_uuids');
        $payrolls = PayrollModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id, 
            'payroll_status' => 2, 
            'payroll_lock' => 1
        ])
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereIn('payroll_uuid', $payroll_uuids)->get();

        if(count($payrolls) <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran penggajian tidak ditemukan!',
            ]);
        }

        $uuids = [];
        // $py->stpenggajian = '';
        foreach($payrolls as $py) {
            $stpenggajian = ($py->payroll_penggajiansetting) ? json_decode($py->payroll_penggajiansetting) : null;
            $payment_date = ($stpenggajian) ? $stpenggajian->stpenggajiankaryawan_paymentdate : date('d');
            // dd($payment_date);
            $uuids[$payment_date][] = $py->payroll_uuid;
        }
        // dd($uuids);
        DB::beginTransaction();
        try {
            if(count($uuids) > 0) {
                foreach($uuids as $date => $uuid) {
                    PayrollModel::whereIn('payroll_uuid', $uuid)
                    ->update(['payroll_status' => 3, 'payroll_paid_at' => date('Y-m-'.$date.' H:i:s')]);
                }
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran penggajian karyawan berhasil.'
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

     /**
     * Display a cetak of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cetak($payrollUuid, Request $request)
    { 
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $payroll = PayrollModel::with(['wajibpajak','karyawan','karyawan.penggajian'])->where([
            'payroll_uuid' => $payrollUuid,
            'ms_wajibpajak_id' => $wajibpajak_id,
            'payroll_status' => 3,
            'payroll_active' => '1',
        ])
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->first();

        if(!$payroll) {
            abort(404);
        }

        // dd($payroll->karyawan->penggajian);
        // dd($payroll->karyawan->jabatan->karyawanjabatan_name);
        $data = [
            'title' => 'Cetak Penggajian',
            'payroll' => $payroll,
            'content' => 'user.penggajian.cetak.payslip-cetak',
        ];
        return view('user.penggajian.cetak.payslip-cetak', $data);
        // return view('user.penggajian.cetak.payslip-multiple-cetak', $data);
    }

     /**
     * Display a multi cetak of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function multicetak(Request $request)
    {
        $payroll_uuids = $request->get('payroll_uuids');
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $payrolls = PayrollModel::with(['wajibpajak','karyawan','karyawan.penggajian'])->where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'payroll_status' => 3,
            'payroll_active' => '1',
        ])
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereIn('payroll_uuid', $payroll_uuids)->get();

        if(count($payrolls) < 1) {
            abort(404);
        }

        if(count($payrolls) > 100) {
            abort(404, 'Download slip tidak boleh lebih dari 100 data.');
        }

        // dd($payroll);
        // dd($payroll->karyawan->jabatan->karyawanjabatan_name);
        $data = [
            'title' => 'Cetak Penggajian',
            'payrolls' => $payrolls,
            'content' => 'user.penggajian.cetak.payslip-multi-cetak',
        ];
        return view('user.penggajian.cetak.payslip-multi-cetak', $data);
        // return view('user.penggajian.cetak.payslip-multiple-cetak', $data);
    }

    /**
     * Send email of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function kirim(Request $request)
    {
        $payroll_uuids = $request->input('payroll_uuids');
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        if(!is_array($payroll_uuids)) {
            return response()->json([
                'success' => false,
                'message' => "Format harus array!"
            ]);
        }

        if(count($payroll_uuids) > $this->maksemaildata) {
            return response()->json([
                'success' => false,
                'message' => "Download slip tidak boleh lebih dari {$this->maksemaildata} data."
            ]);
        }

        $payrolls = PayrollModel::with(['wajibpajak','karyawan'])->where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'payroll_status' => 3,
            'payroll_active' => '1',
        ])
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereIn('payroll_uuid', $payroll_uuids)->get();

        if(count($payrolls) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan!'
            ]);
        }
        
        $karyawanisnotuser = [];
        foreach($payrolls as $payroll) {
            if($payroll->karyawan->karyawan_isuser != 1) {
                array_push($karyawanisnotuser, [
                    'name' => $payroll->karyawan->karyawan_name,
                    'enid' => $payroll->karyawan->karyawan_enid,
                ]);
            }
        }
        if(count($karyawanisnotuser) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Kirim informasi slip gaji gagal. Karyawn berikut bukan merupakan user!',
                'data' => [
                    'karyawan' => $karyawanisnotuser
                ]
            ]);
        }
        $notification_data = [];
        foreach($payrolls as $payroll) {
            $payroll_period = Carbon::parse($payroll->payroll_period)->translatedFormat('F Y');
            array_push($notification_data, [
                'notification_title' => 'Rekkaa - Informasi Slip Gaji Periode - '. $payroll_period,
                'notification_type' => 'EMAIL',
                'notification_from' => env("MAIL_FROM_ADDRESS"),
                'notification_to' => $payroll->karyawan->karyawan_email,
                'ms_user_id' => $payroll->wajibpajak->ms_user_id,
                'ms_wajibpajak_id' => $wajibpajak_id,
                'notification_view' => 'email.email-slip-gaji-karyawan',
                'notification_data' => json_encode([
                    'entity' => [
                        'name' => $payroll->wajibpajak->wajibpajak_name,
                        'user_name' => $payroll->karyawan->karyawan_name,
                        'period' => $payroll_period,
                    ]
                ])
            ]);
        }
        
        // dd($notification_data);
        // insert tr_notification
        DB::beginTransaction();
        try {
            NotificationModel::insert($notification_data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kirim informasi slip berhasil.'
            ]);
        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // public function calculatecronjob(Request $request)
    // {
    //     $stpenggajian_id = $request->get('stpenggajian_id');
    //     $periode = $request->get('periode');
    //     $period = ($periode) ? date('Y-m', strtotime($periode)) : date('Y-m', strtotime("-1 month", strtotime(date('Y-m'))));
    //     $prev_period = date('Y-m', strtotime("-1 month", strtotime($period)));
    //     $date = $period.'-'.date('d');
    //     // dd($period);
    //     $stpenggajian_karyawan = SettingPenggajianKaryawanModel::where([
    //         // 'stpenggajiankaryawan_id' => 20,
    //         'stpenggajiankaryawan_active' => 1,
    //         'stpenggajiankaryawan_id' => $stpenggajian_id
    //         // 'stpenggajiankaryawan_period' => 'KALENDER'
    //     ])
    //     ->with(['karyawan' => function($q) use($stpenggajian_id, $period, $date) {
    //         $q->select('st_penggajian_id', 'ms_karyawan.*'
    //         , DB::raw("( SELECT row_to_json(ptkptable.*) AS row_to_json
    //         FROM ( SELECT mpp.ptkp_id,
    //                  mpp.ptkp_marriage_status,
    //                  mpp.ptkp_description,
    //                  mpp.ptkp_rate,
    //                  mpp.ptkp_active,
    //                  mpp.ptkp_created_at,
    //                  mpp.ptkp_updated_at
    //                 FROM ms_ptkp mpp
    //                WHERE mpp.ptkp_active::text = '1'::text AND mpp.ptkp_id = ms_karyawan.ms_ptkp_id
    //               LIMIT 1) ptkptable) AS ptkptbl")
    //         , DB::raw("( SELECT row_to_json(statttable.*) AS row_to_json
    //         FROM ( SELECT sa.attendance_id,
    //                  sa.ms_wajibpajak_id,
    //                  sa.attendance_description,
    //                  sa.attendance_working_day,
    //                  sa.attendance_check_in,
    //                  sa.attendance_check_in_tolerance,
    //                  sa.attendance_check_out,
    //                  sa.attendance_start_break,
    //                  sa.attendance_end_break,
    //                  sa.attendance_break_status,
    //                  sa.attendance_break_type,
    //                  sa.attendance_check_out_status_photo,
    //                  sa.attendance_check_in_status_photo,
    //                  sa.attendance_location_status,
    //                  sa.attendance_location_address,
    //                  sa.attendance_location_longitude,
    //                  sa.attendance_location_latitude,
    //                  sa.attendance_created_at,
    //                  sa.attendance_updated_at,
    //                  sa.attendance_break_status_photo,
    //                  sa.attendance_status_active
    //                 FROM st_attendance sa
    //                WHERE sa.attendance_status_active = true AND sa.ms_wajibpajak_id = ms_karyawan.ms_wajibpajak_id AND sa.attendance_id = ms_karyawan.st_attendance_id
    //               LIMIT 1) statttable) AS stattendancetbl")
    //         , DB::raw("( SELECT json_agg(sttunjtable.*) AS json_agg
    //         FROM ( SELECT sttk.sttunjangankaryawan_id,
    //                  sttk.sttunjangankaryawan_code,
    //                  sttk.sttunjangankaryawan_name,
    //                  sttk.sttunjangankaryawan_value,
    //                  sttk.sttunjangankaryawan_formula,
    //                  sttk.sttunjangankaryawan_calculation,
    //                  sttk.sttunjangankaryawan_period,
    //                  sttk.sttunjangankaryawan_paymentperiod,
    //                  sttk.sttunjangankaryawan_recievelate,
    //                  sttk.sttunjangankaryawan_recieveabsence,
    //                  sttk.sttunjangankaryawan_taxable,
    //                  sttk.st_grouptunjangankaryawan_id,
    //                  sttk.sttunjangankaryawan_active,
    //                  sttk.ms_wajibpajak_id,
    //                  sttk.sttunjangankaryawan_created_at,
    //                  sttk.sttunjangankaryawan_updated_at,
    //                  sttk.st_sttunjangankaryawan_id,
    //                  sgtk.stgrouptunjangankaryawan_name,
    //                  ( SELECT json_agg(subttable.*) AS json_agg
    //                         FROM ( SELECT subttk.sttunjangankaryawan_id,
    //                                  subttk.sttunjangankaryawan_code,
    //                                  subttk.sttunjangankaryawan_name,
    //                                  subttk.sttunjangankaryawan_value,
    //                                  subttk.sttunjangankaryawan_formula,
    //                                  subttk.sttunjangankaryawan_calculation,
    //                                  subttk.sttunjangankaryawan_period,
    //                                  subttk.sttunjangankaryawan_paymentperiod,
    //                                  subttk.sttunjangankaryawan_recievelate,
    //                                  subttk.sttunjangankaryawan_recieveabsence,
    //                                  subttk.sttunjangankaryawan_taxable,
    //                                  subttk.st_grouptunjangankaryawan_id,
    //                                  subttk.sttunjangankaryawan_active,
    //                                  subttk.ms_wajibpajak_id,
    //                                  subttk.sttunjangankaryawan_created_at,
    //                                  subttk.sttunjangankaryawan_updated_at,
    //                                  subttk.st_sttunjangankaryawan_id
    //                                 FROM st_tunjangan_karyawan subttk
    //                                WHERE (subttk.sttunjangankaryawan_id = ANY (string_to_array(sttk.st_sttunjangankaryawan_id::text, ','::text)::integer[])) AND subttk.sttunjangankaryawan_active::text = '1'::text AND subttk.sttunjangankaryawan_calculation::text = 'JUMLAH_TETAP'::text) subttable) AS tunjangan_json
    //                 FROM st_tunjangan_karyawan sttk
    //                   JOIN st_group_tunjangan_karyawan sgtk ON sgtk.stgrouptunjangankaryawan_id = sttk.st_grouptunjangankaryawan_id
    //                WHERE sttk.sttunjangankaryawan_active::text = '1'::text AND sttk.ms_wajibpajak_id = ms_karyawan.ms_wajibpajak_id AND (sttk.sttunjangankaryawan_id IN ( SELECT stkd.st_tunjangankaryawan_id
    //                         FROM st_tunjangan_karyawan_detail stkd
    //                        WHERE stkd.ms_karyawan_id = ms_karyawan.karyawan_id))) sttunjtable) AS sttunjangantbl")
    //         , DB::raw("( SELECT json_agg(stpottable.*) AS json_agg
    //         FROM ( SELECT stpk.stpotongankaryawan_id,
    //                  stpk.stpotongankaryawan_code,
    //                  stpk.stpotongankaryawan_name,
    //                  stpk.stpotongankaryawan_value,
    //                  stpk.stpotongankaryawan_formula,
    //                  stpk.stpotongankaryawan_period,
    //                  stpk.stpotongankaryawan_type,
    //                  stpk.stpotongankaryawan_method,
    //                  stpk.stpotongankaryawan_maxtype,
    //                  stpk.stpotongankaryawan_maxtypevalue,
    //                  stpk.stpotongankaryawan_taxable,
    //                  stpk.st_grouppotongankaryawan_id,
    //                  stpk.stpotongankaryawan_active,
    //                  stpk.ms_wajibpajak_id,
    //                  stpk.stpotongankaryawan_created_at,
    //                  stpk.stpotongankaryawan_updated_at,
    //                  stpk.stpotongankaryawan_accumulationtime,
    //                  stpk.stpotongankaryawan_maxtypeformula,
    //                  sgpk.stgrouppotongankaryawan_name
    //                 FROM st_potongan_karyawan stpk
    //                   JOIN st_group_potongan_karyawan sgpk ON sgpk.stgrouppotongankaryawan_id = stpk.st_grouppotongankaryawan_id
    //                WHERE stpk.stpotongankaryawan_active::text = '1'::text AND stpk.ms_wajibpajak_id = ms_karyawan.ms_wajibpajak_id AND (stpk.stpotongankaryawan_id IN ( SELECT spkd.st_potongankaryawan_id
    //                         FROM st_potongan_karyawan_detail spkd
    //                        WHERE spkd.ms_karyawan_id = ms_karyawan.karyawan_id))) stpottable) AS stpotongantbl"));

    //         $q->whereRaw("(
    //             CASE WHEN (karyawan_status IN ('KONTRAK','PERCOBAAN'))
    //             THEN 
    //                 TO_CHAR(karyawan_contract_begin, 'YYYY-MM-DD') <= ?
    //                 AND (
    //                     CASE WHEN stpenggajiankaryawan_period = 'KALENDER'
    //                             THEN TO_CHAR(karyawan_contract_end, 'YYYY-MM')  >= ?
                                
    //                         WHEN stpenggajiankaryawan_period = 'TANGGAL' AND TO_CHAR(karyawan_contract_end, 'DD')::int < stpenggajiankaryawan_enddate
    //                             THEN TO_CHAR(karyawan_contract_end, 'YYYY-MM')  >= ?

    //                         WHEN stpenggajiankaryawan_period = 'TANGGAL' and TO_CHAR(karyawan_contract_end, 'DD')::int > stpenggajiankaryawan_enddate
    //                             THEN TO_CHAR(karyawan_contract_end, 'YYYY-MM')  >= ?
    //                     END
    //                 )
    //             ELSE
    //                 TO_CHAR(karyawan_contract_begin, 'YYYY-MM-DD') <= ?
    //             END
    //         )", [$date, $period, $period, $period, $period]);

    //         $q->join('st_penggajian_karyawan', 'st_penggajian_id', 'stpenggajiankaryawan_id');
            
    //     }, 'karyawan.payroll' => function($q) use ($period) {
    //         $q->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = ?)", [$period]);
    //         $q->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN']);
    //     }, 'karyawan.divisi', 'karyawan.jabatan'])
    //     ->first();
    //     // dd($stpenggajian_karyawan->karyawan);
    //     // dd( date('Y-m-d', strtotime("+1 month", strtotime($periode))));
        
    //     $wherestbpjs = ['stbpjskaryawan_active' => '1', 'ms_wajibpajak_id' => $stpenggajian_karyawan->ms_wajibpajak_id];
    //     $stbpjs_karyawan = SettingBpjsKaryawanModel::where($wherestbpjs)->first();

    //     // $wheretarif21 = ['tarif21_active' => '1'];
    //     // $tarif21 = Tarif21Model::where($wheretarif21)->first();

    //     // $wherestattendance = ['attendance_status_active' => true, 'ms_wajibpajak_id' => $stpenggajian_karyawan->ms_wajibpajak_id];
    //     // $stattendance_karyawan = SettingAttendanceModel::where($wherestattendance)->first();

    //     // $whereptkp = ['ptkp_active' => '1'];
    //     // $ptkp = PtkpModel::where()
    //     // dd($stattendance_karyawan);

    //     $nonexistpayroll = [];
    //     $nonexistpph21 = [];
    //     $uuids = [];
    //     if($stpenggajian_karyawan) {
    //         $updated_stpenggajian_ids = [];
    //         $stpenggajian = $stpenggajian_karyawan;

    //         $stpenggajian_period = $stpenggajian->stpenggajiankaryawan_period;
    //         $payroll_period = null;
    //         if($stpenggajian_period == 'KALENDER') {
    //             $payroll_period = $period.'-01';
    //         } else if($stpenggajian_period == 'TANGGAL') {
    //             $prev_month = $period;
    //             $prev_date = str_pad($stpenggajian->stpenggajiankaryawan_startdate, 2, "0", STR_PAD_LEFT);
    //             $payroll_period = $prev_month.'-'.$prev_date;
    //         }
            
    //         $karyawans = $stpenggajian->karyawan;
    //         // dd($karyawans[1]->wajibpajak);
    //         foreach($karyawans as $karyawan) {
    //             // $karyawan = (object) $karyawan;
    //             // $karyawan_payroll_period = date('Y-m');
    //             // if($karyawan_payroll_period != $period) {

    //             if(count($karyawan->payroll) == 0) {
    //                 // dd($karyawan);
    //                 $uuid = 'RK-'.$karyawan->ms_wajibpajak_id.'-'.Uuid::uuid4()->toString();
    //                 $payroll_method = ($karyawan->karyawan_calculation_method) ? $karyawan->karyawan_calculation_method : 'GROSS';
    //                 $uuids[$karyawan->ms_wajibpajak_id]['uuid'] = $uuid;
    //                 $uuids[$karyawan->ms_wajibpajak_id]['sttunjangan'] = $karyawan->sttunjangantbl;

    //                 $temp_data = [
    //                     'payroll_uuid' => $uuid,
    //                     'ms_user_id' => $karyawan->wajibpajak->ms_user_id,
    //                     'ms_wajibpajak_id' => $karyawan->ms_wajibpajak_id,
    //                     'ms_karyawan_id' => $karyawan->karyawan_id,
    //                     'payroll_period' => $payroll_period,
    //                     'payroll_total_netto' => 0,
    //                     'payroll_total_income' => 0,
    //                     'payroll_total_outcome' => 0,
    //                     'payroll_basic_salary' => 0,
    //                     'payroll_prorate_salary' => 0,
    //                     'payroll_allowance_pph21' => 0,
    //                     'payroll_allowance_bpjskes' => 0,
    //                     'payroll_allowance_bpjstk' => 0,
    //                     'payroll_deduction_pph21' => 0,
    //                     'payroll_deduction_bpjskes' => 0,
    //                     'payroll_deduction_bpjstk' => 0,
    //                     'payroll_bpjskes_paidbycompany' => 0,
    //                     'payroll_bpjstk_paidbycompany' => 0,
    //                     'payroll_method' => $payroll_method,

    //                     'payroll_status' => 1,
    //                     'payroll_karyawan_enid' => $karyawan->karyawan_enid,
    //                     'payroll_karyawan_name' => $karyawan->karyawan_name,
    //                     'payroll_karyawan_address' => $karyawan->karyawan_address,
    //                     'payroll_karyawan_nik' => $karyawan->karyawan_nik,
    //                     'payroll_karyawan_npwp' => ($karyawan->karyawan_npwp) ? $karyawan->karyawan_npwp : '-',
    //                     'payroll_karyawan_gender' => $karyawan->karyawan_gender,
    //                     'payroll_karyawan_status' => $karyawan->karyawan_status,
    //                     // 'ms_divisi_id' => $karyawan->ms_divisi_id,
    //                     // 'payroll_karyawandivisi_name' => $karyawan->divisi->karyawandivisi_name,
    //                     // 'ms_jabatan_id' => $karyawan->ms_jabatan_id,
    //                     // 'payroll_karyawanjabatan_name' => $karyawan->jabatan->karyawanjabatan_name,
    //                     'payroll_paid_option' => $stpenggajian->stpenggajiankaryawan_weekendoption,
    //                     'payroll_autoemailslip' => $stpenggajian->stpenggajiankaryawan_autoemailpayslip,
                        
    //                     'payroll_allowance_setting' => $karyawan->sttunjangantbl,
    //                     'payroll_deduction_setting' => $karyawan->stpotongantbl,
    //                     'payroll_bpjssetting' => ($stbpjs_karyawan) ? $stbpjs_karyawan->stbpjskaryawan_value : null,
    //                     'payroll_penggajiansetting' => json_encode($stpenggajian),
    //                     'payroll_attendancesetting' => $karyawan->stattendancetbl,
                        
    //                 ];

    //                 $ptkp = ($karyawan->ptkptbl) ? json_decode($karyawan->ptkptbl) : null;
    //                 $temp_data_pph21 = [
    //                     'tr_payroll_uuid' => $temp_data['payroll_uuid'],
    //                     'ms_user_id' => $temp_data['ms_user_id'],
    //                     'ms_wajibpajak_id' => $temp_data['ms_wajibpajak_id'],
    //                     'ms_karyawan_id' => $temp_data['ms_karyawan_id'],
    //                     'ms_ptkp_id' => ($ptkp) ? $ptkp->ptkp_id : 0,
    //                     'pph21_ptkp_description' => ($ptkp) ? $ptkp->ptkp_description : null,
    //                     'pph21_ptkp_rate' => ($ptkp) ? $ptkp->ptkp_rate : null,
    //                     'pph21_method' => $temp_data['payroll_method'],
    //                     'pph21_period' => $temp_data['payroll_period'],
    //                     'pph21_basic_salary' => $temp_data['payroll_basic_salary'],
    //                     'pph21_prorate_salary' => $temp_data['payroll_prorate_salary'],
    //                     'pph21_ptkp_data' => $karyawan->ptkptbl,
    //                     // 'pph21_tarif21_data' => ($tarif21) ? json_encode($tarif21) : null,
    //                 ];


    //                 array_push($nonexistpayroll, $temp_data);
    //                 array_push($nonexistpph21, $temp_data_pph21);

    //                 // $karyawan_exist = true;
    //             }
    //         }

    //         array_push($updated_stpenggajian_ids, $stpenggajian->stpenggajiankaryawan_id);
    //     }
    //     // dd($nonexistpayroll);
    //     // $save_data_payroll = $this->calculatepayslip($request);
    //     // dd($uuids);
    //     DB::beginTransaction();
    //     try {
    //         if(count($nonexistpayroll) > 0) {
    //             PayrollModel::upsert($nonexistpayroll, 'payroll_uuid');
    //         }
    //         if(count($nonexistpph21) > 0) {
    //             PPh21Model::upsert($nonexistpph21, 'tr_payroll_uuid');
    //         }

    //         if(count($uuids) > 0) {
    //             $save_data_payroll = [];
    //             $save_data_pph21 = [];

    //             $bpjsrate = BpjsRateModel::where(['bpjsrate_active' => '1'])->get();
    //             $setting = SettingModel::whereIn('setting_key', ['BPJS_TK_MAX_AMOUNT','BPJS_KES_MAX_AMOUNT'])
    //                 ->where(['setting_active' => '1'])->get();
    //             $tunjangan_jabatan = TunjanganJabatanModel::where(['tunjanganjabatan_active' => '1'])->first();

    //             $current_wajibpajak_id = null;
    //             $stbpjs_karyawan = null;
    //             $holiday_karyawan = null;

    //             foreach($uuids as $wajibpajak_id => $val) {
    //                 if($current_wajibpajak_id != $wajibpajak_id) {
    //                     $wherestbpjs = ['stbpjskaryawan_active' => '1', 'ms_wajibpajak_id' => $wajibpajak_id];
    //                     $wherestholiday = ['holiday_status_active' => true, 'ms_wajibpajak_id' => $wajibpajak_id];

    //                     $stbpjs_karyawan = SettingBpjsKaryawanModel::where($wherestbpjs)->first();
    //                     $holiday_karyawan = SettingHolidayModel::where($wherestholiday)->get();

    //                     $current_wajibpajak_id = $wajibpajak_id;
    //                 }

    //                 $temp_data = $this->calculatepayslip([
    //                     'payroll_uuids' => $val['uuid'], 
    //                     'wajibpajak_id' => $wajibpajak_id,
    //                     'sttunjangan' => $val['sttunjangan'],
    //                     'setting' => $setting,
    //                     'tunjangan_jabatan' => $tunjangan_jabatan,
    //                     'stbpjs_karyawan' => $stbpjs_karyawan,
    //                     'holiday_karyawan' => $holiday_karyawan,
    //                     'bpjsrate' => $bpjsrate,
    //                 ]);
    //                 $save_data_payroll = array_merge($save_data_payroll, $temp_data['data_payroll']);
    //                 $save_data_pph21 = array_merge($save_data_pph21, $temp_data['data_pph21']);
    //             }
    //             // dd($save_data_pph21);
                
    //             if(count($save_data_payroll) > 0) {
    //                 PayrollModel::upsert($save_data_payroll, 'payroll_uuid');
    //             }
    //             if(count($save_data_pph21) > 0) {
    //                 PPh21Model::upsert($save_data_pph21, 'tr_payroll_uuid');
    //             }
    //         }
    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Kalkulasi penggajian karyawan berhasil.'
    //         ]);
            

    //     } catch(Error $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
    
    public function calculate(Request $request)
    {
        // INFO, uuid SHOULD REQUIRED
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $payroll_uuids = $request->get('payroll_uuids');
        
        $custom_allowance_data = $request->post('custom_allowance_data');
        $custom_deduction_data = $request->post('custom_deduction_data');

        if(!is_array($payroll_uuids)) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan isi ID penggajian.'
            ]);
        }
        // dd($payroll_uuids, $custom_deduction_data);
        if($custom_allowance_data) {
            $idx = 0;
            foreach($custom_allowance_data as $cad) {
                if(!$cad) {
                    unset($custom_allowance_data[$idx]);
                }
                $idx++;
            }
        }
        
        if($custom_deduction_data) {
            $idxx = 0;
            foreach($custom_deduction_data as $cad) {
                if(!$cad) {
                    unset($custom_deduction_data[$idxx]);
                }
                $idxx++;
            }
        }

        $setting = SettingModel::whereIn('setting_key', ['BPJS_TK_MAX_AMOUNT','BPJS_KES_MAX_AMOUNT'])
        ->where(['setting_active' => '1'])->get();
        $tunjangan_jabatan = TunjanganJabatanModel::where(['tunjanganjabatan_active' => '1'])->first();

        $wherestbpjs = ['stbpjskaryawan_active' => '1'];
        $wherestholiday = ['holiday_status_active' => true];
        if($wajibpajak_id) {
            $wherestbpjs = ['stbpjskaryawan_active' => '1', 'ms_wajibpajak_id' => $wajibpajak_id];
            $wherestholiday = ['holiday_status_active' => true, 'ms_wajibpajak_id' => $wajibpajak_id];
        }
        // dd($tunjangan_jabatan);
        $stbpjs_karyawan = SettingBpjsKaryawanModel::where($wherestbpjs)->first();
        $holiday_karyawan = SettingHolidayModel::where($wherestholiday)->get();
        $bpjsrate = BpjsRateModel::where(['bpjsrate_active' => '1'])->get();
        $temp_data = $this->calculatepayslip([
            'wajibpajak_id' => $wajibpajak_id, 
            'payroll_uuids' => $payroll_uuids, 
            'custom_allowance_data' => $custom_allowance_data, 
            'custom_deduction_data' => $custom_deduction_data,
            'setting' => $setting,
            'tunjangan_jabatan' => $tunjangan_jabatan,
            'stbpjs_karyawan' => $stbpjs_karyawan,
            'holiday_karyawan' => $holiday_karyawan,
            'bpjsrate' => $bpjsrate,
        ]);

        $save_data_payroll = $temp_data['data_payroll'];
        $save_data_pph21 = $temp_data['data_pph21'];
        
        // dd($save_data_payroll);
        DB::beginTransaction();
        try {
            if($save_data_payroll) {
                PayrollModel::upsert($save_data_payroll, 'payroll_uuid');

                // begin for log only
                $payroll_uuids = [];
                foreach($save_data_payroll as $py) {
                    array_push($payroll_uuids, $py['payroll_uuid']);
                }
                
                $payrollforlogs = PayrollModel::whereIn('payroll_uuid', $payroll_uuids)->get();
                $this->logs($payrollforlogs, $save_data_payroll, 'tr_payroll', 'updated');
                // end for log only
            }

            if($save_data_pph21) {
                PPh21Model::upsert($save_data_pph21, 'tr_payroll_uuid');
                
                // begin for log only
                $payroll_uuids = [];
                foreach($save_data_pph21 as $py) {
                    array_push($payroll_uuids, $py['tr_payroll_uuid']);
                }
                $pph21forlogs = PPh21Model::whereIn('tr_payroll_uuid', $payroll_uuids)->get();
                $this->logs($pph21forlogs, $save_data_pph21, 'tr_pph21', 'updated');
                // end for log only
            }

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

    public function calculatepayslip($data)
    {
        $wajibpajak_id = isset($data['wajibpajak_id']) && $data['wajibpajak_id'] ? $data['wajibpajak_id'] : null; 
        $calculation_period = isset($data['calculation_period']) && $data['calculation_period'] ? $data['calculation_period'] : null;
        $payroll_uuids = isset($data['payroll_uuids']) && $data['payroll_uuids'] ? $data['payroll_uuids'] : null;
        $custom_allowance_data = isset($data['custom_allowance_data']) && $data['custom_allowance_data'] ? $data['custom_allowance_data'] : null;
        $custom_deduction_data = isset($data['custom_deduction_data']) && $data['custom_deduction_data'] ? $data['custom_deduction_data'] : null;
        $setting = $data['setting'];
        $tunjangan_jabatan = $data['tunjangan_jabatan'];
        // $sttunjangan = isset($data['sttunjangan']) && $data['sttunjangan'] ? $data['sttunjangan'] : null;
        $stbpjs_karyawan = $data['stbpjs_karyawan'];
        $holiday_karyawan = $data['holiday_karyawan'];
        $bpjsrate = $data['bpjsrate'];
        // dd($payroll_uuids);
        // $calculation_period = $request->input('calculation_period');
        // $payroll_ids = $request->get('payroll_ids');
        
        // $current_period = date('Y-m');
        // $current_month = date('n'); // month without leading zero
        
        $appPph21Lib = new AppPPh21Library();
        $bpjs_karyawan_decode = ($stbpjs_karyawan && $stbpjs_karyawan->stbpjskaryawan_value) ? json_decode($stbpjs_karyawan->stbpjskaryawan_value) : [];
        // dd($holiday_karyawan);
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
        // dd($bpjs_kes_gruptunjangan);
        
        
        
        // $karyawans = KaryawanModel::with(['ptkp'])->where(['ms_wajibpajak_id' => $wajibpajak_id])->get();
        
        // dd($payroll_ids);
        if($wajibpajak_id) {
            $wherevwpayroll = ['ms_wajibpajak_id' => $wajibpajak_id];
        }
        if($payroll_uuids && is_array($payroll_uuids)) {
            // $karyawans = VwKaryawanPayrollModel::where($wherevwpayroll)
            $payrolls = PayrollModel::with(['karyawan' => function($q) {
                $q->select("ms_karyawan.*"
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
                               AND stkd.sttunjangankaryawandet_active='1'))) sttunjtable) AS sttunjangantbl"));
                $q->whereNotIn('karyawan_status', ['NONKARYAWAN']);
            }, 'pph21', 'karyawan.tunjangandetail'])->where($wherevwpayroll)
            ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
            ->where(function($query) {
                $query->where('payroll_lock', '0')
                    ->orWhereNull('payroll_lock');
            })->whereIn('payroll_uuid', $payroll_uuids)->get();
        } else {
            $payrolls = PayrollModel::with(['karyawan' => function($q) {
                $q->select("ms_karyawan.*"
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
                               AND stkd.sttunjangankaryawandet_active='1'))) sttunjtable) AS sttunjangantbl"));
                $q->whereNotIn('karyawan_status', ['NONKARYAWAN']);
            }, 'pph21', 'karyawan.tunjangandetail'])->where($wherevwpayroll)
            ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
            ->where(function($query) {
                $query->where('payroll_lock', '0')
                    ->orWhereNull('payroll_lock');
            })->get();
        }
        // dd($payrolls[0]);

        $jkkrate = 0;
        foreach($bpjsrate as $brate) {
            if($brate->bpjsrate_code == 'JKK') {
                if($brate->bpjsrate_id == $bpjs_tk_jkkrate_id) {
                   $jkkrate = $brate->bpjsrate_rate;
                   break;
                }
            }
        }
        $save_data_payroll = [];
        $save_data_pph21 = [];
        foreach($payrolls as $py) {
            $method = ($py->karyawan->karyawan_method) ? $py->karyawan->karyawan_method : 'GROSS';
            $current_salary = $py->karyawan->karyawan_salary;
            $payroll_status = $py->payroll_status;
            $karyawan_wajibpajak_id = $py->ms_wajibpajak_id;
            if($calculation_period) {
                $current_period = date('Y-m', strtotime($calculation_period));
                $current_month = date('n', strtotime($calculation_period));
            } else {
                $current_period = date('Y-m', strtotime($py->payroll_period));
                $current_month = date('n', strtotime($py->payroll_period));
            }
            
            // dd($current_month);
            $isnpwp = ($py->karyawan->karyawan_npwp == '00.000.000.0-000.000') ? 'NO-NPWP' : 'NPWP';
            $bpjskes = 0;
            $bpjstk = 0;
            if(date('Y-m', strtotime($py->karyawan->karyawan_bpjskesdate)) <= $current_period) {
                $bpjskes = ($py->karyawan->karyawan_isbpjskes && count($bpjs_karyawan_decode) > 0) ? 1 : 0; // this should check in st_bpjs_karyawan if provide or not.
            }
            if(date('Y-m', strtotime($py->karyawan->karyawan_bpjstkdate)) <= $current_period) {
                $bpjstk = ($py->karyawan->karyawan_isbpjstk && count($bpjs_karyawan_decode) > 0) ? 1 : 0; // this should check in st_bpjs_karyawan if provide or not.
            }
            
            $isbpjs = [$bpjskes, $bpjstk];
            // $ptkp = ($py->pph21->pph21_ptkp_data) ? json_decode($py->pph21->pph21_ptkp_data) : null;
            $ptkp = $py->karyawan->ptkp;
            
            // dd($py->karyawan);
            $tjkaryawan = $py->karyawan->tunjangandetail;
            // if($py->ms_karyawan_id == 122)
            //     dd($tjkaryawan, $py->payroll_allowance_setting);
            
            $tjkaryawan_ids = [];
            foreach($tjkaryawan as $tjk) {
                array_push($tjkaryawan_ids, $tjk->sttunjangankaryawan_id);
            }
            // dd($karyawan->sttunjangantbl);
            $potongan_nominal = 0;
            $potongan_nominal_pph21 = 0;
            $potongan_karyawan_data = [];
            $tunjangan_nominal = 0;
            $tunjangan_nominal_pph21 = 0;
            $tunjangan_karyawan_data = [];
            $custom_tunjangan_karyawan_data = [];
            $custom_pengurangan_karyawan_data = [];
            // dd($tjkaryawan_ids);
            // $stpenggajian_karyawan = ($karyawan->stpenggajiantbl) ? json_decode($karyawan->stpenggajiantbl) : null;
            $stpenggajian_karyawan = $py->karyawan->penggajian;
            // dd($stpenggajian_karyawan);
            // Look up to st_attendance
            $stattendance = $py->karyawan->attendance;
            $attendance_days = ($stattendance) ? json_decode($stattendance->attendance_working_day) : [];
            // $prorate_salary = $this->calculate_prorate_salary($karyawan, $stpenggajian_karyawan, $attendance_days);
            $appPayslipLib = new AppPayslipLibrary();
            $appPayslipLib->payroll = $py;
            $appPayslipLib->karyawan = $py->karyawan;
            $appPayslipLib->stpenggajian_karyawan = $stpenggajian_karyawan;
            $appPayslipLib->attendance_days = $attendance_days;
            $appPayslipLib->holiday_karyawan = $holiday_karyawan;
            $prorate_salary = $appPayslipLib->calculate_prorate_salary();
            // if($py->ms_karyawan_id == 122)
                // dd($prorate_salary);
            // $total_days = $prorate_salary['total_days'];
            $absence_days = $prorate_salary['absence_days'];
            // $working_days = $prorate_salary['working_days'];
            $late_times = $prorate_salary['late_times'];
            $total_lates = $prorate_salary['total_lates'];
            $current_salary = $prorate_salary['prorate_salary'];
            // if($karyawan->karyawan_id == 122)
            //     dd($karyawan->st_potongan_id);
                
            // Potongan
            $stpotongan_karyawan = $py->karyawan->potongandetail;
            // dd($stpotongan_karyawan);
            if(count($stpotongan_karyawan) > 0) {
                // dd($stpotongan_karyawan);
                foreach($stpotongan_karyawan as $ptkaryawandet) {
                    // dd($ptkaryawan);
                    $ptkaryawan = $ptkaryawandet->stpotongan;
                    $ptkaryawan->stgrouppotongankaryawan_id = $ptkaryawan->stgrouppotongan->stgrouppotongankaryawan_id;
                    $ptkaryawan->stgrouppotongankaryawan_name = $ptkaryawan->stgrouppotongan->stgrouppotongankaryawan_name;
                    if($ptkaryawan->stpotongankaryawan_type == 'TETAP') {
                        if($ptkaryawan->stpotongankaryawan_method == 'BULAN') {
                            $ptkaryawan->stpotongankaryawan_accumulate_value = $ptkaryawan->stpotongankaryawan_value;
                            $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                            if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                                $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                            }

                            array_push($potongan_karyawan_data, $ptkaryawan);
                        }

                        else if($ptkaryawan->stpotongankaryawan_method == 'HARI') {
                            $ptkaryawan->stpotongankaryawan_accumulate_value = $prorate_salary['absence_days'] * $ptkaryawan->stpotongankaryawan_value;
                            $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                            if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                                $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                            }
                            array_push($potongan_karyawan_data, $ptkaryawan);
                        }
                    } 
                    else if($ptkaryawan->stpotongankaryawan_type == 'ABSEN') {
                        if($ptkaryawan->stpotongankaryawan_method == 'TETAP') {
                            $ptkaryawan->stpotongankaryawan_accumulate_value = $prorate_salary['absence_days'] * $ptkaryawan->stpotongankaryawan_value;
                            $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                            if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                                $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                            }
                            array_push($potongan_karyawan_data, $ptkaryawan);
                        }

                        else if($ptkaryawan->stpotongankaryawan_method == 'PRORATA') {
                            $formulapotongan_arr = ($ptkaryawan->stpotongankaryawan_formula) ? explode(',', $ptkaryawan->stpotongankaryawan_formula) : [];

                            $appPayslipLib->data = [
                                'payroll' => $py,
                                'karyawan' => $py->karyawan,
                                'sttunjangan' => $py->karyawan->sttunjangantbl,
                                'current_salary' => $current_salary,
                                'stpenggajian_karyawan' => $stpenggajian_karyawan,
                                'wajibpajak_id' => $karyawan_wajibpajak_id,
                                'attendance_days' => $attendance_days,
                                'current_month' => $current_month,
                                'tjkaryawan_ids' => $tjkaryawan_ids,
                                'grouptunjangan_ids' => $formulapotongan_arr,
                                'prorate_salary' => $prorate_salary,
                                'absence_days' => $absence_days
                            ];
                            $calculate_tunjangan = $appPayslipLib->calculate_tunjangan();
                            // dd($calculate_tunjangan);

                            // dd($prorate_salary, $calculate_tunjangan);
                            if(count($calculate_tunjangan['tunjangan_karyawan_data']) > 0) {
                                $stpotongankaryawan_accumulate_value = 0;
                                foreach($calculate_tunjangan['tunjangan_karyawan_data'] as $tdt) {
                                    $stpotongankaryawan_accumulate_value += $tdt->sttunjangankaryawan_accumulate_value;
                                    $potongan_nominal += $stpotongankaryawan_accumulate_value;
                                    if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                                        $potongan_nominal_pph21 += $stpotongankaryawan_accumulate_value;
                                    }
                                }
                                $ptkaryawan->stpotongankaryawan_accumulate_value = $stpotongankaryawan_accumulate_value;
                                array_push($potongan_karyawan_data, $ptkaryawan);
                            }
                        }
                    }
                    else if($ptkaryawan->stpotongankaryawan_type == 'TELAT') {
                        // dd($ptkaryawan);
                        if($ptkaryawan->stpotongankaryawan_method == 'HARI') {
                            $ptkaryawan->stpotongankaryawan_accumulate_value = $ptkaryawan->stpotongankaryawan_value * count($late_times);
                            $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                            if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                                $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                            }
                            array_push($potongan_karyawan_data, $ptkaryawan);

                        } else if($ptkaryawan->stpotongankaryawan_method == 'BULAN') {
                            $ptkaryawan->stpotongankaryawan_accumulate_value = count($late_times) > 0 ? $ptkaryawan->stpotongankaryawan_value : 0;
                            $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                            if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                                $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                            }
                            array_push($potongan_karyawan_data, $ptkaryawan);
                        } else if($ptkaryawan->stpotongankaryawan_method == 'KELIPATAN_HARI') {
                            if(count($late_times) > 0) {
                                $stpotongankaryawan_accumulate_value = 0;
                                foreach($late_times as $key => $val) {
                                    $lttimes = AppDurationLibrary::fromString($val.':00');
                                    $ltminutes = $lttimes->toMinutes();

                                    if($ltminutes >= $ptkaryawan->stpotongankaryawan_accumulationtime) {
                                        $diffminutes = floor($ltminutes / $ptkaryawan->stpotongankaryawan_accumulationtime);
                                        $stpotongankaryawan_accumulate_value += $ptkaryawan->stpotongankaryawan_value * $diffminutes;
                                    }
                                }

                                $ptkaryawan->stpotongankaryawan_accumulate_value = $stpotongankaryawan_accumulate_value;
        
                                $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                                if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                                    $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                                }
                                
                            } else {
                                $ptkaryawan->stpotongankaryawan_accumulate_value = 0;
                                $ptkaryawan->stpotongankaryawan_value = 0;
                            }
                            array_push($potongan_karyawan_data, $ptkaryawan);
                            
                        } else if($ptkaryawan->stpotongankaryawan_method == 'KELIPATAN_BULAN') {
                            if(count($total_lates) > 0) {
                                $lttimes = AppDurationLibrary::fromString($total_lates[0].':'.$total_lates[1].':'.$total_lates[2]);
                                $ltminutes = $lttimes->toMinutes();
                                if($ltminutes >= $ptkaryawan->stpotongankaryawan_accumulationtime) {
                                    $diffminutes = floor($ltminutes / $ptkaryawan->stpotongankaryawan_accumulationtime);
                                    $ptkaryawan->stpotongankaryawan_accumulate_value = $ptkaryawan->stpotongankaryawan_value * $diffminutes;

                                    $potongan_nominal += $ptkaryawan->stpotongankaryawan_accumulate_value;
                                    if($ptkaryawan->stpotongankaryawan_taxable == '1') {
                                        $potongan_nominal_pph21 += $ptkaryawan->stpotongankaryawan_accumulate_value;
                                    }
                                    array_push($potongan_karyawan_data, $ptkaryawan);
                                }
                            } else {
                                $ptkaryawan->stpotongankaryawan_accumulate_value = 0;
                                $ptkaryawan->stpotongankaryawan_value = 0;
                            }
                            array_push($potongan_karyawan_data, $ptkaryawan);
                        }
                    }
                }
            }
            // if($py->ms_karyawan_id == 122)
            //     dd($py->karyawan);
            // Tunjangan
            if(count($tjkaryawan_ids) > 0) {
                $appPayslipLib->data = [
                    'payroll' => $py,
                    'karyawan' => $py->karyawan,
                    'sttunjangan' => $py->karyawan->sttunjangantbl,
                    'current_salary' => $current_salary,
                    'stpenggajian_karyawan' => $stpenggajian_karyawan,
                    'wajibpajak_id' => $karyawan_wajibpajak_id,
                    'attendance_days' => $attendance_days,
                    'current_month' => $current_month,
                    'tjkaryawan_ids' => $tjkaryawan_ids,
                ];
                $calculate_tunjangan = $appPayslipLib->calculate_tunjangan();
                // if($py->ms_karyawan_id == 122)
                //     dd($calculate_tunjangan);
                $tunjangan_nominal += $calculate_tunjangan['tunjangan_nominal'];
                $tunjangan_nominal_pph21 += $calculate_tunjangan['tunjangan_nominal_pph21'];
                $tunjangan_karyawan_data = array_merge($tunjangan_karyawan_data, $calculate_tunjangan['tunjangan_karyawan_data']);
                
            }
            if($custom_allowance_data) {
                foreach($custom_allowance_data as $cadata) {
                    $tunjangan_nominal += (isset($cadata['nominal'])) ? intval($cadata['nominal']) : 0;
                    $tunjangan_nominal_pph21 += (isset($cadata['taxable']) && $cadata['taxable'] == 1) ? intval($cadata['nominal']) : 0;
                    array_push($custom_tunjangan_karyawan_data, $cadata);
                }
            }

            if($custom_deduction_data) {
                foreach($custom_deduction_data as $cddata) {
                    $potongan_nominal += (isset($cddata['nominal'])) ? intval($cddata['nominal']) : 0;
                    $potongan_nominal_pph21 += (isset($cddata['taxable']) && $cddata['taxable'] == 1) ? intval($cddata['nominal']) : 0;
                    array_push($custom_pengurangan_karyawan_data, $cddata);
                }
            }
            // if($karyawan->karyawan_id == 122)
                // dd($calculate_tunjangan);

            $sttunjanganbpjskes = [];
            $sttunjanganbpjskespph21 = [];
            $sttunjanganbpjstk = [];
            $sttunjanganbpjstkpph21 = [];
            
            if(count($tunjangan_karyawan_data) > 0) {
                foreach($tunjangan_karyawan_data as $tjdatakaryawan) {
                    if(in_array($tjdatakaryawan->st_grouptunjangankaryawan_id, $bpjs_tk_gruptunjangan)) {
                        array_push($sttunjanganbpjstk, $tjdatakaryawan);
                        if($tjdatakaryawan->sttunjangankaryawan_taxable == 1) {
                            array_push($sttunjanganbpjstkpph21, $tjdatakaryawan);
                        }
                    }
                    if(in_array($tjdatakaryawan->st_grouptunjangankaryawan_id, $bpjs_kes_gruptunjangan)) {
                        array_push($sttunjanganbpjskes, $tjdatakaryawan);
                        if($tjdatakaryawan->sttunjangankaryawan_taxable == 1) {
                            array_push($sttunjanganbpjskespph21, $tjdatakaryawan);
                        }
                    }
                }
            }
            // if($py->ms_karyawan_id == 122)
                // dd($tunjangan_karyawan_data);
            //     dd($sttunjanganbpjstkpph21,$sttunjanganbpjskespph21);
            $bpjskes_accumulate_total = ($bpjs_kes_gp) ? $current_salary : 0;
            if(count($sttunjanganbpjskes) > 0) {
                $bpjskes_accumulate_total = $current_salary;
                if(($bpjs_kes_lainnya == null)) {
                    
                    if($tjkaryawan_ids) {
                        foreach($sttunjanganbpjskes as $tjkes) {
                            if(in_array($tjkes->sttunjangankaryawan_id, $tjkaryawan_ids)) {
                                $bpjskes_accumulate_total += $tjkes->sttunjangankaryawan_accumulate_value;
                            }
                        }
                    }
                } else {
                    $bpjskes_accumulate_total = $bpjs_kes_lainnya;
                }
            }

            // dd($sttunjanganbpjskes);
            $bpjstk_accumulate_total = ($bpjs_tk_gp) ? $current_salary : 0;
            if(count($sttunjanganbpjstk) > 0) {
                $bpjstk_accumulate_total = $current_salary;
                if($bpjs_tk_lainnya == null) {
                    
                        if($tjkaryawan_ids) {
                            foreach($sttunjanganbpjstk as $tjtk) {
                                if(in_array($tjtk->sttunjangankaryawan_id, $tjkaryawan_ids)) {
                                    $bpjstk_accumulate_total += $tjtk->sttunjangankaryawan_accumulate_value;
                                }
                            }
                        }
                } else {
                    $bpjstk_accumulate_total = $bpjs_tk_lainnya;
                }
                
            }
            
            $bpjstk_jp_accumulate_total = $bpjstk_accumulate_total;
            if(count($setting) > 0) {
                foreach($setting as $st) {
                    if($st->setting_key == 'BPJS_TK_MAX_AMOUNT') {
                        $bpjstkval = intval($st->setting_value);
                        if($bpjstk_jp_accumulate_total > $bpjstkval) {
                            $bpjstk_jp_accumulate_total = $bpjstkval;
                        }
                    }
                    if($st->setting_key == 'BPJS_KES_MAX_AMOUNT') {
                        $bpjskesval = intval($st->setting_value);
                        if($bpjskes_accumulate_total > $bpjskesval) {
                            $bpjskes_accumulate_total = $bpjskesval;
                        }
                    }
                }
            }
            // dd($bpjs_kes_gp, $bpjs_tk_gp, $bpjs_karyawan_decode);
            // dd($bpjstk_jp_accumulate_total);
            // dd($tunjangan_nominal_pph21,$potongan_nominal_pph21);
            
            $calculate_pph21 = $appPph21Lib->calculateTotal($bpjsrate, $ptkp, [$bpjskes_accumulate_total, $bpjstk_accumulate_total, $bpjstk_jp_accumulate_total], $current_salary, [
                'tunjangan_nominal' => $tunjangan_nominal_pph21, 
                'tunjangan_jabatan' => $tunjangan_jabatan,
                'potongan_nominal' => $potongan_nominal_pph21,
            ], $method, $isnpwp, $jkkrate, $isbpjs);
            // if($py->ms_karyawan_id == 122)
            //     dd($calculate_pph21);
            $biaya_jamkes_payslip = 0;
            $biaya_jht = $calculate_pph21['biaya_jht'];
            $biaya_jp = $calculate_pph21['biaya_jp'];
            $biaya_jamkes_payslip_rate = $appPph21Lib->getBpjsRate($bpjsrate,'JamKesMin', true);
            $calculate_pph21['biaya_jamkes_payslip_rate'] = ($biaya_jamkes_payslip_rate) ? $biaya_jamkes_payslip_rate->bpjsrate_rate : 0;
            // dd($isbpjs);
            if($isbpjs[0] == 1) { // bpjs kesehatan
                $biaya_jamkes_payslip = $appPph21Lib->getTotalBpjsRate($bpjsrate,'JamKesMin', $bpjskes_accumulate_total, null, true);
                $calculate_pph21['biaya_jamkes_payslip'] = floor($biaya_jamkes_payslip);
                
            } else {
                $calculate_pph21['biaya_jamkes_payslip'] = 0;
            }
    
            $penghasilan_jht_payslip = 0;
            $penghasilan_jp_payslip = 0;
            $penghasilan_jkk = $calculate_pph21['penghasilan_jkk'];
            $penghasilan_jamkes = $calculate_pph21['penghasilan_jamkes'];
            $penghasilan_jkm = $calculate_pph21['penghasilan_jkm'];

            $penghasilan_jht_payslip_rate = $appPph21Lib->getBpjsRate($bpjsrate,'JHT', true);
            $penghasilan_jp_payslip_rate = $appPph21Lib->getBpjsRate($bpjsrate,'JP', true);
            $calculate_pph21['penghasilan_jht_payslip_rate'] = ($penghasilan_jht_payslip_rate) ? $penghasilan_jht_payslip_rate->bpjsrate_rate : 0;
            $calculate_pph21['penghasilan_jp_payslip_rate'] = ($penghasilan_jp_payslip_rate) ? $penghasilan_jp_payslip_rate->bpjsrate_rate : 0;

            if($isbpjs[1] == 1) { // bpjs tk
                $penghasilan_jht_payslip = $appPph21Lib->getTotalBpjsRate($bpjsrate,'JHT', $bpjstk_accumulate_total, null, true);
                $penghasilan_jp_payslip = $appPph21Lib->getTotalBpjsRate($bpjsrate,'JP', $bpjstk_jp_accumulate_total, null, true);

                $calculate_pph21['penghasilan_jht_payslip'] = floor($penghasilan_jht_payslip);
                $calculate_pph21['penghasilan_jp_payslip'] = floor($penghasilan_jp_payslip);
            } else {
                $calculate_pph21['penghasilan_jht_payslip'] = 0;
                $calculate_pph21['penghasilan_jp_payslip'] = 0;
            }
            
            $allowance_pph21 = ($method == 'GROSS') ? 0 : $calculate_pph21['total_pph_terutang_perbulan'];
            // if($py->ms_karyawan_id == 122)
            //     dd($penghasilan_jamkes);
            if($bpjs_kes_ditanggung) {
                $allowance_bpjskes = $penghasilan_jamkes + $biaya_jamkes_payslip;
                $biaya_jamkes_payslip = 0;
                // dd($allowance_bpjskes);
            } else {
                // dd($penghasilan_jamkes);
                $allowance_bpjskes = $penghasilan_jamkes;
                // $allowance_bpjskes = 0;
            }
            
            if($bpjs_tk_ditanggung) {
                $allowance_bpjstk = $penghasilan_jkk + $penghasilan_jkm + $penghasilan_jht_payslip + $penghasilan_jp_payslip + $biaya_jht + $biaya_jp;
                $penghasilan_jht_payslip = 0;
                $penghasilan_jp_payslip = 0;
                $biaya_jht = 0;
                $biaya_jp = 0;
            } else {
                $allowance_bpjstk = $penghasilan_jkk + $penghasilan_jkm + $penghasilan_jht_payslip + $penghasilan_jp_payslip;
                // $allowance_bpjstk = 0;
            }
            // dd($allowance_bpjstk);
            
            $income = $current_salary + $allowance_pph21 + $allowance_bpjskes + $allowance_bpjstk + $tunjangan_nominal;
            // dd($biaya_jamkes_payslip);
            $deduction_pph21 = $calculate_pph21['total_pph_terutang_perbulan'];
            $deduction_bpjskes = $biaya_jamkes_payslip;
            $deduction_bpjstk = $biaya_jht + $biaya_jp;
            $allowance_bpjskes_ditanggung = 0;
            $allowance_bpjstk_ditanggung = 0;
            if($bpjs_kes_ditanggung) {
                $allowance_bpjskes_ditanggung = $allowance_bpjskes;
            }
            if($bpjs_tk_ditanggung) {
                $allowance_bpjstk_ditanggung = $allowance_bpjstk;
            }
            $outcome = $deduction_pph21 + $deduction_bpjskes + $deduction_bpjstk + $allowance_bpjskes + $allowance_bpjstk + $potongan_nominal;
            // $outcome = $deduction_pph21 + $deduction_bpjskes + $deduction_bpjstk + $allowance_bpjskes_ditanggung + $allowance_bpjstk_ditanggung + $potongan_nominal;
            // dd($deduction_pph21 , $deduction_bpjskes , $deduction_bpjstk , $allowance_bpjskes_ditanggung , $allowance_bpjstk_ditanggung , $potongan_nominal);
            $payroll = [
                'pph21' => $calculate_pph21,
                'payroll' => [
                    'gaji' => floor($current_salary),
                    'income' => floor($income),
                    'outcome' => floor($outcome),
                    'netto' => floor($income - $outcome),
                    'allowance_pph21' => floor($allowance_pph21),
                    'allowance_bpjskes' => floor($allowance_bpjskes),
                    'allowance_bpjstk' => floor($allowance_bpjstk),
                    'deduction_pph21' => floor($deduction_pph21),
                    'deduction_bpjskes' => floor($deduction_bpjskes),
                    'deduction_bpjstk' => floor($deduction_bpjstk),
                ]
            ];
            // dd($payroll);
            // dd($custom_pengurangan_karyawan_data);
            // dd($potongan_karyawan_data);
            $temp_data = [
                'payroll_uuid' => ($py->payroll_uuid) ? $py->payroll_uuid : 'RK-'.$karyawan_wajibpajak_id.'-'.Uuid::uuid4()->toString(),
                'ms_user_id' => $py->ms_user_id,
                'ms_wajibpajak_id' => $karyawan_wajibpajak_id,
                'ms_karyawan_id' => $py->ms_karyawan_id,
                'payroll_period' => $py->payroll_period,
                'payroll_total_netto' => $payroll['payroll']['netto'],
                'payroll_total_income' => $payroll['payroll']['income'],
                'payroll_total_outcome' => $payroll['payroll']['outcome'],
                'payroll_basic_salary' => $py->karyawan->karyawan_salary,
                'payroll_prorate_salary' => floor($current_salary),
                'payroll_allowance_pph21' => $payroll['payroll']['allowance_pph21'],
                'payroll_allowance_bpjskes' => $payroll['payroll']['allowance_bpjskes'],
                'payroll_allowance_bpjstk' => $payroll['payroll']['allowance_bpjstk'],
                
                'payroll_deduction_pph21' => $payroll['payroll']['deduction_pph21'],
                'payroll_deduction_bpjskes' => $payroll['payroll']['deduction_bpjskes'],
                'payroll_deduction_bpjstk' => $payroll['payroll']['deduction_bpjstk'],
                
                'payroll_status' => $payroll_status,
                // 'payroll_pph21' => json_encode($payroll['pph21']),
                'payroll_bpjssetting' => ($stbpjs_karyawan) ? $stbpjs_karyawan->stbpjskaryawan_value : null,
                'payroll_bpjskes_paidbycompany' => ($bpjs_kes_ditanggung) ? 1 : 0,
                'payroll_bpjstk_paidbycompany' => ($bpjs_tk_ditanggung) ? 1 : 0,
                // 'payroll_ptkpsetting' => $karyawan->ptkptbl,
                'payroll_prorate_data' => json_encode($prorate_salary),
                'payroll_allowance_setting' => ($tunjangan_karyawan_data) ? json_encode($tunjangan_karyawan_data) : null,
                'payroll_allowance_addition' => ($custom_tunjangan_karyawan_data) ? json_encode($custom_tunjangan_karyawan_data) : null,
                'payroll_deduction_setting' => ($potongan_karyawan_data) ? json_encode($potongan_karyawan_data) : null,
                'payroll_deduction_addition' => ($custom_pengurangan_karyawan_data) ? json_encode($custom_pengurangan_karyawan_data) : null,
                'payroll_attendancesetting' => $py->payroll_attendancesetting,
                'payroll_penggajiansetting' => json_encode($stpenggajian_karyawan),
                'payroll_method' => $method,
                'payroll_karyawan_enid' => $py->karyawan->karyawan_enid,
                'payroll_karyawan_name' => $py->karyawan->karyawan_name,
                'payroll_karyawan_address' => $py->karyawan->karyawan_address,
                'payroll_karyawan_nik' => $py->karyawan->karyawan_nik,
                'payroll_karyawan_npwp' => ($py->karyawan->karyawan_npwp) ? $py->karyawan->karyawan_npwp : $py->payroll_karyawan_npwp,
                'payroll_karyawan_gender' => $py->karyawan->karyawan_gender,
                'payroll_karyawan_status' => $py->karyawan->karyawan_status,
                'ms_divisi_id' => $py->karyawan->ms_divisi_id,
                'payroll_karyawandivisi_name' => ($py->karyawan->ms_divisi_id) ? $py->divisi->karyawandivisi_name : null,
                'ms_jabatan_id' => $py->karyawan->ms_jabatan_id,
                'payroll_karyawanjabatan_name' => ($py->karyawan->ms_jabatan_id) ? $py->karyawan->jabatan->karyawanjabatan_name : null,
                'payroll_paid_option' => $stpenggajian_karyawan->stpenggajiankaryawan_weekendoption,
                'payroll_autoemailslip' => $stpenggajian_karyawan->stpenggajiankaryawan_autoemailpayslip,
            ];

            $temp_data_pph21 = [
                'tr_payroll_uuid' => $temp_data['payroll_uuid'],
                'ms_user_id' => $temp_data['ms_user_id'],
                'ms_wajibpajak_id' => $temp_data['ms_wajibpajak_id'],
                'ms_karyawan_id' => $temp_data['ms_karyawan_id'],
                'ms_ptkp_id' => $py->karyawan->ms_ptkp_id,
                'pph21_ptkp_description' => $py->karyawan->ptkp->ptkp_description,
                'pph21_ptkp_rate' => $py->karyawan->ptkp->ptkp_rate,
                'pph21_method' => $temp_data['payroll_method'],
                'pph21_period' => $temp_data['payroll_period'],
                'pph21_basic_salary' => $temp_data['payroll_basic_salary'],
                'pph21_prorate_salary' => $temp_data['payroll_prorate_salary'],

                'pph21_deduction_other' => $calculate_pph21['nominal_potongan_lain'],
                'pph21_deduction_position' => $calculate_pph21['total_biaya_jabatan'],
                'pph21_deduction_jamkes' => $calculate_pph21['biaya_jamkes'],
                'pph21_deduction_jamkes_payslip' => $calculate_pph21['biaya_jamkes_payslip'],
                'pph21_deduction_jht' => $calculate_pph21['biaya_jht'],
                'pph21_deduction_jp' => $calculate_pph21['biaya_jp'],

                'pph21_deduction_jamkes_rate' => $calculate_pph21['biaya_jamkes_rate'],
                'pph21_deduction_jamkes_payslip_rate' => $calculate_pph21['biaya_jamkes_payslip_rate'],
                'pph21_deduction_jht_rate' => $calculate_pph21['biaya_jht_rate'],
                'pph21_deduction_jp_rate' => $calculate_pph21['biaya_jp_rate'],

                'pph21_allowance_other' => $calculate_pph21['nominal_tunjangan_lain'],
                'pph21_allowance_jamkes' => $calculate_pph21['penghasilan_jamkes'],
                'pph21_allowance_jkk' => $calculate_pph21['penghasilan_jkk'],
                'pph21_allowance_jkm' => $calculate_pph21['penghasilan_jkm'],
                'pph21_allowance_jht' => $calculate_pph21['penghasilan_jht'],
                'pph21_allowance_jht_payslip' => $calculate_pph21['penghasilan_jht_payslip'],
                'pph21_allowance_jp' => $calculate_pph21['penghasilan_jp'],
                'pph21_allowance_jp_payslip' => $calculate_pph21['penghasilan_jp_payslip'],
                
                'pph21_allowance_jamkes_rate' => $calculate_pph21['penghasilan_jamkes_rate'],
                'pph21_allowance_jkk_rate' => $calculate_pph21['penghasilan_jkk_rate'],
                'pph21_allowance_jkm_rate' => $calculate_pph21['penghasilan_jkm_rate'],
                'pph21_allowance_jht_rate' => $calculate_pph21['penghasilan_jht_rate'],
                'pph21_allowance_jht_payslip_rate' => $calculate_pph21['penghasilan_jht_payslip_rate'],
                'pph21_allowance_jp_rate' => $calculate_pph21['penghasilan_jp_rate'],
                'pph21_allowance_jp_payslip_rate' => $calculate_pph21['penghasilan_jp_payslip_rate'],
                
                'pph21_pkp' => $calculate_pph21['total_pkp'],
                'pph21_ptkp' => $calculate_pph21['total_ptkp'],
                
                'pph21_bruto_month' => $calculate_pph21['total_bruto_perbulan'],
                'pph21_bruto_year' => $calculate_pph21['total_bruto_pertahun'],
                'pph21_netto_month' => $calculate_pph21['total_neto_perbulan'],
                'pph21_netto_year' => $calculate_pph21['total_neto_pertahun'],
                'pph21_total_month' => $calculate_pph21['total_pph_terutang_perbulan'],
                'pph21_total_year' => $calculate_pph21['total_pph_terutang_setahun'],

                'pph21_ptkp_data' => json_encode($ptkp),
            ];

            array_push($save_data_payroll, $temp_data);
            array_push($save_data_pph21, $temp_data_pph21);
        }
        return ['data_payroll' => $save_data_payroll, 'data_pph21' => $save_data_pph21];
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