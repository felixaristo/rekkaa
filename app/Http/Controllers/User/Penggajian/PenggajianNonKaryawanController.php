<?php
namespace App\Http\Controllers\User\Penggajian;

use App\Http\Controllers\Controller;
use App\Libraries\AppPPh21NonKaryawanLibrary;
use App\Model\Master\KaryawanModel;
use App\Model\Master\Tarif21Model;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use App\Model\Transaction\PayrollModel;
use App\Model\Transaction\PPh21Model;
use Carbon\Carbon;
use App\Libraries\ChunkReadFilterLibrary;
use App\Model\Master\PtkpDetailModel;
use App\Model\Master\TarifNonNpwpModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Setting\SettingPajakPph21Model;
use App\Model\Transaction\HistoryImportModel;
use App\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Ramsey\Uuid\Uuid;
use Spatie\Activitylog\Models\Activity;

class PenggajianNonKaryawanController extends Controller
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
            'title' => 'Penggajian Non Karyawan',
            'content' => 'user.penggajian.nonkaryawan.index',
            'user' => $user
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
    public function detail($karyawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $periode = ($request->get('periode')) ? $request->get('periode') : date('m-Y');
        $payroll_periodyearmonth = Carbon::createFromFormat('!m-Y', $periode)->format('Y-m');

        $karyawan = KaryawanModel::with(['ptkp', 'objekpajak', 'jabatan', 'payroll' => function($q) use ($payroll_periodyearmonth) {
            $q->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = ?)", [$payroll_periodyearmonth]);
            $q->whereIn('payroll_karyawan_status', ['NONKARYAWAN']);
            $q->where([
                // 'payroll_status' => 3,
                'payroll_active' => '1',
            ]);
        }])
        ->when($payroll_periodyearmonth, function($q, $payroll_periodyearmonth) {
            return $q->whereRaw("(
                CASE WHEN karyawan_contract_end IS NOT NULL
                THEN 
                    TO_CHAR(karyawan_contract_end, 'YYYY-MM') >= ?
                ELSE 
                    karyawan_contract_end IS NULL
                END
            )", [$payroll_periodyearmonth]);
        })
        ->where([
            'karyawan_id' => $karyawanId,
            'ms_wajibpajak_id' => $wajibpajak_id,
            // 'karyawan_active' => '1',
        ])
        ->whereRaw("(TO_CHAR(karyawan_contract_begin, 'YYYY-MM') <= ?)", [$payroll_periodyearmonth])
        ->whereIn('karyawan_status', ['NONKARYAWAN'])
        ->first();

        if(!$karyawan) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data karyawan tidak ditemukan.'
                ]);
            } else {
                abort(404);
            }
        }

        // dd($karyawan);
        // dd($payroll->karyawan->jabatan->karyawanjabatan_name);
        $data = [
            'title' => 'Penggajian',
            'karyawan' => $karyawan,
            'ptkp_detail' => PtkpDetailModel::select('ptkpdet_id','ptkpdet_year','ptkpdet_rate_percentage','ptkpdet_category','ptkpdet_rate_month')->where(['ptkpdet_active' => 1])->orderBy('ptkpdet_rate_percentage', 'ASC')->get(),
            'tarif21' => Tarif21Model::where(['tarif21_active' => 1])->get(),
            'tarif21_nonnpwp' => TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first(),
            'content' => 'user.penggajian.nonkaryawan.detail',
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
        $order_columns = ['karyawan_id','karyawan_name','karyawan_id','payroll_total_netto'];
        $order_col = $order_columns[0];
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $periode = ($request->get('periode')) ? $request->get('periode') : date('m-Y');
        $payroll_periodyearmonth = Carbon::createFromFormat('!m-Y', $periode)->format('Y-m');
        $karyawan_ids = $request->get('karyawan_ids');

        $where = [
            // 'karyawan_active' => 1,
            // 'karyawan_contract_end' => null,
            // 'karyawan_contract_now' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ];
        
        $escape_periode = DB::connection()->getPdo()->quote($payroll_periodyearmonth);
        $list = KaryawanModel::select("ms_karyawan.*"
        , DB::raw("(SELECT SUM(payroll_total_netto) FROM tr_payroll WHERE ms_karyawan_id = karyawan_id AND payroll_active='1' AND payroll_status='3' AND TO_CHAR(payroll_period, 'YYYY-MM') = {$escape_periode}) as payroll_total_netto"))
        ->with(['payroll' => function($q) use($payroll_periodyearmonth) {
            $q->where([
                "payroll_active" => "1",
                "payroll_status" => "3"
            ]);
            $q->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = ?)", [$payroll_periodyearmonth]);
        }])
        // ->when($payroll_periodyearmonth, function($q, $payroll_periodyearmonth) {
        //     return $q->whereRaw("(
        //         CASE WHEN karyawan_contract_end IS NOT NULL
        //         THEN 
        //             TO_CHAR(karyawan_contract_end, 'YYYY-MM') >= ?
        //         END
        //     )", [$payroll_periodyearmonth]);
        // })
        ->when($karyawan_ids, function($q, $karyawan_ids) {
            return $q->whereIn('karyawan_id', $karyawan_ids);
        })
        ->where($where)
        ->whereIn('karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("(TO_CHAR(karyawan_contract_begin, 'YYYY-MM') <= ? AND (
            CASE WHEN karyawan_contract_end IS NOT NULL
            THEN 
                TO_CHAR(karyawan_contract_end, 'YYYY-MM') >= ?
            ELSE
                karyawan_contract_end IS NULL
            END
        ))", [$payroll_periodyearmonth, $payroll_periodyearmonth])
        ->take($limit)->skip($offset)->orderBy($order_col, $order_type)->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = KaryawanModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable_detail(Request $request)
    {
        // dd(env('DB_HOST'));
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $limit = 100;
        $periode = ($request->get('periode')) ? $request->get('periode') : date('m-Y');
        $payroll_periodyearmonth = Carbon::createFromFormat('!m-Y', $periode)->format('Y-m');
        $periodeyear = Carbon::createFromFormat('!m-Y', $periode)->format('Y');
        // dd($periode);
        $karyawan_id = $request->get('karyawan_id');

        $where = [
            'payroll_active' => 1,
            // 'karyawan_contract_end' => null,
            'ms_karyawan_id' => $karyawan_id,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ];
        
        // dd($payroll_periodyearmonth);
        $list = PayrollModel::with(['pph21'])
        ->where($where)
        ->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = ?)", [$payroll_periodyearmonth])
        ->whereIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->limit($limit)
        ->orderBy('payroll_trx_at', 'asc')
        ->orderBy('payroll_created_at', 'asc')->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = PayrollModel::where($where)
        ->whereIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = ?)", [$payroll_periodyearmonth])
        ->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        // $data['total_dpp_kulumatif'] = $ddp_kulumatif->total_dpp_kumulatif;
        
        return response()->json($data);
    }

    public function confirmation(Request $request)
    {
        // INFO, uuid SHOULD REQUIRED
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $payroll_uuids = $request->get('payroll_uuids');
        $stpajkpph21 = SettingPajakPph21Model::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$stpajkpph21) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Pajak PPh21 tidak ditemukan. Silahkan lakukan pengaturan terlebih dahulu!'
            ]);
        }
        // dd($payroll_uuids);
        $payrolls = PayrollModel::select("tr_payroll.*"
        , DB::raw("(SELECT laspayroll.payroll_trx_at FROM tr_payroll laspayroll WHERE
            laspayroll.payroll_karyawan_status = 'NONKARYAWAN' AND laspayroll.payroll_active='1'
            AND TO_CHAR(laspayroll.payroll_period, 'YYYY-MM') = TO_CHAR(tr_payroll.payroll_period, 'YYYY-MM') 
            AND tr_payroll.ms_karyawan_id = laspayroll.ms_karyawan_id
            AND tr_payroll.ms_wajibpajak_id = laspayroll.ms_wajibpajak_id
            AND laspayroll.payroll_lock = '0' ORDER BY laspayroll.payroll_trx_at ASC LIMIT 1) as payroll_trx_at_lastunlock")
        )
        ->where([
            'ms_wajibpajak_id' => $wajibpajak_id, 
            'payroll_status' => 1, 
            'payroll_lock' => 0
        ])
        ->whereIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereIn('payroll_uuid', $payroll_uuids)->get();

        if(count($payrolls) <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Perhitungan penggajian tidak ditemukan!',
            ]);
        }

        // dd($payrolls);
        $lastunlockexist = false;
        $uuids = [];
        foreach($payrolls as $py) {
            // $trxat = strtotime($py->payroll_trx_at);
            // $trxatlasunlock = strtotime($py->payroll_trx_at_lastunlock);
            // if($trxat > $trxatlasunlock) {
            //     $lastunlockexist = true;
            //     break;
            // }
            array_push($uuids, $py->payroll_uuid);
        }
        
        // if($lastunlockexist) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Perhitungan pada tanggal sebelumnya belum dikonfirmasi. Silahkan konfirmasi terlebih dahulu!',
        //     ]);
        // }

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
        $payrolls = PayrollModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id, 
            'payroll_status' => 2, 
            'payroll_lock' => 1
        ])
        ->whereIn('payroll_karyawan_status', ['NONKARYAWAN'])
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
        ->whereIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->first();

        if(!$payroll) {
            abort(404);
        }

        $stpenggajian = SettingPenggajianKaryawanModel::where([
            'stpenggajiankaryawan_active' => 1,
            'stpenggajiankaryawan_isnonemployee' => TRUE,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
            
        // dd($payroll->karyawan->penggajian);
        // dd($payroll->karyawan->jabatan->karyawanjabatan_name);
        $data = [
            'title' => 'Cetak Penggajian',
            'payroll' => $payroll,
            'stpenggajian' => $stpenggajian,
            'type' => 'payroll',
            'content' => 'user.penggajian.cetak.payslip-nonkaryawan-cetak',
        ];
        return view('user.penggajian.cetak.payslip-nonkaryawan-cetak', $data);
        // return view('user.penggajian.cetak.payslip-multiple-cetak', $data);
    }

    /**
     * Display a cetak of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function cetakperiode(Request $request)
    { 
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawan_id = $request->get('karyawan_id');
        $payroll_period = $request->get('periode');
        $payroll_periodyearmonth = Carbon::createFromFormat('!m-Y', $payroll_period)->format('Y-m');
        $payrolls = PayrollModel::with(['karyawan','wajibpajak','karyawan.penggajian'])->where([
            'ms_karyawan_id' => $karyawan_id,
            'ms_wajibpajak_id' => $wajibpajak_id,
            'payroll_status' => 3,
            'payroll_active' => '1',
        ])
        ->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = ?)", [$payroll_periodyearmonth])
        ->whereIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->get();

        if(count($payrolls) < 1) {
            abort(404);
        }
        
        $stpenggajian = SettingPenggajianKaryawanModel::where([
            'stpenggajiankaryawan_active' => 1,
            'stpenggajiankaryawan_isnonemployee' => TRUE,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
            
        // dd($stpenggajian);
        $data = [
            'title' => 'Cetak Penggajian',
            'type' => 'periode',
            'payrolls' => $payrolls,
            'stpenggajian' => $stpenggajian,
            'content' => 'user.penggajian.cetak.payslip-nonkaryawan-cetak',
        ];
        return view('user.penggajian.cetak.payslip-nonkaryawan-cetak', $data);
    }

     /**
     * Display a multi cetak of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function multicetakperiode(Request $request)
    {
        $payroll_uuids = $request->get('payroll_uuids');
        // dd($karyawan_ids);
        if(!is_array($payroll_uuids)) {
            $payroll_uuids = [];
        }
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $payrolls = PayrollModel::with(['wajibpajak','karyawan','karyawan.penggajian'])->where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'payroll_status' => 3,
            'payroll_active' => '1',
        ])
        ->whereIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereIn('payroll_uuid', $payroll_uuids)->get();

        if(count($payrolls) < 1) {
            abort(404);
        }

        if(count($payrolls) > 100) {
            abort(404, 'Download slip tidak boleh lebih dari 100 data.');
        }

        $stpenggajian = SettingPenggajianKaryawanModel::where([
            'stpenggajiankaryawan_active' => 1,
            'stpenggajiankaryawan_isnonemployee' => TRUE,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();

        // dd($payrolls);
        // dd($payroll->karyawan->jabatan->karyawanjabatan_name);
        $data = [
            'title' => 'Cetak Penggajian',
            'payrolls' => $payrolls,
            'stpenggajian' => $stpenggajian,
            'content' => 'user.penggajian.cetak.payslip-nonkaryawan-multicetak',
        ];
        return view('user.penggajian.cetak.payslip-nonkaryawan-multicetak', $data);
        // return view('user.penggajian.cetak.payslip-multiple-cetak', $data);
    }

    public function calculate($karyawanId, Request $request)
    {
        // dd($request->all());
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $stpajkpph21 = SettingPajakPph21Model::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$stpajkpph21) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Pajak PPh21 tidak ditemukan. Silahkan lakukan pengaturan terlebih dahulu!'
            ]);
        }

        $nonkaryawan_tgltrx = Carbon::createFromFormat('d-m-Y', $request->input('nonkaryawan_tgltrx'));
        // dd($nonkaryawan_tgltrx->format('Y-m'));
        $nonkaryawan_tgltrx_ymd = $nonkaryawan_tgltrx->format('Y-m-d');
        $nonkaryawan_tgltrx_dt = $nonkaryawan_tgltrx->translatedFormat('Y-m-d H:i:s');
        $nonkaryawan_pendapatankotor = $request->input('nonkaryawan_pendapatankotor');
        $payroll_uuid = $request->input('nonkaryawan_payroll_uuid');
        $payroll_period = ($request->get('periode')) ? $request->get('periode') : date('m-Y');
        $payroll_periodyearmonth = Carbon::createFromFormat('!m-Y', $payroll_period)->format('Y-m');
        $payroll_periodyear = Carbon::createFromFormat('!m-Y', $payroll_period)->format('Y');
        // $prevmonth = Carbon::createFromFormat('!m-Y', $payroll_period)->subMonths(1);
         // check if month-year greater than current month-year
        if(strtotime($payroll_periodyearmonth) > strtotime(date('Y-m'))) {
            return response()->json([
                'success' => false,
                'message' => 'Tgl. Transaksi tidak boleh melebihi bulan ini.'
            ]);
        }
        
         // BEGIN GET MAX QUOTA FROM SUBSCRIPTION
        $grouppy = PayrollModel::select("ms_karyawan_id")
        ->where([
            'payroll_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id
        ])
        ->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = ?)", [$payroll_periodyearmonth])
        ->groupBy('ms_karyawan_id')->get();
        // dd($grouppy);
        $currentwp = WajibPajakModel::where(['wajibpajak_id' => $wajibpajak_id])->first();
        $decodepermission = json_decode($currentwp->wajibpajaksubscription->wajibpajaksubscription_permission);
        // dd($decodepermission);
        $maxquota = 0;
        foreach($decodepermission as $permit) {
            if($permit->menu_id == 3) {
                $maxquota = $permit->Q;
                break;
            }
        }
        // dd($grouppy);
        // dd($payroll_uuid);
        if(count($grouppy) >= $maxquota) {
            // check if karyawan exist
            $karyawan_exist = false;
            foreach($grouppy as $gpy) {
                if($gpy->ms_karyawan_id == $karyawanId) {
                    $karyawan_exist = true;
                    break;
                }
            }
            
            if(!$karyawan_exist) { // if not exist
                return response()->json([
                    'success' => false,
                    'message' => 'Kuota Kalkulasi Penggajian melebihi batas.'
                ]);
            }
        }
        // END GET MAX QUOTA FROM SUBSCRIPTION
        // dd($prevmonth->format('Y-m'));
        // $escape_periodeyear = DB::connection()->getPdo()->quote($payroll_periodyear);
        $escape_periodyearmonth = DB::connection()->getPdo()->quote($payroll_periodyearmonth);
        $escape_tgltrx = DB::connection()->getPdo()->quote($nonkaryawan_tgltrx_ymd);
        $escape_payroll_uuid = ($payroll_uuid) ? DB::connection()->getPdo()->quote($payroll_uuid) : DB::connection()->getPdo()->quote('-');
        // dd($escape_periodyearmonth);
        $karyawan = KaryawanModel::
            select("ms_karyawan.*"
            , DB::raw("(SELECT COUNT(payroll_id) FROM tr_payroll WHERE ms_karyawan_id = karyawan_id AND payroll_karyawan_status = 'NONKARYAWAN' AND payroll_active='1' LIMIT 1) as payroll_count")
            , DB::raw("(SELECT payroll_trx_at FROM tr_payroll WHERE ms_karyawan_id = karyawan_id 
            AND payroll_karyawan_status = 'NONKARYAWAN' AND payroll_active='1'
            AND TO_CHAR(payroll_period, 'YYYY-MM') = {$escape_periodyearmonth} AND payroll_lock = '1' ORDER BY payroll_trx_at DESC LIMIT 1) as payroll_trx_at_lastlock")
            , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE ms_karyawan_id = karyawan_id 
            AND payroll_karyawan_status = 'NONKARYAWAN' AND payroll_active='1'
            AND TO_CHAR(payroll_period, 'YYYY-MM') < {$escape_periodyearmonth} AND payroll_lock = '0' LIMIT 1) as payroll_prev_islocked")
            , DB::raw("(SELECT pph21_id FROM tr_pph21 WHERE ms_karyawan_id = karyawan_id AND pph21_active='1' AND TO_CHAR(pph21_trx_at, 'YYYY-MM-DD') = {$escape_tgltrx} LIMIT 1) as pph21_exist")
            , DB::raw("(SELECT pph21_dpp FROM tr_pph21 WHERE ms_karyawan_id = karyawan_id AND pph21_active='1' AND TO_CHAR(pph21_trx_at, 'YYYY-MM-DD') = {$escape_tgltrx} AND tr_payroll_uuid = {$escape_payroll_uuid} LIMIT 1) as pph21_dpp_current")
        )
        ->with(['ptkp', 'objekpajak'
            , 'payroll' => function($q) use ($payroll_periodyearmonth, $nonkaryawan_tgltrx_ymd) {
                $q->with(['pph21']);
                $q->where(['payroll_active' => '1']);
                // $q->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = ? AND TO_CHAR(payroll_trx_at, 'YYYY-MM-DD') > ?)", [$payroll_periodyearmonth, $nonkaryawan_tgltrx_ymd]);
                $q->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = ?)", [$payroll_periodyearmonth]);
                $q->orderBy('payroll_trx_at', 'ASC');
            }
        ])
        ->when($payroll_periodyearmonth, function($q, $payroll_periodyearmonth) {
            return $q->whereRaw("(
                CASE WHEN karyawan_contract_end IS NOT NULL
                THEN 
                    TO_CHAR(karyawan_contract_end, 'YYYY-MM') >= ?
                ELSE 
                    karyawan_contract_end IS NULL
                END
            )", [$payroll_periodyearmonth]);
        })
        ->where([
            'karyawan_id' => $karyawanId,
            'ms_wajibpajak_id' => $wajibpajak_id,
            // 'karyawan_active' => '1',
        ])
        ->whereRaw("(TO_CHAR(karyawan_contract_begin, 'YYYY-MM') <= ?)", [$payroll_periodyearmonth])
        ->whereIn('karyawan_status', ['NONKARYAWAN'])
        ->first();

        if(!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan tidak ditemukan.'
            ]);
        }

        if($karyawan->karyawan_active == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Data karyawan sudah tidak aktif.'
            ]);
        }

        // dd($karyawan->payroll_prev_islocked);
        // if($karyawan->payroll_prev_islocked) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Periode penggajian sebelumnya belum di lock!'
        //     ]);
        // }

        if($karyawan->payroll_trx_at_lastlock >= $nonkaryawan_tgltrx_ymd) {
            return response()->json([
                'success' => false,
                'message' => 'Periode penggajian harus lebih besar dari tanggal '.$karyawan->payroll_trx_at_lastlock.'!'
            ]);
        }

        // dd($payroll_periodyearmonth, $nonkaryawan_tgltrx->format('Y-m'));
        if($nonkaryawan_tgltrx->format('Y-m') != $payroll_periodyearmonth) {
            return response()->json([
                'success' => false,
                'message' => 'Periode bulan transaksi tidak boleh lebih besar / kecil dari periode.'
            ]);
        }
        
        if(!$payroll_uuid && $karyawan->pph21_exist) {
            return response()->json([
                'success' => false,
                'message' => 'Tgl transaksi sudah ada. Silahkan gunakan tgl lainnya.'
            ]);
        }

        // dd($karyawan->karyawan_ismultiple);
        
        $isnpwp = ($karyawan->karyawan_npwp == '00.000.000.0-000.000') ? 'NO-NPWP' : 'NPWP';
        $ratenonnpwp = 100;
        // if($isnpwp == 'NO-NPWP') {
        //     $tarif21_nonnpwp = TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first();
        //     $ratenonnpwp = $tarif21_nonnpwp->tarifnonnpwp_rate;
        // }
        $dpp = $nonkaryawan_pendapatankotor * 50 / 100;
        $karyawan->ptkp->ptkp_detail = (object) PtkpDetailModel::where(['ptkpdet_category' => $karyawan->ptkp->ptkp_category, 'ptkpdet_active' => '1'])->orderBy('ptkpdet_rate_month', 'ASC')->get()->toArray();
        $appPph21NonKaryawanLib = new AppPPh21NonKaryawanLibrary();
        $appPph21NonKaryawanLib->karyawan = $karyawan;
        $appPph21NonKaryawanLib->pendapatankotor = $nonkaryawan_pendapatankotor;
        $appPph21NonKaryawanLib->dpp = $dpp;
        $appPph21NonKaryawanLib->ratenonnpwp = $ratenonnpwp;
        $appPph21NonKaryawanLib->tarif21 = Tarif21Model::where(['tarif21_active' => 1])->orderBy('tarif21_startincome', 'ASC')->get()->toArray();
        // $tarif21 = $this->getTarifPPH21($total_dpp);
        // $calculation = $this->_recalculate($nonkaryawan_pendapatankotor, $karyawan);
        // var $tarif21;
        $calculate_pph21 = $appPph21NonKaryawanLib->calculate();
        // $trx[$karyawan->karyawan_id] = [
        //     'trxdate' => $nonkaryawan_tgltrx,
        //     'bruto' => $nonkaryawan_pendapatankotor
        // ];
        // dd($calculate_pph21);
        $calculation = $this->_recalculate($appPph21NonKaryawanLib, $calculate_pph21, $nonkaryawan_tgltrx_dt, $payroll_uuid);
        // dd($payroll_uuid, $calculation);
        
        DB::beginTransaction();
        try {
            $objekpajak_code = $karyawan->objekpajak->objekpajak_code;
            $objekpajak_description = $karyawan->objekpajak->objekpajak_description;
            // dd($objekpajak_code);
            if($karyawan->payroll_count > 0 && $objekpajak_code == '21-100-09') {
                $objekpajak_code = '21-100-08';
                $objekpajak_description = 'Bukan Pegawai yang Menerima Penghasilan yang Bersifat Berkesinambungan.';
                // dd($objekpajak_code);
                KaryawanModel::where(['karyawan_id' => $karyawan->karyawan_id])
                ->update([
                    'karyawan_code_objekpajak' => $objekpajak_code
                ]);

                // begin for log only
                $this->logs([$karyawan], [
                    'karyawan_code_objekpajak' => $objekpajak_code
                ], 'ms_karyawan', 'updated');
                // end for log only
            }

            foreach($calculation['pph21'] as &$pph) {
                $pph['pph21_objekpajak_code'] = $objekpajak_code;
                $pph['pph21_objekpajak_description'] = $objekpajak_description;
                $pph['pph21_pajakpph21_data'] = ($stpajkpph21) ? json_encode($stpajkpph21) : null;
            }
            
            PayrollModel::upsert($calculation['payroll'], 'payroll_uuid');
            PPh21Model::upsert($calculation['pph21'], 'tr_payroll_uuid');

            // begin for log only
            $payroll_uuids = [];
            foreach($calculation['payroll'] as $py) {
                array_push($payroll_uuids, $py['payroll_uuid']);
            }
            
            $payrollforlogs = PayrollModel::whereIn('payroll_uuid', $payroll_uuids)->get();
            $this->logs($payrollforlogs, $calculation['payroll'], 'tr_payroll', 'updated');
            // end for log only

            // begin for log only
            $payroll_uuids = [];
            foreach($calculation['pph21'] as $py) {
                array_push($payroll_uuids, $py['tr_payroll_uuid']);
            }
            $pph21forlogs = PPh21Model::whereIn('tr_payroll_uuid', $payroll_uuids)->get();
            $this->logs($pph21forlogs, $calculation['pph21'], 'tr_pph21', 'updated');
            // end for log only

            

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kalkulasi penggajian karyawan berhasil disimpan.'
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($payrollUuid, Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $payroll = PayrollModel::select('payroll_uuid', 'payroll_period', 'payroll_trx_at'
        , 'ms_karyawan_id')
        ->where([
            'payroll_uuid' => $payrollUuid,
            'payroll_active' => 1,
            'payroll_lock' => 0,
            'payroll_karyawan_status' => 'NONKARYAWAN',
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$payroll) {
            return response()->json([
                'success' => false,
                'message' => 'Penggajian karyawan tidak ditemukan!'
            ]); 
        }
        // dd($payroll);
        DB::beginTransaction();
        try {
            $deletedpayroll = PayrollModel::where(['payroll_uuid' => $payrollUuid])->first();
            $deletedpph21 = PPh21Model::where(['tr_payroll_uuid' => $payrollUuid])->first();
            // PayrollModel::where(['payroll_uuid' => $payrollUuid])->update(['payroll_active' => 0]);
            // PPh21Model::where(['tr_payroll_uuid' => $payrollUuid])->update(['pph21_active' => 0]);
            $deletedpayroll->update(['payroll_active' => 0]);
            $deletedpph21->update(['pph21_active' => 0]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Penggajian karyawan berhasil dihapus',
                'data' => $payroll
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function importPenggajian(Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        // dd($request->all());
        if (!$request->hasfile('file')) {
            $res["success"] = false;
            $res["message"] = 'Data excel wajib diisi!';
            return response()->json($res, 500);
        }
    
        // Get the uploaded file
        $file = $request->file('file');

        if(!in_array($file->extension(), ['xls','xlsx','csv'])) {
            $res["success"] = false;
            $res["message"] = 'Format harus xls, xlsx atau csv!';
            return response()->json($res, 500);
        }

        // BEGIN GET MAX QUOTA FROM SUBSCRIPTION
        $currentwp = WajibPajakModel::where(['wajibpajak_id' => $wajibpajak_id])->first();
        $decodepermission = json_decode($currentwp->wajibpajaksubscription->wajibpajaksubscription_permission);
        // dd($decodepermission);
        $maxquota = 0;
        foreach($decodepermission as $permit) {
            if($permit->menu_id == 3) { // max karyawan
                $maxquota = $permit->Q;
                break;
            }
        }
        // dd($maxquota);
        // END GET MAX QUOTA FROM SUBSCRIPTION
    
        $originalFileName = $file->getClientOriginalName();

        $filename = rand().'.xlsx';
        $file->move(public_path('uploads/'), $filename);
        
        $path = public_path('uploads/'.$filename);

        $inputFileType = IOFactory::identify($path);
        $reader = IOFactory::createReader($inputFileType);
        $worksheetData = $reader->listWorksheetInfo($path);

        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
    
        // Get the Sheet By Name
        $sheet = $spreadsheet->getSheetByName('Impor Transaksi');
        if(!$sheet) {
            unlink(public_path('uploads/'). $filename); // remove file
            $res["success"] = false;
            $res["message"] = 'Sheet Impor Transaksi tidak ditemukan!';
            return response()->json($res, 500);
        }
        $maxRows = $sheet->getHighestRow();

        $chunkFilter = new ChunkReadFilterLibrary();
        $reader->setReadFilter($chunkFilter);

        $chunkSize = 100; // read as chunk
        $startRow = 1; // mulai baris ke 1;

        $result = [];
        $totaltrxs = [];
        $overquota = null;
        $duplicate_date = [];
        $temp_date = [];
        for ($startRow; $startRow <= $maxRows; $startRow += $chunkSize) {
            $chunkFilter = new ChunkReadFilterLibrary($startRow, $chunkSize);
            // // Tell the Read Filter, the limits on which rows we want to read this iteration
            $chunkFilter->setRows($startRow, $chunkSize);
            // Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
            $spreadsheet_chunk = $reader->load($path);
            $nb = $startRow;
            $max_chunk = $startRow + $chunkSize;

            for($nb; $nb<=$max_chunk; $nb++) {
                $nbi = $nb + 1;
                $cellID = $sheet->getCell("A" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $cellDate = $sheet->getCell("B" . $nbi)->getValue();
                $cellBruto = intval($sheet->getCell("C" . $nbi)->getValue());

                if($cellID === null || $cellDate === null || $cellBruto === 0) {
                    break;
                }

                // if(date('d-m-Y', strtotime($cellDate)) != date($cellDate)) {
                //     $wrongdateformat = true;
                //     break;
                // }

                $columnB = $cellDate;
                $columnC = $cellBruto;

                if(date('d-m-Y', strtotime($cellDate)) != date($cellDate)) {
                    $periodyearmonth = '00-0000';
                } else {
                    // check if month-year greater than current month-year
                    $periodyearmonth = Carbon::createFromFormat('d-m-Y', $cellDate)->format('m-Y');
                }
                // if(strtotime($periodyearmonth) > strtotime(date('Y-m'))) {
                //     break;
                // }

                // if(count($karyawantotal) >= $maxquota) {
                //     $ismaxquota = true;
                //     break;                    
                // }

                if(!isset($totaltrxs[$periodyearmonth])) {
                    $totaltrxs[$periodyearmonth] = [];
                    array_push($totaltrxs[$periodyearmonth], $cellID);
                } else {
                    if(!in_array($cellID, $totaltrxs[$periodyearmonth])) {
                        array_push($totaltrxs[$periodyearmonth],$cellID);
                    }
                }
                
                $result[] = [
                    "karyawan_enid" => $cellID,
                    "date" => $columnB,
                    "bruto" => $columnC,
                    'row' => $nb + 1
                ];
                
                if(isset($temp_date[$cellID]) && in_array($columnB, $temp_date[$cellID])) {
                    $duplicate_date[] = $columnB;
                } else {
                    $temp_date[$cellID][] = $columnB;
                }
            }

            $spreadsheet_chunk->__destruct();
            $spreadsheet_chunk = null;
            unset($spreadsheet_chunk);
            // if($ismaxquota) {
            //     break;
            // }
        }
        // if($wrongdateformat) {
        //     $res["success"] = false;
        //     $res["message"] = 'Format tanggal tidak sesuai. Pastikan format tanggal dd-mm-yyyy';
        //     return response()->json($res);
        // }

        // then release the memory
        $spreadsheet->__destruct();
        $spreadsheet = null;
        unset($spreadsheet);
        $reader = null;
        unset($reader);
        unlink(public_path('uploads/'). $filename); // remove file
        // dd($result);
        if(count($totaltrxs)) {
            foreach($totaltrxs as $trxdate => $trxenid) {
                // dd(count($trxenid));
                if(count($trxenid) > $maxquota) {
                    $overquota = ['date' => $trxdate, 'id' => $trxenid];
                    break;
                }
            }
        }
        if($overquota) {
            $res["success"] = false;
            $res["message"] = 'Transaksi periode <b>'.Carbon::createFromFormat('m-Y', $overquota['date'])->translatedFormat('F Y').'</b> melebihi kuota!';
            return response()->json($res);
        }
        
        if(count($duplicate_date) > 0)
        {
            // Array has duplicates
            $res["success"] = false;
            $res["message"] = 'Tanggal transaksi '.implode(', ', $duplicate_date).' tidak boleh sama untuk karyawan yang sama!';
            return response()->json($res);
        }

        // dd($overquota);
        $exist_result = [];
        $nonexist_result = [];
        foreach($result as $res) {
            // exist data;
            $karyawan_enid = $res['karyawan_enid'];
            // dd($res['date']);
            if(date('d-m-Y', strtotime($res['date'])) != date($res['date'])) {
                $karyawan_tgltrx_ymd = '0000-00-00';
                $payroll_period = '00-0000';
                $payroll_periodyearmonth = '0000-00';
                $payroll_periodyear = '0000';
                $escape_periodyearmonth = DB::connection()->getPdo()->quote('0000-00');
                $escape_tgltrx = DB::connection()->getPdo()->quote('0000-00-00');

                $trxdate = $res['date'] . ' 00:00:00';
                $trxperiod = '0000/00';

            } else {
                $karyawan_tgltrx_ymd = Carbon::createFromFormat('d-m-Y', $res['date'])->format('Y-m-d');
                $payroll_period = Carbon::createFromFormat('d-m-Y', $res['date'])->format('m-Y');
                $payroll_periodyearmonth = Carbon::createFromFormat('!m-Y', $payroll_period)->format('Y-m');
                $payroll_periodyear = Carbon::createFromFormat('!m-Y', $payroll_period)->format('Y');
                $escape_periodyearmonth = DB::connection()->getPdo()->quote($payroll_periodyearmonth);
                $escape_tgltrx = DB::connection()->getPdo()->quote($karyawan_tgltrx_ymd);

                $trxdate = Carbon::createFromFormat('d-m-Y', $res['date'])->format('Y-m-d H:i:s');
                $trxperiod = Carbon::createFromFormat('d-m-Y', $res['date'])->format('Y-m');
            }
            $escape_payroll_uuid = DB::connection()->getPdo()->quote('-');
            // dd($escape_tgltrx);
            // dd($escape_periodyearmonth);
            $karyawan = KaryawanModel::
                select("ms_karyawan.*"
                , DB::raw("(SELECT COUNT(payroll_id) FROM tr_payroll WHERE ms_karyawan_id = karyawan_id AND payroll_karyawan_status = 'NONKARYAWAN' AND payroll_active='1' LIMIT 1) as payroll_count")
                , DB::raw("(SELECT payroll_trx_at FROM tr_payroll WHERE ms_karyawan_id = karyawan_id 
            AND payroll_karyawan_status = 'NONKARYAWAN' AND payroll_active='1'
            AND TO_CHAR(payroll_period, 'YYYY-MM') = {$escape_periodyearmonth} AND payroll_lock = '1' ORDER BY payroll_trx_at DESC LIMIT 1) as payroll_trx_at_lastlock")
                , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE ms_karyawan_id = karyawan_id 
                AND payroll_karyawan_status = 'NONKARYAWAN' AND payroll_active='1'
                AND TO_CHAR(payroll_period, 'YYYY-MM') < {$escape_periodyearmonth} AND payroll_lock = '0' LIMIT 1) as payroll_prev_islocked")
                , DB::raw("(SELECT pph21_id FROM tr_pph21 WHERE ms_karyawan_id = karyawan_id AND pph21_active='1' AND TO_CHAR(pph21_trx_at, 'YYYY-MM-DD') = {$escape_tgltrx} LIMIT 1) as pph21_exist")
                , DB::raw("(SELECT pph21_dpp FROM tr_pph21 WHERE ms_karyawan_id = karyawan_id AND pph21_active='1' AND TO_CHAR(pph21_trx_at, 'YYYY-MM-DD') = {$escape_tgltrx} AND tr_payroll_uuid = {$escape_payroll_uuid} LIMIT 1) as pph21_dpp_current")
            )
            ->with(['ptkp', 'objekpajak'
                , 'payroll' => function($q) use ($payroll_periodyearmonth, $karyawan_tgltrx_ymd) {
                    $q->with(['pph21']);
                    $q->where(['payroll_active' => '1']);
                    // $q->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = ? AND TO_CHAR(payroll_trx_at, 'YYYY-MM-DD') > ?)", [$payroll_periodyearmonth, $karyawan_tgltrx_ymd]);
                    $q->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = ?)", [$payroll_periodyearmonth]);
                    $q->orderBy('payroll_trx_at', 'ASC');
                }
            ])
            ->when($payroll_periodyearmonth, function($q, $payroll_periodyearmonth) {
                return $q->whereRaw("(
                    CASE WHEN karyawan_contract_end IS NOT NULL
                    THEN 
                        TO_CHAR(karyawan_contract_end, 'YYYY-MM') >= ?
                    ELSE 
                        karyawan_contract_end IS NULL
                    END
                )", [$payroll_periodyearmonth]);
            })->where([
                'karyawan_enid' => $karyawan_enid,
                'karyawan_active' => '1',
                'ms_wajibpajak_id' => $wajibpajak_id,
                // 'payroll_active' => '1'
            ])
            ->whereRaw("(TO_CHAR(karyawan_contract_begin, 'YYYY-MM') <= ?)", [$payroll_periodyearmonth])
            ->whereIn('karyawan_status', ['NONKARYAWAN'])
            ->first();

            if($karyawan) {
                $exist_result[] = [
                    'karyawan_enid' => $karyawan_enid,
                    'trxdate' => $trxdate,
                    'trxperiod' => $trxperiod,
                    'bruto' => $res['bruto'],
                    'karyawan' => $karyawan,
                ];
                
            } else {
                array_push($nonexist_result, [
                    'karyawan_enid' => $karyawan_enid,
                    'trxdate' => $trxdate,
                    'trxperiod' => $trxperiod,
                    'bruto' => $res['bruto'],
                    'karyawan' => null,
                ]);
            }
        }
        
        // dd($nonexist_result, $exist_result);

        // if(count($nonexist_result) > 0) {
        //     $res["success"] = false;
        //     $res["message"] = 'Karyawan dengan ID berikut tidak ditemukan!<br>'.implode(', ', $nonexist_result);
        //     return response()->json($res);
        // }
        $noixr = 0;
        $ixr = 0;
        $totalComplete = 0;
        $totalFail = 0;
        
        foreach($nonexist_result as $noexresult) {
            unset($nonexist_result[$ixr]['karyawan']);
            $nonexist_result[$noixr]['status'] = 'Gagal';
            $nonexist_result[$noixr]['remark'] = 'Karyawan tidak ditemukan pada periode ini!';
               
            if(date('Y-m', strtotime($noexresult['trxperiod'])) != date($noexresult['trxperiod'])) {
                $nonexist_result[$noixr]['remark'] .= ' Format tanggal harus dd-mm-yyyy!';
            }
            $noixr++;
            $totalFail++;
        }
            
        $appPph21NonKaryawanLib = new AppPPh21NonKaryawanLibrary();
        $appPph21NonKaryawanLib->tarif21 = Tarif21Model::where(['tarif21_active' => 1])->get();
        $karyawans = [];
        $calculation = [
            'payroll' => [],
            'pph21' => [],
        ];

        
        // dd($exist_result);
        // sort by date
        usort($exist_result, function($a, $b) {
            return $a['trxdate'] <=> $b['trxdate'];
        });
        // dd($exist_result);

        // $ix=0;
        $trxkaryawan = [];
        foreach($exist_result as $exresult) {

            $karyawan = $exresult['karyawan'];
            // dd($karyawan);
            $karyawan_tgltrx = Carbon::parse($exresult['trxdate']);
            $nonkaryawan_pendapatankotor = $exresult['bruto'];
            $karyawan_period = $exresult['trxperiod'];
            // remove karyawan array
            unset($exist_result[$ixr]['karyawan']);
            
            if($karyawan_period > date('Y-m')) {
                
                $exist_result[$ixr]['status'] = 'Gagal';
                $exist_result[$ixr]['remark'] = 'Periode penggajian tidak boleh lebih dari periode bulan ini!';
                $ixr++;
                $totalFail++;
                continue;
            }

            // if($karyawan->payroll_prev_islocked) {
            //     dd('xxxx',$karyawan);
            //     $exist_result[$ixr]['status'] = 'Gagal';
            //     $exist_result[$ixr]['remark'] = 'Periode penggajian sebelumnya belum di lock!';
            //     $ixr++;
            //     $totalFail++;
            //     continue;
            // }
    
            // if($karyawan->payroll_trx_at_lastlock) {
            //     dd('yyyy',$karyawan);
            //     $exist_result[$ixr]['status'] = 'Gagal';
            //     $exist_result[$ixr]['remark'] = 'Periode penggajian harus lebih besar dari tanggal '.$karyawan->payroll_trx_at_lastlock.'!';
            //     $ixr++;
            //     $totalFail++;
            //     continue;
            // }
            
            if($karyawan->pph21_exist) {
                $exist_result[$ixr]['status'] = 'Gagal';
                $exist_result[$ixr]['remark'] = 'Tgl transaksi '.$karyawan_tgltrx->format('d-m-Y').' sudah ada. Silahkan gunakan tgl lainnya.';
                $ixr++;
                $totalFail++;
                continue;
            }

            $exist_result[$ixr]['status'] = 'Sukses';
            $exist_result[$ixr]['remark'] = '-';

            $trxkaryawan[$karyawan->karyawan_id][] = [
                'trxdate' => $karyawan_tgltrx->format('Y-m-d H:i:s'),
                'bruto' => $nonkaryawan_pendapatankotor,
                'karyawan' => $karyawan,
            ];
            $karyawans[] = $karyawan;

            $ixr++;
            $totalComplete++;
        }
        // dd($trxkaryawan);
        foreach($trxkaryawan as $ktrx) {
            // dd($ktrx);
            foreach($ktrx as $krid => $trx) {
                $trxdate = $trx['trxdate'];
                $nonkaryawan_pendapatankotor = $trx['bruto'];
                $karyawan = $trx['karyawan'];
                $isnpwp = ($karyawan->karyawan_npwp == '00.000.000.0-000.000') ? 'NO-NPWP' : 'NPWP';
                $ratenonnpwp = 100;
                // if($isnpwp == 'NO-NPWP') {
                //     $tarif21_nonnpwp = TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first();
                //     $ratenonnpwp = $tarif21_nonnpwp->tarifnonnpwp_rate;
                // }
                $dpp = $nonkaryawan_pendapatankotor * 50 / 100;
                $karyawan->ptkp->ptkp_detail = (object) PtkpDetailModel::where(['ptkpdet_category' => $karyawan->ptkp->ptkp_category, 'ptkpdet_active' => '1'])->orderBy('ptkpdet_rate_month', 'ASC')->get()->toArray();
                $appPph21NonKaryawanLib = new AppPPh21NonKaryawanLibrary();
                $appPph21NonKaryawanLib->karyawan = $karyawan;
                $appPph21NonKaryawanLib->pendapatankotor = $nonkaryawan_pendapatankotor;
                $appPph21NonKaryawanLib->dpp = $dpp;
                $appPph21NonKaryawanLib->ratenonnpwp = $ratenonnpwp;
                $appPph21NonKaryawanLib->tarif21 = Tarif21Model::where(['tarif21_active' => 1])->orderBy('tarif21_startincome', 'ASC')->get()->toArray();
                $calculate_pph21 = $appPph21NonKaryawanLib->calculate();
                $calculate = $this->_recalculate($appPph21NonKaryawanLib, $calculate_pph21, $trxdate);     
                $calculation['payroll'] = array_merge($calculation['payroll'], $calculate['payroll']);
                $calculation['pph21'] = array_merge($calculation['pph21'], $calculate['pph21']);
            }
        }

        DB::beginTransaction();
        
        try {

            $karyawan_objekpajak = [];
            foreach($karyawans as $karyawan) {
                $objekpajak_code = $karyawan->objekpajak->objekpajak_code;
                $objekpajak_description = $karyawan->objekpajak->objekpajak_description;

                if($karyawan->payroll_count > 0 && $objekpajak_code == '21-100-09') {
                    $objekpajak_code = '21-100-08';
                    $objekpajak_description = 'Bukan Pegawai yang Menerima Penghasilan yang Bersifat Berkesinambungan.';
                    
                    array_push($karyawan_objekpajak, [
                        'karyawan_code_objekpajak' => $objekpajak_code,
                        'karyawan_id' => $karyawan->karyawan_id
                    ]);
                }
            }
            foreach($calculation['pph21'] as &$pph) {
                $pph['pph21_objekpajak_code'] = $objekpajak_code;
                $pph['pph21_objekpajak_description'] = $objekpajak_description;
            }

            // dd('ooi',$calculation);
            PayrollModel::upsert($calculation['payroll'], 'payroll_uuid');

            PPh21Model::upsert($calculation['pph21'], 'tr_payroll_uuid');

            // begin for log only
            $payroll_uuids = [];
            foreach($calculation['payroll'] as $py) {
                array_push($payroll_uuids, $py['payroll_uuid']);
            }
            
            $payrollforlogs = PayrollModel::whereIn('payroll_uuid', $payroll_uuids)->get();
            $this->logs($payrollforlogs, $calculation['payroll'], 'tr_payroll', 'updated');
            // end for log only

            // begin for log only
            $payroll_uuids = [];
            foreach($calculation['pph21'] as $py) {
                array_push($payroll_uuids, $py['tr_payroll_uuid']);
            }
            $pph21forlogs = PPh21Model::whereIn('tr_payroll_uuid', $payroll_uuids)->get();
            $this->logs($pph21forlogs, $calculation['pph21'], 'tr_pph21', 'updated');
            // end for log only


            if(count($karyawan_objekpajak) > 0) {
                KaryawanModel::upsert($karyawan_objekpajak, 'karyawan_id');

                // begin for log only
                $this->logs($karyawans, $karyawan_objekpajak, 'ms_karyawan', 'updated');
                // end for log only
            }

            $history_result = array_merge($nonexist_result, $exist_result);
            // dd($history_result);
            $temp_historydata = [
                'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                'ms_user_id' => session()->get('user_data')['user_id'],
                'historyimport_type' => "PAYROLL_NONEMPLOYEE",
                'historyimport_date' => date('Y-m-d H:i:s'),
                'historyimport_detail_import' => $totalComplete . ' Sukses, ' . $totalFail . ' Gagal', 
                'historyimport_content' => json_encode($history_result),
                'historyimport_file_name' => $originalFileName
            ];
            $createHistory = HistoryImportModel::create($temp_historydata);
            // begin for log only
            $this->logs($createHistory, $temp_historydata, 'tr_history_import', 'created');
            // end for log only

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Import Penggajian Non Karyawan berhasil disimpan.',
                'result' => $history_result,
                'totalsuccess' => $totalComplete,
                'totalfail'=> $totalFail,
                'totaldata' => $totalComplete + $totalFail,
                'history' => $createHistory->historyimport_id
            ]);
            

        } catch(Error $e) {
            unlink(public_path('uploads/'). $filename); // remove file
            Log::error('Error occurred: ' . $e->getMessage());

            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function generateHistory(Request $request) 
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $id = $request->input('id');
        try {
            $getHistory = HistoryImportModel::where([
                'historyimport_id' => $id, 
                'ms_wajibpajak_id' => $wajibpajak_id
            ])->first();
            if(!$getHistory) {
                // return response()->json([
                //     'success' => false,
                //     'message' => 'History tidak ditemukan'
                // ]);
                abort(404, 'History tidak ditemukan');
            }
            $content = json_decode($getHistory['historyimport_content']);
            if(!$content) {
                abort(404, 'Data tidak ditemukan');
            }
            // Get the WajibPajakModel data based on the wajibpajak_id
            $nonkaryawans = KaryawanModel::select('karyawan_id', 'karyawan_enid', 'karyawan_name')
            ->where([
                'ms_wajibpajak_id' => $wajibpajak_id,
                'karyawan_active' => '1', 
                'karyawan_status' => 'NONKARYAWAN'
            ])
            ->orderBy('karyawan_enid', 'ASC')
            ->get();
            
            $excelFilePath = public_path('assets/import/Template_Import_Transaksi_NonKaryawan.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $unixtime = time();
            $title = 'Riwayat_Impor_Transaksi_NonKaryawan_'.$unixtime;

            $spreadsheet = $reader->load($excelFilePath);

            $sheetTemplateNonKaryawan = $spreadsheet->getSheetByName('Referensi Data Non Karyawan');
            // $sheetEmployee = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Referensi Data Non Karyawan');
            // $spreadsheet->addSheet($sheetEmployee);
            // $sheetTemplateNonKaryawan = $spreadsheet->createSheet();
            // $sheetTemplateNonKaryawan->setTitle('Referensi Data Non Karyawan');
            // $sheetTemplateNonKaryawan->setCellValue('A1', 'Id Karyawan');
            // $sheetTemplateNonKaryawan->setCellValue('B1', 'Nama Non Karyawan');
            
            $rowIndex = 2;
            foreach($nonkaryawans as $item) {
                $listnonkaryawan = [
                    'A' => $item->karyawan_enid,
                    'B' => $item->karyawan_name,
                ];

                foreach ($listnonkaryawan as $column => $value) {
                    $sheetTemplateNonKaryawan->setCellValueExplicit($column . $rowIndex, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }

                // Increment the row index for the next row of data
                $rowIndex++;
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
            
            $sheetTemplate = $spreadsheet->getSheetByName('Impor Transaksi');
            // $sheetTemplate = $spreadsheet->createSheet();
            // $sheetTemplate->setTitle('Impor Transaksi');
            // $sheetTemplate->setCellValue('A1', 'Id Karyawan*');
            // $sheetTemplate->setCellValue('B1', 'Tanggal Transaksi*');
            // $sheetTemplate->setCellValue('C1', 'Penghasilan Bruto*');
            $sheetTemplate->setCellValue('D1', 'Status');
            $sheetTemplate->setCellValue('E1', 'Catatan');

            for ($col = 'A'; $col <= 'E'; $col++) {
                $sheetTemplate->getStyle($col . '1')->applyFromArray($boldFontStyle);
                $sheetTemplate->getStyle($col . ':' . $col)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            }
           
            $rowIndex = 2;
            foreach($content as $item) {
                $listtrx = [
                    'A' => $item->karyawan_enid,
                    'B' => Carbon::parse($item->trxdate)->format('d-m-Y'),
                    'C' => $item->bruto,
                    'D' => isset($item->status) ? $item->status : 'Sukses',
                    'E' => isset($item->remark) ? $item->remark : '-',
                ];

                foreach ($listtrx as $column => $value) {
                    $sheetTemplate->setCellValueExplicit($column . $rowIndex, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }

                // Increment the row index for the next row of data
                $rowIndex++;
            }
         
            $writer = new Xls($spreadsheet);

            $filename = $title.'.xls';
            ob_end_clean();
            header("Content-Disposition: attachment; filename=".$filename);
            exit($writer->save('php://output'));
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function generateTemplate(Request $request) 
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        try {
            // Get the WajibPajakModel data based on the wajibpajak_id
            $nonkaryawans = KaryawanModel::select('karyawan_id', 'karyawan_enid', 'karyawan_name')
            ->where([
                'ms_wajibpajak_id' => $wajibpajak_id,
                'karyawan_active' => '1', 
                'karyawan_status' => 'NONKARYAWAN'
            ])
            ->orderBy('karyawan_enid', 'ASC')
            ->get();
            
            $excelFilePath = public_path('assets/import/Template_Import_Transaksi_NonKaryawan.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $unixtime = time();
            $title = 'Templat_Impor_Transaksi_NonKaryawan_'.$unixtime;

            $spreadsheet = $reader->load($excelFilePath);

            $sheetTemplateNonKaryawan = $spreadsheet->getSheetByName('Referensi Data Non Karyawan');

            $rowIndex = 2;
            foreach($nonkaryawans as $item) {
                $listnonkaryawan = [
                    'A' => $item->karyawan_enid,
                    'B' => $item->karyawan_name,
                ];

                foreach ($listnonkaryawan as $column => $value) {
                    $sheetTemplateNonKaryawan->setCellValueExplicit($column . $rowIndex, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }

                // Increment the row index for the next row of data
                $rowIndex++;
            }
         
            $writer = new Xls($spreadsheet);

            $filename = $title.'.xls';
            ob_end_clean();
            header("Content-Disposition: attachment; filename=".$filename);
            exit($writer->save('php://output'));
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importHistory(Request $request)
    {
        $limit = ($request->get('length')) ? intval($request->get('length')) : 10;
        $offset = ($request->get('start')) ? intval($request->get('start')) : 0;
        // Get the start_date and end_date inputs
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        
        $where = [
            'tr_history_import.ms_wajibpajak_id' => $wajibpajak_id,
            'historyimport_type' => 'PAYROLL_NONEMPLOYEE'
        ];
        $escape_start_date = DB::connection()->getPdo()->quote($start_date);
        $escape_end_date = DB::connection()->getPdo()->quote($end_date);
        $list = HistoryImportModel::where($where)
            ->whereRaw("(TO_CHAR(tr_history_import.historyimport_date, 'YYYY-MM-DD') BETWEEN {$escape_start_date} AND {$escape_end_date})")
            ->orderBy('historyimport_date', 'DESC')
            ->leftjoin('mr_user_wajib_pajak', 'tr_history_import.ms_user_id', '=', 'mr_user_wajib_pajak.ms_user_id')
            ->select('tr_history_import.historyimport_date', 'tr_history_import.ms_user_id'
            , 'tr_history_import.historyimport_status', 'tr_history_import.historyimport_id'
            , 'tr_history_import.historyimport_file_name'
            , 'tr_history_import.historyimport_detail_import'
            , 'mr_user_wajib_pajak.userwajibpajak_name')
            ->take($limit)->skip($offset)->get();

        $data = [
            'success' => true,
            'message' => 'Berhasil mengambil data History.',
            'data' => $list,
            'draw' => $request->input('draw'),
            'recordsTotal' => HistoryImportModel::where($where)
            ->whereRaw("(TO_CHAR(tr_history_import.historyimport_date, 'YYYY-MM-DD') BETWEEN {$escape_start_date} AND {$escape_end_date})")
            ->count(),
        ];
		$data['recordsFiltered'] = $data['recordsTotal'];
        return response()->json($data, 200);
    }

    

    function _recalculate($appPph21NonKaryawanLib, $calculate_pph21, $trx_date, $payroll_uuid=null)
    {
        // $method = $karyawan->karyawan_calculation_method;
        // $ptkp = ($karyawan->ptkp) ? json_decode($karyawan->ptkp) : null;
        // // dd($current_payroll_uuid);
        // $allpayroll = $karyawan->payroll->toArray();
        
        // // dd($allpayroll);
        // foreach($trx as $itrx) {
        //     $bruto = $itrx['bruto'];
        //     $karyawan_tgltrx_ymd = $itrx['trxdate']->format('Y-m-d');
        //     $karyawan_tgltrx_dt = $itrx['trxdate']->translatedFormat('Y-m-d H:i:s');
            
        //     // sort payroll by date
        //     usort($allpayroll, function($a, $b) {
        //         $adt = Carbon::parse($a['payroll_trx_at'])->format('Y-m-d');
        //         $bdt = Carbon::parse($b['payroll_trx_at'])->format('Y-m-d');
        //         return $adt <=> $bdt;
        //     });
        //     // insert current trx in the payroll
        //     $interval = [];
        //     $idx = 0;
        //     $currentedit_idx = 0;
        //     // dd($allpayroll);
        //     foreach($allpayroll as $pyroll) {
        //         $pdt = Carbon::parse($pyroll['payroll_trx_at'])->format('Y-m-d');
        //         $interval[] = abs(strtotime($pdt) - strtotime($karyawan_tgltrx_ymd));

        //         if($current_payroll_uuid && $current_payroll_uuid == $pyroll['payroll_uuid']) {
        //             $currentedit_idx = $idx;
        //         }
        //         $idx++;
        //     }
        //     // dd($interval);
        //     asort($interval);
        //     $closest = key($interval);

        //     // bug here if edit
        //     if(!$current_payroll_uuid) {
        //         // dd($closest);
        //         $allpayroll = $this->insertArrayAtPosition($allpayroll, [
        //             'payroll_uuid' => 'NEW', 
        //             'payroll_trx_at' => $karyawan_tgltrx_dt, 
        //             'payroll_basic_salary' => $bruto,
        //             'payroll_period' => $karyawan_tgltrx_ymd,
        //         ], $closest);
        //     }
        //     else {
        //         $allpayroll[$currentedit_idx]['payroll_trx_at'] = $karyawan_tgltrx_dt;
        //         $allpayroll[$currentedit_idx]['payroll_basic_salary'] = $bruto;
        //         $allpayroll[$currentedit_idx]['payroll_period'] = $karyawan_tgltrx_ymd;
        //     }
        // }
        // // sort payroll by date
        // usort($allpayroll, function($a, $b) {
        //     $adt = Carbon::parse($a['payroll_trx_at'])->format('Y-m-d');
        //     $bdt = Carbon::parse($b['payroll_trx_at'])->format('Y-m-d');
        //     return $adt <=> $bdt;
        // });
        // // dd($allpayroll);

        // $appPph21NonKaryawanLib = new AppPPh21NonKaryawanLibrary();
        // dd($appPph21NonKaryawanLib->karyawan);
        $karyawan = $appPph21NonKaryawanLib->karyawan;
        $temp_data_all = [];
        $temp_data_pph21_all = [];
        // $total_dpp_kumulatif = 0;
        // dd($allpayroll);
        // foreach($allpayroll as $pyroll) {
        //     $karyawan_tgltrx_date = $pyroll['payroll_trx_at'];
        //     $payroll_period = $pyroll['payroll_period'];
        //     $karyawan_pendapatanbruto = $pyroll['payroll_basic_salary'];
            if(!$payroll_uuid) {
                $uuid = 'RK-'.$karyawan->ms_wajibpajak_id.'-'.Uuid::uuid4()->toString();
            } else {
                $uuid = $payroll_uuid;
            }
            // $calculate_pph21 = $appPph21NonKaryawanLib->calculate($appPph21NonKaryawanLib, $pendapatankotor, $karyawan);
            // dd($calculate_pph21);
            $payroll_period = date('Y-m-d', strtotime($trx_date));
            $method = $calculate_pph21['metode'];
            $dpp = $calculate_pph21['total_dpp'];
            $pph21 = $calculate_pph21['total_pph_terutang'];
            $total_bruto = $calculate_pph21['total_bruto'];
            $ptkp = $calculate_pph21['ptkp'];
            $ptkp_detail = $calculate_pph21['ptkp_detail'];
            if($method == 'GROSS') {
                $netto = $calculate_pph21['total_bruto'] - $pph21;
                $allowance_pph21 = 0;
                $income = $netto + $allowance_pph21 + $pph21;
            } else {
                $netto = $calculate_pph21['total_bruto'];
                $allowance_pph21 = $pph21;
                $income = $netto + $allowance_pph21;
            }
            
            // $income = $netto + $allowance_pph21;
            $outcome = $pph21;
            
            // $payroll_method = $method;
            
            // if(!isset($pyroll['payroll_lock']) || $pyroll['payroll_lock'] == 0) {
                $temp_data = [
                    'payroll_uuid' => $uuid,
                    'ms_user_id' => $karyawan->wajibpajak->ms_user_id,
                    'ms_wajibpajak_id' => $karyawan->ms_wajibpajak_id,
                    'ms_karyawan_id' => $karyawan->karyawan_id,
                    'payroll_period' => $payroll_period,
                    'payroll_total_netto' => floor($netto),
                    'payroll_total_income' => floor($income),
                    'payroll_total_outcome' => floor($outcome),
                    'payroll_basic_salary' => floor($total_bruto),
                    'payroll_prorate_salary' => floor($total_bruto),
                    'payroll_deduction_pph21' => floor($pph21),
                    'payroll_allowance_pph21' => floor($allowance_pph21),
                    'payroll_allowance_bpjskes' => 0,
                    'payroll_allowance_bpjstk' => 0,
                    'payroll_deduction_bpjskes' => 0,
                    'payroll_deduction_bpjstk' => 0,
                    'payroll_bpjskes_paidbycompany' => 0,
                    'payroll_bpjstk_paidbycompany' => 0,
                    'payroll_method' => $method,
                    'payroll_status' => 1,
                    'payroll_karyawan_enid' => $karyawan->karyawan_enid,
                    'payroll_karyawan_name' => $karyawan->karyawan_name,
                    'payroll_karyawan_address' => $karyawan->karyawan_address,
                    'payroll_karyawan_nik' => $karyawan->karyawan_nik,
                    'payroll_karyawan_npwp' => ($karyawan->karyawan_npwp) ? $karyawan->karyawan_npwp : '-',
                    'payroll_karyawan_gender' => $karyawan->karyawan_gender,
                    'payroll_karyawan_status' => $karyawan->karyawan_status,
                    'payroll_trx_at' => $trx_date,
                ];
                $temp_data_pph21 = [
                    'tr_payroll_uuid' => $temp_data['payroll_uuid'],
                    'ms_user_id' => $temp_data['ms_user_id'],
                    'ms_wajibpajak_id' => $temp_data['ms_wajibpajak_id'],
                    'ms_karyawan_id' => $temp_data['ms_karyawan_id'],
                    'ms_ptkp_id' => ($ptkp) ? $ptkp['ptkp_id'] : 0,
                    'pph21_ptkp_description' => ($ptkp) ? $ptkp['ptkp_description'] : null,
                    'pph21_ptkp_rate' => ($ptkp) ? $ptkp['ptkp_rate'] : null,
                    'pph21_ptkp_category' => $ptkp['ptkp_category'],
                    'ms_ptkpdet_id' => ($ptkp_detail) ? $ptkp_detail['ptkpdet_id'] : 0,
                    'pph21_ptkpdet_rate_percentage' => ($ptkp_detail) ? floatval($ptkp_detail['ptkpdet_rate_percentage']) : null,
                    'pph21_ptkpdet_rate_nominal' => ($ptkp_detail) ? intval($ptkp_detail['ptkpdet_rate_month']) : null,
                    'pph21_method' => $temp_data['payroll_method'],
                    'pph21_period' => $temp_data['payroll_period'],
                    'pph21_basic_salary' => $temp_data['payroll_basic_salary'],
                    'pph21_prorate_salary' => $temp_data['payroll_prorate_salary'],
                    'pph21_trx_at' => $temp_data['payroll_trx_at'],
                    'pph21_dpp' => floor($dpp),
                    'pph21_bruto_month' => floor($total_bruto),
                    'pph21_bruto_year' => floor($total_bruto) * 12,
                    'pph21_netto_month' => floor($netto),
                    'pph21_netto_year' => floor($netto) * 12,
                    'pph21_total_month' => floor($pph21),
                    'pph21_total_year' => floor($pph21) * 12,

                    'pph21_ptkp_data' => json_encode($ptkp),
                    'pph21_ptkpdet_data' => json_encode($ptkp_detail),
                    'pph21_tarif21_data' => ($calculate_pph21['tarif21']) ? json_encode($calculate_pph21['tarif21']) : null,
                ];
                
                array_push($temp_data_all, $temp_data);
                array_push($temp_data_pph21_all, $temp_data_pph21);
            // }
        // }

        return [
            'payroll' => $temp_data_all,
            'pph21' => $temp_data_pph21_all,
        ];
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
        } else if($table == 'tr_history_import') {
            $historyImportModel = new HistoryImportModel();
            $activitylog_properties["attributes"] = $updateddata;
            array_push($datalogs, [
                "log_name" => $historyImportModel->getLogName(),
                "description" => $historyImportModel->getDescriptionForEvent($eventname),
                "subject_type" => $historyImportModel->getCauserTypeModel(),
                "subject_id" => $dataforlogs->historyimport_id,
                "causer_id" => $historyImportModel->getCauserId(),
                "causer_type" => $historyImportModel->getCauserTypeModel(),
                "properties" => json_encode($activitylog_properties),
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s')
            ]);
        } else if($table == 'ms_karyawan') {
            $karyawanModel = new KaryawanModel();
            // dd($dataforlogs);
            foreach($dataforlogs as $dt) {
                if(!isset($dt['karyawan_code_objekpajak'])) { // if 2 dimensional array
                    foreach($dt as $subdt) {
                        $activitylog_properties["old"] = $subdt;
                        $activitylog_properties["attributes"] = $subdt;
                        array_push($datalogs, [
                            "log_name" => $karyawanModel->getLogName(),
                            "description" => $karyawanModel->getDescriptionForEvent($eventname),
                            "subject_type" => $karyawanModel->getCauserTypeModel(),
                            "subject_id" => $subdt->karyawan_id,
                            "causer_id" => $karyawanModel->getCauserId(),
                            "causer_type" => $karyawanModel->getCauserTypeModel(),
                            "properties" => json_encode($activitylog_properties),
                            "created_at" => date('Y-m-d H:i:s'),
                            "updated_at" => date('Y-m-d H:i:s')
                        ]);
                    }
                } else {
                    $activitylog_properties["old"] = $dt;
                    $activitylog_properties["attributes"] = $dt;
                    array_push($datalogs, [
                        "log_name" => $karyawanModel->getLogName(),
                        "description" => $karyawanModel->getDescriptionForEvent($eventname),
                        "subject_type" => $karyawanModel->getCauserTypeModel(),
                        "subject_id" => $dt->karyawan_id,
                        "causer_id" => $karyawanModel->getCauserId(),
                        "causer_type" => $karyawanModel->getCauserTypeModel(),
                        "properties" => json_encode($activitylog_properties),
                        "created_at" => date('Y-m-d H:i:s'),
                        "updated_at" => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }

        if($datalogs)
            Activity::insert($datalogs);
    }
}