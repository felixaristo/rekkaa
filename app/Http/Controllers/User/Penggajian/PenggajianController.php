<?php
namespace App\Http\Controllers\User\Penggajian;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Libraries\AppDurationLibrary;
use App\Libraries\AppPayslipLibrary;
use App\Libraries\AppPPh21Library;
use App\Model\Master\BpjsRateModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PtkpDetailModel;
use App\Model\Master\PtkpModel;
use App\Model\Master\Tarif21Model;
use App\Model\Master\TarifNonNpwpModel;
use App\Model\Master\TunjanganJabatanModel;
use App\Model\Mview\VwKaryawanPayrollModel;
use App\Model\Setting\SettingAttendanceModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingHolidayModel;
use App\Model\Setting\SettingModel;
use App\Model\Setting\SettingPajakPph21Model;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use App\Model\Setting\SettingPotonganKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use App\Model\Transaction\AttendanceKaryawanModel;
use App\Model\Transaction\HistoryImportModel;
use App\Model\Transaction\LemburKaryawanModel;
use App\Model\Transaction\NotificationModel;
use App\Model\Transaction\PayrollModel;
use App\Model\Transaction\PPh21Model;
use App\User;
use Carbon\Carbon;
use DateTime;
use DOMDocument;
use Error;
use Exception;
use Faker\Generator;
use Generator as GlobalGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Spatie\Activitylog\Models\Activity;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\DataType as PhpSpreadsheetDataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpSpreadsheetDate;

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
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $user_id = session()->get('user_data')['user_id'];
        $periode = date('m-Y');
        $user = User::with(['wajibpajak.wpstpenggajian'])->where([
            'user_id' => $user_id
        ])->first();
        $data = [
            'title' => 'Penggajian',
            'content' => 'user.penggajian.karyawan.index',
            'user' => $user,
            'stpenggajian' => SettingPenggajianKaryawanModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'stpenggajiankaryawan_active' => 1])->first(),
        ];

        // $request->request->add(['payroll_period' => date('m-Y')]);
        // $this->calculatenew($request);

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

        $payroll_periodmy = Carbon::parse($payroll->payroll_period)->format('m-Y');;
        $setting = SettingModel::whereIn('setting_key', ['BPJS_TK_MAX_AMOUNT','BPJS_KES_MAX_AMOUNT'])
        ->where(['setting_active' => '1'])->get();

        $bpjstk_jp_max = 0;
        $bpjskes_max = 0;
        if(count($setting) > 0) {
            foreach($setting as $st) {
                if($st->setting_key == 'BPJS_TK_MAX_AMOUNT') {
                    $bpjstkval = 0;
                    // $bpjstkval = intval($st->setting_value);
                    $bpjs_tk_setting = json_decode($st->setting_value);

                    usort($bpjs_tk_setting, function($a, $b) {
                        return $b->period <=> $a->period;
                    });
                    // dd($this->payroll_period);
                    // dd($bpjs_tk_setting, $payroll_periodmy);
                    foreach($bpjs_tk_setting as $sttk) {
                        if(strtotime('01-'.$payroll_periodmy) >= strtotime('01-'.$sttk->period)) {
                            $bpjstkval = $sttk->value;

                            break;
                        }
                    }
                    
                    $bpjstk_jp_max = $bpjstkval;
                }
                if($st->setting_key == 'BPJS_KES_MAX_AMOUNT') {
                    $bpjskesval = intval($st->setting_value);
                    $bpjskes_max = $bpjskesval;
                }
            }
        }

        // dd($payroll);
        // dd($payroll->karyawan->jabatan->karyawanjabatan_name);
        // dd($payroll->lembur);
        $lembur = [
            'jam' => 0,
            // 'nominal' => 0,
            'total' => 0,
        ];
        foreach($payroll->lembur as $lb) {
            $lembur['jam'] += $lb->lemburkaryawan_total_time;
            // $lembur['nominal'] += $lb->lemburkaryawan_overtime_amount;
            $lembur['total'] += $lb->lemburkaryawan_total_overtime_amount;
        }
        // dd($lembur);
        $data = [
            'title' => 'Penggajian',
            'max_bpjs' => ['KES' => $bpjskes_max, 'TK' => $bpjstk_jp_max],
            'payroll' => $payroll,
            'lembur' => $lembur,
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
        
        $payroll_period = Carbon::parse(date('d').'-'.$periode);
        $payroll_periodm = $payroll_period->format('m');
        $payroll_periody = $payroll_period->format('Y');
        
        if($payroll_periodm == 12) {
            for($ip = 1; $ip <= 12; $ip++) {
                $payroll_period = Carbon::parse(date('d').'-'.$periode);
                $payroll_periodm = $payroll_period->format('m');
                $iper = ($ip > 9) ? $ip.'-'.$payroll_periody : '0'.$ip.'-'.$payroll_periody;
                $request->request->add(['payroll_period' => $iper]);
                $this->calculatenew($request);
            }
        } else {
            $request->request->add(['payroll_period' => $periode]);
            $this->calculatenew($request);
        }
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
		$data['recordsTotal'] = PayrollModel::when($karyawan_ids, function($q, $karyawan_ids) {
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
        $payroll_period = ($request->get('payroll_period')) ? Carbon::parse(date('d').'-'.$request->get('payroll_period')) : date('d-m-Y');
        $payroll_periodym = $payroll_period->format('Y-m');
        $check_all = $request->post('check_all');
        $stpajkpph21 = SettingPajakPph21Model::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$stpajkpph21) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Pajak PPh21 tidak ditemukan. Silahkan lakukan pengaturan terlebih dahulu!'
            ]);
        }

        $payrolls = PayrollModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id, 
            'payroll_status' => 1, 
            'payroll_lock' => 0
        ])
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("TO_CHAR(payroll_period, 'YYYY-MM') = ?", [$payroll_periodym]);

        if($check_all != 1) {
            $payrolls->whereIn('payroll_uuid', $payroll_uuids);
        }
        $payrolls = $payrolls->get();
        
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
                Pph21Model::whereIn('tr_payroll_uuid', $uuids)
                ->update(['pph21_pajakpph21_data' => ($stpajkpph21) ? json_encode($stpajkpph21) : null,]);
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
        $check_all = $request->post('check_all');
        $payroll_period = ($request->get('payroll_period')) ? Carbon::parse(date('d').'-'.$request->get('payroll_period')) : date('d-m-Y');
        $payroll_periodym = $payroll_period->format('Y-m');
        $payrolls = PayrollModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id, 
            'payroll_status' => 2, 
            'payroll_lock' => 1
        ])
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("TO_CHAR(payroll_period, 'YYYY-MM') = ?", [$payroll_periodym]);
        if($check_all != 1) {
            $payrolls->whereIn('payroll_uuid', $payroll_uuids);
        }
        $payrolls = $payrolls->get();
        // dd(count($payrolls));
        // dd($payroll_period);
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
        // dd(count($uuids), count($payrolls));
        DB::beginTransaction();
        try {
            if(count($uuids) > 0) {
                foreach($uuids as $date => $uuid) {
                    PayrollModel::whereIn('payroll_uuid', $uuid)
                    ->update(['payroll_status' => 3, 'payroll_paid_at' => date('Y-m-d H:i:s')]);
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
                'notification_text' => 'EMAIL_PAYSLIP',
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

            $notifications = NotificationModel::where(['notification_text' => 'EMAIL_PAYSLIP', 'ms_wajibpajak_id' => $wajibpajak_id, 'notification_status' => 'PENDING'])->get();
            // dd($notifications);
            foreach($notifications as $notification) {
                dispatch(new SendMailJob($notification->notification_id));
            }
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
    public function calculatenew(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $user_id = session()->get('user_data')['user_id'];
        $payroll_period = Carbon::parse(date('d').'-'.$request->input('payroll_period'));
        $user = User::with(['wajibpajak.wpstpenggajian'])->where([
            'user_id' => $user_id
        ])->first();
        $payroll_periody = $payroll_period->format('Y');
        $payroll_periodmy = $payroll_period->format('m-Y');
        $payroll_periodym = $payroll_period->format('Y-m');

        $joindate = Carbon::parse($user->user_created_at);
        // dd($payroll_uuids);
        if($payroll_periody < $joindate->format('Y')) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun tidak boleh kurang dari tahun pendaftaran!',
            ]);
        }
        if($payroll_periody > date('Y')) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun tidak boleh melebihi dari tahun sekarang!',
            ]);
        }

        $karyawans = KaryawanModel::with(['payroll' => function($q) use($payroll_periodmy) {
            return $q->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$payroll_periodmy]);
        }])->where(['ms_wajibpajak_id' => $wajibpajak_id, 'karyawan_active' => 1])
        ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("(
            CASE WHEN (karyawan_status IN ('KONTRAK','PERCOBAAN'))
            THEN 
                TO_CHAR(karyawan_contract_begin, 'YYYY-MM') <= ?
                AND TO_CHAR(karyawan_contract_end, 'YYYY-MM')  >= ?
            ELSE
                TO_CHAR(karyawan_contract_begin, 'YYYY-MM') <= ?
            END
        )", [$payroll_periodym, $payroll_periodym, $payroll_periodym])->get();

        $stpenggajian = SettingPenggajianKaryawanModel::select('st_penggajian_karyawan.*')
            // ->where('stpenggajiankaryawan_active', '1')
            ->where('ms_wajibpajak_id', $wajibpajak_id)
            ->orderBy('stpenggajiankaryawan_created_at', 'desc')
            ->first();

        if(!$stpenggajian) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan lakukan pengaturan di menu Pengaturan > Absensi terlebih dahulu sebelum melakukan kalkulasi.'
            ]);
        }

        $stattendance = SettingAttendanceModel::select('st_attendance.*')
            // ->where('stpenggajiankaryawan_active', '1')
            ->where('ms_wajibpajak_id', $wajibpajak_id)
            ->orderBy('attendance_created_at', 'desc')
            ->first();

        if(!$stattendance) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan lakukan pengaturan di menu Pengaturan > Penggajian terlebih dahulu sebelum melakukan kalkulasi.'
            ]);
        }

        $data_payroll = [];
        $data_pph21 = [];
        $payroll_uuids = [];
        // $newuser = 0;
        // $olduser = 0;
        foreach($karyawans as $kr) {
            $karyawan = $kr;
            if(count($karyawan->payroll) < 1) {
                $uuid = 'RK-'.$karyawan->ms_wajibpajak_id.'-'.Uuid::uuid4()->toString();
                array_push($payroll_uuids, $uuid);
                $temp_data_payroll = [
                    'payroll_uuid' => $uuid,
                    'ms_user_id' => $karyawan->wajibpajak->ms_user_id,
                    'ms_wajibpajak_id' => $karyawan->ms_wajibpajak_id,
                    'ms_karyawan_id' => $karyawan->karyawan_id,
                    'payroll_period' => $payroll_period,
                    'payroll_total_netto' => 0,
                    'payroll_total_income' => 0,
                    'payroll_total_outcome' => 0,
                    'payroll_basic_salary' => $karyawan->karyawan_salary,
                    'payroll_prorate_salary' => 0,
                    'payroll_allowance_pph21' => 0,
                    'payroll_allowance_bpjskes' => 0,
                    'payroll_allowance_bpjstk' => 0,
                    
                    'payroll_deduction_pph21' => 0,
                    'payroll_deduction_bpjskes' => 0,
                    'payroll_deduction_bpjstk' => 0,
                    
                    'payroll_status' => 0,
                    'payroll_bpjskes_paidbycompany' => 0,
                    'payroll_bpjstk_paidbycompany' => 0,
                    'payroll_method' => $karyawan->karyawan_calculation_method,
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
                ];
        
                $temp_data_pph21 = [
                    'tr_payroll_uuid' => $temp_data_payroll['payroll_uuid'],
                    'ms_user_id' => $temp_data_payroll['ms_user_id'],
                    'ms_wajibpajak_id' => $temp_data_payroll['ms_wajibpajak_id'],
                    'ms_karyawan_id' => $temp_data_payroll['ms_karyawan_id'],
                    'ms_ptkp_id' => $karyawan->ms_ptkp_id,
                    'pph21_method' => $temp_data_payroll['payroll_method'],
                    'pph21_period' => $temp_data_payroll['payroll_period'],
                    'pph21_basic_salary' => $temp_data_payroll['payroll_basic_salary'],
                    'pph21_prorate_salary' => $temp_data_payroll['payroll_prorate_salary'],
                    'pph21_objekpajak_code' => $karyawan->objekpajak->objekpajak_code,
                    'pph21_objekpajak_description' => $karyawan->objekpajak->objekpajak_description,
                ];

                array_push($data_payroll, $temp_data_payroll);
                array_push($data_pph21, $temp_data_pph21);
            } 
            // else {
            //     $uuid = $karyawan->payroll[0]->payroll_uuid;
            //     array_push($payroll_uuids, $uuid);
            // }
        }
        DB::beginTransaction();
        try {
            if($data_payroll)
                PayrollModel::insert($data_payroll);

            if($data_pph21)
                PPh21Model::insert($data_pph21);

            if($payroll_uuids) {
                $request->request->add(['payroll_uuids' => $payroll_uuids, 'calculatenew' => 1]);
                // dd($payroll_uuids);
                $this->calculate($request);
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

    public function calculate(Request $request)
    {
        // INFO, uuid SHOULD REQUIRED
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $payroll_uuids = $request->get('payroll_uuids');
        $calculatenew = $request->get('calculatenew');
        $final = $request->get('final');
        if($final == 1) { // check setting exist
            $stpajkpph21 = SettingPajakPph21Model::where([
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])->first();
            if(!$stpajkpph21) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengaturan Pajak PPh21 tidak ditemukan. Silahkan lakukan pengaturan terlebih dahulu!'
                ]);
            }
        }
        
        $payroll_period = $request->get('payroll_period');
        // dd(date('Y-m', $payroll_period));\
        // dd($payroll_uuids);
        // $str = "11-Mar";
        $payroll_periodYm = null;
        $payroll_periodY = null;
        if($payroll_period) {
            $date = DateTime::createFromFormat('m-Y', $payroll_period);
            $payroll_periodY = $date->format('Y');
            $payroll_periodYm = $date->format('Y-m');
            $payroll_periodYmd = $date->format('Y-m-d');
            $payroll_periodYmlastday = $date->format('Y-m-t');
        }
        // dd($payroll_periodYmlastday);
        // dd(env('APP_ENV'));
        // $check_all = $request->post('check_all');
        $custom_allowance_data = $request->post('custom_allowance_data');
        $custom_deduction_data = $request->post('custom_deduction_data');
        $ps_total_netto = $request->post('ps_total_netto');
        $ps_total_pph21_total = $request->post('ps_total_pph21_total');
        // dd($check_all);
        if(!is_array($payroll_uuids)) {
            return response()->json([
                'success' => false,
                'message' => 'Silahkan pilih karyawan.'
            ]);
        }

        // dd($final);
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

        // $wherestbpjs = ['stbpjskaryawan_active' => '1'];
        // $wherestholiday = ['holiday_status_active' => true];
        // if($wajibpajak_id) {
            $wherestbpjs = ['stbpjskaryawan_active' => '1', 'ms_wajibpajak_id' => $wajibpajak_id];
            $wherestholiday = ['holiday_status_active' => true, 'ms_wajibpajak_id' => $wajibpajak_id];
        // }
        // dd($tunjangan_jabatan);
        $stbpjs_karyawan = SettingBpjsKaryawanModel::where($wherestbpjs)->first();
        $periode_year = $payroll_periodY;
        $holiday_karyawan = SettingHolidayModel::where($wherestholiday)->whereRaw(DB::raw("TO_CHAR(holiday_start_date, 'YYYY') = ?"), [$periode_year])->get();
        $bpjsrate = BpjsRateModel::where(['bpjsrate_active' => '1'])->get();
        $stpajkpph21 = SettingPajakPph21Model::where(['stpajakpph21_active' => '1', 'ms_wajibpajak_id' => $wajibpajak_id])->first();
        $temp_data = $this->calculatepayslip([
            'calculatenew' => $calculatenew,
            'wajibpajak_id' => $wajibpajak_id,
            // 'calculation_period' => $check_all ? $payroll_periodYm : null,
            'payroll_uuids' => $payroll_uuids, 
            'custom_allowance_data' => $custom_allowance_data, 
            'custom_deduction_data' => $custom_deduction_data,
            'ps_total_netto' => $ps_total_netto,
            'ps_total_pph21_total' => $ps_total_pph21_total,
            'setting' => $setting,
            'tunjangan_jabatan' => $tunjangan_jabatan,
            'stbpjs_karyawan' => $stbpjs_karyawan,
            'holiday_karyawan' => $holiday_karyawan,
            'bpjsrate' => $bpjsrate,
            'final' => $final,
            'stpajkpph21' => $stpajkpph21
        ]);

        $save_data_payroll = $temp_data['data_payroll'];
        $save_data_pph21 = $temp_data['data_pph21'];
        $save_data_lemburid = $temp_data['data_lemburids'];
        // dd($save_data_lemburid);
        // dd($save_data_payroll);
        if(count($payroll_uuids) == 1 && $final == 1) {
            $date = DateTime::createFromFormat('Y-m-d', $save_data_payroll[0]['payroll_period']);
            $payroll_periodYm = $date->format('Y-m');
            $payroll_periodYmd = $date->format('Y-m-d');
            $payroll_periodYmlastday = $date->format('Y-m-t');
        }

        $karyawan_ids = [];
        foreach($save_data_pph21 as $dtpph21) {
            $karyawan_ids[] = $dtpph21['ms_karyawan_id'];
        }
        if(count($karyawan_ids) > 0) {
            $karyawans = KaryawanModel::whereIn('karyawan_id', $karyawan_ids)->get();
            $tempkaryawans = [];
            foreach($karyawans as $tmpkr) {
                $tempkaryawans[$tmpkr->karyawan_id] = [
                    'objekpajak_code' => $tmpkr->objekpajak->objekpajak_code,
                    'objekpajak_description' => $tmpkr->objekpajak->objekpajak_description,
                ];
            }
            foreach($save_data_pph21 as &$dtpph21) {
                $dtpph21['pph21_objekpajak_code'] = $tempkaryawans[$dtpph21['ms_karyawan_id']]['objekpajak_code'];
                $dtpph21['pph21_objekpajak_description'] = $tempkaryawans[$dtpph21['ms_karyawan_id']]['objekpajak_description'];
            }
        }
        // dd($payroll_periodYmlastday);
        // check cutoff
        if($final == 1) {
            $stpenggajian = SettingPenggajianKaryawanModel::select('st_penggajian_karyawan.*')
            // ->where('stpenggajiankaryawan_active', '1')
            ->where('ms_wajibpajak_id', $wajibpajak_id)
            ->orderBy('stpenggajiankaryawan_created_at', 'desc')
            ->first();
            // dd($stpenggajian);
            if($stpenggajian->stpenggajiankaryawan_period == 'TANGGAL') {
                // check current date
                $dt = strlen($stpenggajian->stpenggajiankaryawan_enddate) > 1 ? $stpenggajian->stpenggajiankaryawan_enddate : '0'.$stpenggajian->stpenggajiankaryawan_enddate;
                $enddate = $payroll_periodYm."-".$dt;
                $cutoffdate = date('Y-m-d', strtotime("+1 day", strtotime($enddate)));
                $cutoffdatedmy = date('d-m-Y', strtotime($cutoffdate));
                // dd($enddate, $cutoffdate);
                
            } else {
                $enddate = $payroll_periodYmlastday;
                $cutoffdate = date('Y-m-d', strtotime("+1 day", strtotime($enddate)));
                $cutoffdatedmy = date('d-m-Y', strtotime($cutoffdate));
            }
            // if(strtotime(date('Y-m-d')) < strtotime($cutoffdate)) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Finalisasi hanya bisa dilakukan saat cut off date di tanggal '.$cutoffdatedmy.'.'
            //     ]);
            // }
            foreach($save_data_payroll as &$svp) {
                $svp['payroll_trx_at'] = date('Y-m-d H:i:s');
            }
            // dd($stpenggajian);
        }
        // dd($temp_data);
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
            
            if($save_data_lemburid) {
                foreach($save_data_lemburid as $lmb) {
                    LemburKaryawanModel::whereIn('lemburkaryawan_id', $lmb['lemburids'])
                    ->update(['tr_payroll_uuid' => $lmb['uuid']]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => ($final == 1) ? 'Finalisasi perhitungan berhasil' : 'Kalkulasi penggajian karyawan berhasil.'
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
        $calculatenew = isset($data['calculatenew']) && $data['calculatenew'] ? $data['calculatenew'] : 0; 
        // $calculation_period = isset($data['calculation_period']) && $data['calculation_period'] ? $data['calculation_period'] : null;
        $payroll_uuids = isset($data['payroll_uuids']) && $data['payroll_uuids'] ? $data['payroll_uuids'] : null;
        $custom_allowance_data = isset($data['custom_allowance_data']) && $data['custom_allowance_data'] ? $data['custom_allowance_data'] : null;
        $custom_deduction_data = isset($data['custom_deduction_data']) && $data['custom_deduction_data'] ? $data['custom_deduction_data'] : null;
        $pph21_prevcp_netto = isset($data['ps_total_netto']) && $data['ps_total_netto'] ? intval($data['ps_total_netto']) : 0;
        $pph21_prevcp_pph21_total = isset($data['ps_total_pph21_total']) && $data['ps_total_pph21_total'] ? intval($data['ps_total_pph21_total']) : 0;
        $final = isset($data['final']) && $data['final'] ? $data['final'] : null;
        $setting = $data['setting'];
        $tunjangan_jabatan = $data['tunjangan_jabatan'];
        // $sttunjangan = isset($data['sttunjangan']) && $data['sttunjangan'] ? $data['sttunjangan'] : null;
        $stbpjs_karyawan = $data['stbpjs_karyawan'];
        $holiday_karyawan = $data['holiday_karyawan'];
        $bpjsrate = $data['bpjsrate'];
        $stpajkpph21 = $data['stpajkpph21'];
        // dd($pph21_prevcp_pph21_total);
        // dd($holiday_karyawan);
        // $calculation_period = $request->input('calculation_period');
        // $payroll_ids = $request->get('payroll_ids');
        
        // $current_period = date('Y-m');
        // $current_month = date('n'); // month without leading zero
        
        // $appPph21Lib = new AppPPh21Library();
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
        
        // dd($bpjs_kes_ditanggung, $bpjs_tk_ditanggung);
        
        // $karyawans = KaryawanModel::with(['ptkp'])->where(['ms_wajibpajak_id' => $wajibpajak_id])->get();
        
        // dd($wajibpajak_id);
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
                         sttk.sttunjangankaryawan_type,
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
                                         subttk.sttunjangankaryawan_type,
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
                         sttk.sttunjangankaryawan_type,
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
                                         subttk.sttunjangankaryawan_type,
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
            });
            // if($calculation_period) {
            //     $payrolls->whereRaw("TO_CHAR(payroll_period, 'YYYY-MM') = ?", [$calculation_period]);
            // }
            $payrolls = $payrolls->get();
        }
        // dd($payrolls);

        $stbpjs_kes_lainnya = false;
        $stbpjs_tk_lainnya = false;
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
                    // $bpjs_kes_lainnya = $bpjskr->value;
                    $stbpjs_kes_lainnya = true;
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
                    // $bpjs_tk_lainnya = $bpjskr->value;
                    $stbpjs_tk_lainnya = true;
                }
                if($bpjskr->name == 'JKK_RATE' && $bpjskr->type == 'TENAGA_KERJA') {
                    $bpjs_tk_jkkrate_id = $bpjskr->value;
                }
            }
        }
        // dd($stbpjs_kes_lainnya, $stbpjs_tk_lainnya);

        $jkkrate = 0;
        foreach($bpjsrate as $brate) {
            if($brate->bpjsrate_code == 'JKK') {
                if($brate->bpjsrate_id == $bpjs_tk_jkkrate_id) {
                   $jkkrate = $brate->bpjsrate_rate;
                   break;
                }
            }
        }
        // $tarif21_nonnpwp = TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first();
        $data_payroll = [];
        $data_pph21 = [];
        $data_lemburids = [];
        foreach($payrolls as $py) {
            $karyawan = $py->karyawan;
            $payroll_method = $karyawan->karyawan_calculation_method;
            $payroll_period = $py->payroll_period;
            $payroll_period_ymd = $payroll_period;
            $stpenggajian_karyawan = $karyawan->penggajian;
            
            if($stbpjs_kes_lainnya) {
                $bpjs_kes_lainnya = $karyawan->karyawan_bpjskeslainnya;
            }

            if($stbpjs_tk_lainnya) {
                $bpjs_tk_lainnya = $karyawan->karyawan_bpjstklainnya;
            }
            // dd($karyawan->penggajian);
            // $ptkp = $karyawan->ptkp;
            // $karyawan = (object) $karyawan;
            // $karyawan_payroll_period = date('Y-m');
            // if($karyawan_payroll_period != $period) {

            // if(count($karyawan->payroll) == 0) {
                // dd($karyawan);
                $uuid = $py->payroll_uuid;
                $payroll_status = $py->payroll_status;
                // $uuids[$karyawan->ms_wajibpajak_id]['uuid'] = $uuid;
                // $uuids[$karyawan->ms_wajibpajak_id]['sttunjangan'] = $karyawan->sttunjangantbl;

                $isnpwp = ($karyawan->karyawan_npwp == '00.000.000.0-000.000') ? 'NO-NPWP' : 'NPWP';
                // dd($isnpwp);
                $ratenonnpwp = 100;
                // if($isnpwp == 'NO-NPWP') {
                //     $ratenonnpwp = $tarif21_nonnpwp->tarifnonnpwp_rate;
                // }
                $karyawan->ptkp->ptkp_detail = (object) PtkpDetailModel::where(['ptkpdet_category' => $karyawan->ptkp->ptkp_category, 'ptkpdet_active' => '1'])->orderBy('ptkpdet_rate_month', 'ASC')->get()->toArray();
                
                $bpjskes = 0;
                $bpjstk = 0;
                if(date('Y-m', strtotime($karyawan->karyawan_bpjskesdate)) <= $payroll_period) {
                    $bpjskes = ($karyawan->karyawan_isbpjskes && count($bpjs_karyawan_decode) > 0) ? 1 : 0; // this should check in st_bpjs_karyawan if provide or not.
                }
                if(date('Y-m', strtotime($karyawan->karyawan_bpjstkdate)) <= $payroll_period) {
                    $bpjstk = ($karyawan->karyawan_isbpjstk && count($bpjs_karyawan_decode) > 0) ? 1 : 0; // this should check in st_bpjs_karyawan if provide or not.
                }
                $isbpjs = [$bpjskes, $bpjstk];
                // dd($isbpjs, 'oooi', $payroll_period);

                // Get Prorate Salary
                $appPayslipLib = new AppPayslipLibrary();
                $appPayslipLib->ratenonnpwp = $ratenonnpwp;
                $appPayslipLib->custom_allowance_data = $custom_allowance_data;
                $appPayslipLib->custom_deduction_data = $custom_deduction_data;
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
                $appPayslipLib->pph21_prevcp_netto = $pph21_prevcp_netto;
                $appPayslipLib->pph21_prevcp_pph21_total = $pph21_prevcp_pph21_total;
                // dd($karyawan);
                $prorate_salary = $appPayslipLib->calculate_prorate_salary();
                if($calculatenew || ($karyawan->attendance && $karyawan->attendance->attendance_is_used == 0)) {
                    $prorate_salary['absence_days'] = 0;
                }
                // dd($prorate_salary);
                $appPayslipLib->prorate_salary = $prorate_salary;
                $appPayslipLib->isbpjs = $isbpjs;
                
                $calculate_pph21 = $appPayslipLib->calculate();

                $payroll = $calculate_pph21['payroll'];
                $pph21 = $calculate_pph21['pph21'];
                $ptkp = $pph21['ptkp'];
                // dd($pph21);
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
                    'payroll_allowance_overtime' => $payroll['allowance_overtime'],
                    'payroll_deduction_pph21' => $payroll['deduction_pph21'],
                    'payroll_deduction_bpjskes' => $payroll['deduction_bpjskes'],
                    'payroll_deduction_bpjstk' => $payroll['deduction_bpjstk'],
                    
                    // 'payroll_status' => ($final == 1) ? 1 : $payroll_status,
                    'payroll_status' => ($final == 1) ? 1 : 0,
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
                    // 'pph21_allowance_overtime' => $temp_data_payroll['payroll_allowance_overtime'],
                    'pph21_pkp' => $pph21['total_pkp_pertahun'],
                    'pph21_ptkp' => $pph21['total_ptkp'],
                    
                    'pph21_prevcp_netto' => $pph21['pph21_prevcp_netto'],
                    'pph21_prevcp_pph21_total' => $pph21['pph21_prevcp_pph21_total'],

                    'pph21_bruto_month' => $pph21['total_bruto_perbulan'],
                    'pph21_bruto_year' => $pph21['total_bruto_pertahun'],
                    'pph21_netto_month' => $pph21['total_neto_perbulan'],
                    'pph21_netto_year' => $pph21['total_neto_pertahun'],
                    'pph21_total_month' => $pph21['total_pph_terutang_perbulan'],
                    'pph21_total_year' => $pph21['total_pph_terutang_pertahun'],
        
                    'pph21_ptkp_data' => json_encode($ptkp),
                    'pph21_ptkpdet_data' => json_encode($ptkp_detail),
                    'pph21_tarif21_data' => ($pph21['tarif21']) ? json_encode($pph21['tarif21']) : null,
                    'pph21_pajakpph21_data' => ($stpajkpph21) ? json_encode($stpajkpph21) : null,
                ];

                // dd($temp_data_payroll, $temp_data_pph21);
                array_push($data_payroll, $temp_data_payroll);
                array_push($data_pph21, $temp_data_pph21);
                array_push($data_lemburids, ['uuid' => $uuid, 'lemburids' => $calculate_pph21['lemburids']]);
            // }
        }
        return ['data_payroll' => $data_payroll, 'data_pph21' => $data_pph21, 'data_lemburids' => $data_lemburids];
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

    public function importTemplate(Request $request)
    {
        try {
            // Get the file path from the 'public' directory
            $excelFilePath = public_path('assets/import/Template_Import_Tambahan_Tunjangan_&_Potongan.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $spreadsheet = $reader->load($excelFilePath);

            $sheetKaryawan = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Referensi Data Karyawan');
            $spreadsheet->addSheet($sheetKaryawan, 2);

            $karyawan = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                ->where('karyawan_active', 1)
                ->where('karyawan_status', '!=', 'NONKARYAWAN')
                ->orderBy('karyawan_enid', 'ASC')
                ->with(['jabatan'])
                ->get();

            // Get the specified sheet by title
            $htmlString = view('user.master.karyawan.import.profil.import-supervisor', ['title' => 'Referensi Data Karyawan', 'supervisor' => $karyawan])->render();
            $dom1 = new DOMDocument();
            $dom1->loadHTML($htmlString);
            $table1 = $dom1->getElementsByTagName('table')->item(0);

            $rowIndex1 = 1;
            foreach ($table1->getElementsByTagName('tr') as $row) {
                $cellIndex = 1;
                foreach ($row->getElementsByTagName('th') as $header) {
                    // Extract the text content without HTML tags
                    $headerText = strip_tags($header->nodeValue);
                    
                    // Set the cell value
                    $sheetKaryawan->setCellValueByColumnAndRow($cellIndex, $rowIndex1, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheetKaryawan->getStyleByColumnAndRow($cellIndex, $rowIndex1)->getFont()->setBold(true);
                    
                    $cellIndex++;
                }
            
                // Skip the first row (headers row) when iterating through data rows
                if ($rowIndex1 === 1) {
                    $rowIndex1++;
                    continue;
                }
            
                foreach ($row->getElementsByTagName('td') as $cell) {
                    // Extract the text content without HTML tags
                    $cellValue = strip_tags($cell->nodeValue);
                    
                    // Set the cell value
                    $sheetKaryawan->setCellValueByColumnAndRow($cellIndex, $rowIndex1, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex1++;
            }

            $boldFontStyle = [
                'font' => ['bold' => true],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];

            $sheetTemplate = $spreadsheet->createSheet();
            $sheetTemplate->setTitle('Impor Tambahan Data');
            
            $sheetTemplate->setCellValue('A1', 'Id Karyawan*');
            $sheetTemplate->setCellValue('B1', 'Tipe Tambahan*');
            $sheetTemplate->setCellValue('C1', 'Deskripsi*');
            $sheetTemplate->setCellValue('D1', 'Nominal*');
            $sheetTemplate->setCellValue('E1', 'Dikenakan Pajak*');
            $sheetTemplate->setCellValue('F1', 'Periode*');

            for ($col = 'A'; $col <= 'F'; $col++) {
                $sheetTemplate->getStyle($col . '1')->applyFromArray($boldFontStyle);
                $sheetTemplate->getStyle($col . ':' . $col)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            }

            $startRow = 2;

            $listStatus = '"Tunjangan,Potongan"';
            $statusValidation = $sheetTemplate->getCell('B' . $startRow)->getDataValidation();
            $statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $statusValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $statusValidation->setShowDropDown(true);
            $statusValidation->setFormula1($listStatus);

            $listTrueFalse = '"Ya,Tidak"';
            $userValidation = $sheetTemplate->getCell('E' . $startRow)->getDataValidation();
            $userValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $userValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $userValidation->setShowDropDown(true);
            $userValidation->setFormula1($listTrueFalse);

            for ($row = $startRow + 1; $row <= 51; $row++) {
                $cellB = $sheetTemplate->getCell('B' . $row);
                $cellB->setDataValidation(clone $statusValidation);
                
                $cellE = $sheetTemplate->getCell('E' . $row);
                $cellE->setDataValidation(clone $userValidation);
            }

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                
                // Set all columns in the sheet to auto width
                if($sheetName !== 'Panduan Pengguna') {
                    foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }
            
            $writer = new Xls($spreadsheet);
            $unixtime = time();
            $filename = 'Templat_Impor_Tambahan_Tunjangan_&_Potongan_' . $unixtime . '.xls';
            $location = public_path('assets/export/');
            ob_start();
            $writer->save($location.$filename);
            // $xlsData = ob_get_contents();
            ob_end_clean();

            return url('/assets/export/'.$filename);
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importAdditional(Request $request)
    {
        if (!$request->hasfile('file')) {
            $res["success"] = false;
            $res["message"] = 'Data excel wajib diisi!';
            return response()->json($res, 500);
        }

        // $quota = $request->get('quota');
    
        // Get the uploaded file
        $file = $request->file('file');

        if(!in_array($file->extension(), ['xls','xlsx','csv'])) {
            $res["success"] = false;
            $res["message"] = 'Format harus xls, xlsx atau csv!';
            return response()->json($res, 500);
        }
    
        $originalFileName = $file->getClientOriginalName();

        $filename = rand().'.xlsx';
        $file->move(public_path('uploads/'), $filename);
        $path = public_path('uploads/'.$filename);

        $inputFileType = IOFactory::identify($path);
        $reader = IOFactory::createReader($inputFileType);
        $worksheetData = $reader->listWorksheetInfo($path);

        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
    
        // Get the Sheet D
        $sheet = $spreadsheet->getSheetByName('Impor Tambahan Data');
        $maxRows = $sheet->getHighestRow();
        // dd($maxRows);
        // if($maxRows > $quota['maxquota']) {
        //     return response()->json([
        //         'success' => false,
        //         'noquota' => true,
        //         'message' => 'Kuota sudah habis! kuota maksimal adalah '.$quota['maxquota']
        //     ]);
        // }

        $chunkFilter = new ChunkReadFilter();
        $reader->setReadFilter($chunkFilter);

        $chunkSize = 100; // read as chunk
        $startRow = 1; // mulai baris ke 3;

        $result = [];

        for ($startRow; $startRow <= $maxRows; $startRow += $chunkSize) {
            $chunkFilter = new ChunkReadFilter($startRow, $chunkSize);
            // // Tell the Read Filter, the limits on which rows we want to read this iteration
            $chunkFilter->setRows($startRow, $chunkSize);
            // Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
            $spreadsheet_chunk = $reader->load($path);
            $nb = $startRow;
            $max_chunk = $startRow + $chunkSize;

            for($nb; $nb<=$max_chunk; $nb++) {
                $nbi = $nb + 1;
                $columnA = $sheet->getCell("A" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $dataB = $sheet->getCell("B" . $nbi)->getValue();

                if($columnA === null) {
                    if($dataB === null) {
                        break;
                    } else {
                        $spreadsheet_chunk->__destruct();
                        $spreadsheet_chunk = null;
                        unset($spreadsheet_chunk);
                        
                        return response()->json([
                            'success' => false,
                            'message' => "Mohon lengkapi Id Karyawan yang masih kosong"
                        ]);
                    }
                }

                $columnB = $dataB;
                $columnC = $sheet->getCell("C" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnD = $sheet->getCell("D" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnE = $sheet->getCell("E" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnF = convertExcelDate($sheet->getCell("F" . $nbi)); // Use $sheet instead of $worksheetData
               
                $result[] = [
                    "karyawan_enid" => $columnA,
                    "additional_type" => $columnB,
                    "additional_description" => $columnC,
                    "additional_nominal" => $columnD,
                    "additional_is_tax" => $columnE,
                    "additional_periode" => $columnF,
                    "aditional_periode_raw" => $sheet->getCell("F" . $nbi)->getValue(),
                    "row" => $nb + 1
                ];
            }

            $spreadsheet_chunk->__destruct();
            $spreadsheet_chunk = null;
            unset($spreadsheet_chunk);
        }
        // then release the memory
        $spreadsheet->__destruct();
        $spreadsheet = null;
        unset($spreadsheet);
        $reader = null;
        unset($reader);

        
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Berhasil import data non karyawan.',
        //     'result' => $result
        // ], 200);

        function capitalizeFirstLetterOfWords($string) {
            // Convert the string to lowercase
            $lowercaseString = strtolower($string);
            
            // Use ucwords() to capitalize the first letter of each word
            $capitalizedString = ucwords($lowercaseString);
            
            return $capitalizedString;
        }

        function findEntryByName(&$array, $name) {
            foreach ($array as $key => &$entry) {
                if ($entry['name'] === $name) {
                    return $key;
                }
            }
            return null;
        }

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Berhasil import data karyawan.',
        //     'result' => $result
        // ], 200);

        $mane = null;

        DB::beginTransaction();
        try {
            $totalComplete = 0;
            $totalFail = 0;
            $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
            $user_id = session()->get('user_data')['user_id'];

            if(count($result) > 0) {
                $karyawanCreated = [];
        
                for($x = 0; $x < count($result); $x++) {
                    if ($result[$x]['karyawan_enid'] === null || $result[$x]['karyawan_enid'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Karyawan Id';
                        continue;
                    }

                    $checkKaryawan = KaryawanModel::where('karyawan_enid', $result[$x]['karyawan_enid'])->where('ms_wajibpajak_id', $wajibpajak_id)->first();
    
                    if(!$checkKaryawan) {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Id Karyawan tidak terdaftar';
                        continue;
                    }

                    if($result[$x]['additional_type'] !== 'Potongan' && $result[$x]['additional_type'] !== 'Tunjangan') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon hanya masukan Potongan atau Tunjangan untuk Tipe Tambahan';
                        continue;
                    }
                    
                    if($result[$x]['additional_description'] === null || $result[$x]['additional_description'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Deskripsi';
                        continue;
                    }

                    if(!is_numeric($result[$x]['additional_nominal'])) {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan angka untuk Nominal';
                        continue;
                    }

                    if($result[$x]['additional_is_tax'] !== 'Ya' && $result[$x]['additional_is_tax'] !== 'Tidak') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon hanya masukan Ya atau Tidak untuk status Dikenakan Pajak';
                        continue;
                    }

                    $periode = null;
                    $data_payroll = null;

                    if($result[$x]['additional_periode'] !== '' && $result[$x]['additional_periode'] !== null && $result[$x]['additional_periode'] !== '-') {
                        $periode = $result[$x]['additional_periode'];

                        if (formatDate($periode) === false) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan format Periode sesuai format sistem REKKAA';
                            continue;
                        } else {
                            $currentMonthYear = date('Y-m');
                            $inputMonthYear = date('Y-m', strtotime($periode));

                            if ($inputMonthYear !== $currentMonthYear) {
                                $totalFail = $totalFail + 1;
                                $result[$x]['status'] = 'Gagal';
                                $result[$x]['remark'] = 'Periode tidak sesuai dengan Periode saat ini';
                                continue;
                            } else {
                                $startDate = date('Y-m-01', strtotime($periode)); // First day of the month
                                $endDate = date('Y-m-t', strtotime($periode));   // Last day of the month
                                
                                $data_payroll = PayrollModel::where('ms_karyawan_id', $checkKaryawan->karyawan_id)
                                    ->whereRaw("payroll_period BETWEEN ? AND ?", [$startDate, $endDate])
                                    ->first();
                                
                                    // dd($data_payroll);

                                if(!$data_payroll) {
                                    $totalFail = $totalFail + 1;
                                    $result[$x]['status'] = 'Gagal';
                                    $result[$x]['remark'] = 'Karyawan ' . $result[$x]['karyawan_enid'] . ' belum memiliki perhitungan Penggajian periode sekarang';
                                    continue;
                                }
                            }
                        }
                    } else {
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Periode penggajian';
                        continue;
                    }
                    $datatunjangan = json_decode($data_payroll->payroll_allowance_addition, true) ?? [];
                    $datapotongan = json_decode($data_payroll->payroll_deduction_addition, true) ?? [];
                    
                    $rawdata = [
                        "name" => $result[$x]['additional_description'],
                        "nominal" => $result[$x]['additional_nominal'],
                        "taxable" => $result[$x]['additional_is_tax'] == 'Ya' ? 1 : 0
                    ];
                    
                    if ($result[$x]['additional_type'] === 'Potongan') {
                        $key = findEntryByName($datapotongan, $rawdata['name']);
                        if ($key !== null) {
                            // Update nominal value
                            $datapotongan[$key]['nominal'] += $rawdata['nominal'];
                        } else {
                            // Add a new entry
                            $datapotongan[] = $rawdata;
                        }
                    } else {
                        $key = findEntryByName($datatunjangan, $rawdata['name']);
                        if ($key !== null) {
                            // Update nominal value
                            $datatunjangan[$key]['nominal'] += $rawdata['nominal'];
                        } else {
                            // Add a new entry
                            $datatunjangan[] = $rawdata;
                        }
                    }
                    
                    $dataToMerge = [
                        'custom_allowance_data' => $datatunjangan,
                        'custom_deduction_data' => $datapotongan,
                        'payroll_uuids' => [$data_payroll->payroll_uuid]
                    ];

                    // dd($dataToMerge);
                    
                    // Merge the data with the existing request
                    $request->merge($dataToMerge);
                    
                    $this->calculate($request);
    
                    $totalComplete = $totalComplete + 1;
                    $result[$x]['status'] = 'Sukses';
                    $result[$x]['remark'] = '-';
                }
            }

            $createHistory = HistoryImportModel::create([
                'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                'ms_user_id' => session()->get('user_data')['user_id'],
                'historyimport_type' => "ADDITIONAL_PAYROLL",
                'historyimport_date' => date('Y-m-d H:i:s'),
                'historyimport_detail_import' => $totalComplete . ' Sukses, ' . $totalFail . ' Gagal', 
                'historyimport_content' => json_encode($result),
                'historyimport_file_name' => $originalFileName
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Berhasil import data tambahan penggajian.',
                'result' => $result,
                'totalsuccess' => $totalComplete,
                'totalfail'=> $totalFail,
                'totaldata' => $totalComplete + $totalFail,
                'history' => $createHistory->historyimport_id
            ], 200);
        } catch (\Exception $e) {
            unlink(public_path('uploads/'). $filename); // remove file
            Log::error('Error occurred: ' . $e->getMessage());
            DB::rollback();
            // something went wrong
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function generateHistory(Request $request) 
    {
        try {
            // Get the file path from the 'public' directory
            $excelFilePath = public_path('assets/import/Template_Import_Tambahan_Tunjangan_&_Potongan.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $unixtime = time();
            $title = 'Riwayat_Impor_Tambahan_Tunjangan_&_Potongan_'.$unixtime;

            $spreadsheet = $reader->load($excelFilePath);

            $sheetSpv = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Referensi Data Karyawan');
            $spreadsheet->addSheet($sheetSpv, 2);

            $supervisor = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                ->where('karyawan_active', 1)
                ->where('karyawan_status', '!=', 'NONKARYAWAN')
                ->orderBy('karyawan_enid', 'ASC')
                ->with(['jabatan'])
                ->get();

            // return $supervisor;

            // Get the specified sheet by title
            $htmlString = view('user.master.karyawan.import.profil.import-supervisor', ['title' => 'Referensi Data Karyawan', 'supervisor' => $supervisor])->render();
            $dom1 = new DOMDocument();
            $dom1->loadHTML($htmlString);
            $table1 = $dom1->getElementsByTagName('table')->item(0);

            $rowIndex1 = 1;
            foreach ($table1->getElementsByTagName('tr') as $row) {
                $cellIndex = 1;
                foreach ($row->getElementsByTagName('th') as $header) {
                    // Extract the text content without HTML tags
                    $headerText = strip_tags($header->nodeValue);
                    
                    // Set the cell value
                    $sheetSpv->setCellValueByColumnAndRow($cellIndex, $rowIndex1, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheetSpv->getStyleByColumnAndRow($cellIndex, $rowIndex1)->getFont()->setBold(true);
                    
                    $cellIndex++;
                }
            
                // Skip the first row (headers row) when iterating through data rows
                if ($rowIndex1 === 1) {
                    $rowIndex1++;
                    continue;
                }
            
                foreach ($row->getElementsByTagName('td') as $cell) {
                    // Extract the text content without HTML tags
                    $cellValue = strip_tags($cell->nodeValue);
                    
                    // Set the cell value
                    $sheetSpv->setCellValueByColumnAndRow($cellIndex, $rowIndex1, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex1++;
            }

            $boldFontStyle = [
                'font' => ['bold' => true],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];

            $sheetTemplate = $spreadsheet->createSheet();
            $sheetTemplate->setTitle('Impor Tambahan Data');
            
            $sheetTemplate->setCellValue('A1', 'Id Karyawan*');
            $sheetTemplate->setCellValue('B1', 'Tipe Tambahan*');
            $sheetTemplate->setCellValue('C1', 'Deskripsi*');
            $sheetTemplate->setCellValue('D1', 'Nominal*');
            $sheetTemplate->setCellValue('E1', 'Dikenakan Pajak*');
            $sheetTemplate->setCellValue('F1', 'Periode*');
            $sheetTemplate->setCellValue('G1', 'Status');
            $sheetTemplate->setCellValue('H1', 'Catatan');
            // $sheetImpor->getStyle('A2:AB2')->applyFromArray($boldFontStyle);

            $id = $request->input('id');

            $getHistory = HistoryImportModel::find($id);

            $content = json_decode($getHistory['historyimport_content']);

            $rowIndex = 2;  

            foreach($content as $item) {
                $listImpor = [
                    'A' => isset($item->karyawan_enid) ? $item->karyawan_enid : '',
                    'B' => isset($item->additional_type) ? $item->additional_type : '',
                    'C' => isset($item->additional_description) ? $item->additional_description : '',
                    'D' => isset($item->additional_nominal) ? $item->additional_nominal : '',
                    'E' => isset($item->additional_is_tax) ? $item->additional_is_tax : '',
                    'F' => isset($item->aditional_periode_raw) ? $item->aditional_periode_raw : '',
                    'G' => isset($item->status) ? $item->status : 'Sukses',
                    'H' => isset($item->remark) ? $item->remark : '-'
                ];

                foreach ($listImpor as $column => $value) {
                    $sheetTemplate->setCellValueExplicit($column . $rowIndex, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }

                // Increment the row index for the next row of data
                $rowIndex++;
            }

            $startRow = 2;

            $listStatus = '"Tunjangan,Potongan"';
            $statusValidation = $sheetTemplate->getCell('B' . $startRow)->getDataValidation();
            $statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $statusValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $statusValidation->setShowDropDown(true);
            $statusValidation->setFormula1($listStatus);

            $listTrueFalse = '"Ya,Tidak"';
            $userValidation = $sheetTemplate->getCell('E' . $startRow)->getDataValidation();
            $userValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $userValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $userValidation->setShowDropDown(true);
            $userValidation->setFormula1($listTrueFalse);

            for ($row = $startRow + 1; $row <= 51; $row++) {
                $cellB = $sheetTemplate->getCell('B' . $row);
                $cellB->setDataValidation(clone $statusValidation);
                
                $cellE = $sheetTemplate->getCell('E' . $row);
                $cellE->setDataValidation(clone $userValidation);
            }

            for ($col = 'A'; $col <= 'F'; $col++) {
                $sheetTemplate->getStyle($col . '1')->applyFromArray($boldFontStyle);
                $sheetTemplate->getStyle($col . ':' . $col)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            }

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                
                // Set all columns in the sheet to auto width
                if($sheetName !== 'Panduan Pengguna') {
                    foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }
            
            $writer = new Xls($spreadsheet);

            $filename = $title.'.xls';
            $location = public_path('assets/export/');
            ob_start();
            $writer->save($location.$filename);
            // $xlsData = ob_get_contents();
            ob_end_clean();

            return url('/assets/export/'.$filename);
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importHistory(Request $request)
    {
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
        
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        
        // Get the start_date and end_date inputs
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        
        $dataHistory = HistoryImportModel::where('historyimport_type', 'ADDITIONAL_PAYROLL')
            ->where('tr_history_import.ms_wajibpajak_id', $wajibpajak_id)
            ->orderBy('historyimport_date', 'DESC')
            ->leftjoin('mr_user_wajib_pajak', 'tr_history_import.ms_user_id', '=', 'mr_user_wajib_pajak.ms_user_id')
            ->select('tr_history_import.historyimport_date', 'tr_history_import.ms_user_id', 'tr_history_import.historyimport_status', 'tr_history_import.historyimport_id', 'tr_history_import.historyimport_file_name', 'tr_history_import.historyimport_detail_import', 'mr_user_wajib_pajak.userwajibpajak_name');
        
        // Apply date filtering if start_date and end_date are provided
        if ($start_date && $end_date) {
            $dataHistory->whereBetween('tr_history_import.historyimport_date', [$start_date, $end_date]);
        }
        
        $dataHistory = $dataHistory->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data Setting Kehadiran.',
            'data' => $dataHistory->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $dataHistory->total(),
            'recordsTotal' => $dataHistory->total(),
        ], 200);
    }
}

class ChunkReadFilter implements IReadFilter
{
    private $startRow = 0;

    private $endRow = 0;

    /**
     * Set the list of rows that we want to read.
     *
     * @param mixed $startRow
     * @param mixed $chunkSize
     */
    public function setRows($startRow, $chunkSize)
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        //  Only read the heading row, and the rows that are configured in            $this->_startRow and $this->_endRow
        if (($row == 1) || ($row >= $this->startRow && $row <   $this->endRow)) {
            return true;
        }

        return false;
    }
}