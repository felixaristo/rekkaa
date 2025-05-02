<?php
namespace App\Http\Controllers\User\Dashboard;

use App\Http\Controllers\Api\XenditCallback\XenditCallbackController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Penggajian\PenggajianController;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PermissionModel;
use App\Model\Master\SubscriptionModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Master\WajibPajakUserModel;
use App\Model\MasterRelation\MrSubscriptionPermissionModel;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\Model\Transaction\LeaveKaryawanModel;
use App\Model\Transaction\PayrollModel;
use App\Model\Transaction\UserOrderModel;
use App\User;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd(getCurrentAccessGroup());
        // dd(session()->get('wajibpajak_current'));
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $periode = date('m-Y');
        $user = User::with(['wajibpajak.wpstpenggajian'])->where([
            'user_id' => $user_id
        ])->first();
        $listpenggajian = PayrollModel::
        where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            // 'payroll_status' => 0
        ])
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$periode])
        ->get();
        $totalnotfinal = 0;
        $totalnotpaid = 0;
        foreach($listpenggajian as $lspg) {
            if($lspg->payroll_status == '0') {
                $totalnotfinal++;
            }
            if($lspg->payroll_status <= '3') {
                $totalnotpaid++;
            }
        }
        // dd($totalnotfinal, $listpenggajian);
        $data = [
            'title' => 'Beranda',
            'content' => 'user.dashboard.index',
            'user' => $user,
            'stpenggajian' => $user->wajibpajak->wpstpenggajian,
            'listpenggajian' => $listpenggajian,
            'totalnotfinal' => $totalnotfinal,
            'totalnotpaid' => $totalnotpaid,
            'permission' => PermissionModel::where(['permission_active' => 1])->orderBy('permission_parent', 'ASC')
            ->orderBy('permission_child', 'ASC')->orderBy('permission_order', 'ASC')->get(),
            'subscription' => SubscriptionModel::where(['subscription_active' => 1])->whereNotIn('subscription_type', ['FREES'])->orderBy('subscription_order', 'ASC')->get()
        ];

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function data(Request $request)
    {
        $data = null;
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $user = User::with(['wajibpajak.wpstpenggajian'])->where([
            'user_id' => $user_id
        ])->first();
        if(session()->get('wajibpajak_current')) {
            $periode_attendance = $request->input('periode_attendance');
            $today = \DateTime::createFromFormat('d-m-Y', $periode_attendance)->format('Y-m-d');
            $monthyear = \DateTime::createFromFormat('d-m-Y', $periode_attendance)->format('Y-m');
            $dayName = \DateTime::createFromFormat('Y-m-d', $today)->format('l');
            $periode_gaji = ($request->input('periode_gaji')) ? $request->input('periode_gaji') : date('m-Y');
            $periode_chart = $request->input('periode_chart');

            $payroll_period = Carbon::parse(date('d').'-'.$periode_gaji);
            $payroll_periodm = $payroll_period->format('m');
            $payroll_periody = $payroll_period->format('Y');

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
            $penggajiankaryawan = new PenggajianController();
            // if($payroll_periodm == 12) {
                for($ip = 1; $ip <= 12; $ip++) {
                    $payroll_period = Carbon::parse(date('d').'-'.$periode_gaji);
                    // $payroll_periodm = $payroll_period->format('m');
                    $iper = ($ip > 9) ? $ip.'-'.$payroll_periody : '0'.$ip.'-'.$payroll_periody;
                    $request->request->add(['payroll_period' => $iper]);
                    $penggajiankaryawan->calculatenew($request);
                }
            // } else {
            //     $request->request->add(['payroll_period' => $periode_gaji]);
            //     $penggajiankaryawan->calculatenew($request);
            // }

            $wp_id = session()->get('wajibpajak_current')['wajibpajak_id'];
            $data = WajibPajakModel::select(
                // (SELECT json_agg(ptable) FROM (SELECT tr_penerimaan.kode_faktur, penerimaan_total_terbayar, bayarsupplier_nominal FROM tr_penerimaan JOIN tr_bayar_supplier ON tr_bayar_supplier.kode_faktur = tr_penerimaan.kode_faktur AND tr_kasbankkeluar_id = kasbankkeluar_id AND penerimaan_aktif='y') as ptable) as nofaktur_json
                DB::raw("(SELECT COUNT(karyawan_id) FROM ms_karyawan 
                WHERE karyawan_active='1' AND ms_karyawan.karyawan_status = 'TETAP' AND ms_karyawan.ms_wajibpajak_id = {$wp_id} AND (TO_CHAR(ms_karyawan.karyawan_contract_end, 'YYYY-MM') >= '{$monthyear}' OR ms_karyawan.karyawan_contract_end IS NULL)) as total_karyawan_tetap")
                , DB::raw("(SELECT COUNT(karyawan_id) FROM ms_karyawan 
                WHERE karyawan_active='1' AND ms_karyawan.karyawan_status = 'KONTRAK' AND ms_karyawan.ms_wajibpajak_id = {$wp_id} AND (TO_CHAR(ms_karyawan.karyawan_contract_end, 'YYYY-MM') >= '{$monthyear}' OR ms_karyawan.karyawan_contract_end IS NULL)) as total_karyawan_kontrak")
                , DB::raw("(SELECT COUNT(karyawan_id) FROM ms_karyawan 
                WHERE karyawan_active='1' AND ms_karyawan.karyawan_status = 'NONKARYAWAN' AND ms_karyawan.ms_wajibpajak_id = {$wp_id} AND (TO_CHAR(ms_karyawan.karyawan_contract_end, 'YYYY-MM') >= '{$monthyear}' OR ms_karyawan.karyawan_contract_end IS NULL)) as total_karyawan_bukan")
                , DB::raw("(SELECT COUNT(karyawan_id) FROM ms_karyawan 
                WHERE karyawan_active='1' AND ms_karyawan.karyawan_status = 'PERCOBAAN' AND ms_karyawan.ms_wajibpajak_id = {$wp_id} AND (TO_CHAR(ms_karyawan.karyawan_contract_end, 'YYYY-MM') >= '{$monthyear}' OR ms_karyawan.karyawan_contract_end IS NULL)) as total_karyawan_percobaan")
                , DB::raw("(SELECT SUM(tr_payroll.payroll_total_netto) FROM tr_payroll
                WHERE tr_payroll.payroll_active='1'
                AND EXTRACT(MONTH FROM tr_payroll.payroll_period) = SUBSTRING('{$periode_gaji}', 1, 2)::INTEGER
                AND EXTRACT(YEAR FROM tr_payroll.payroll_period) = SUBSTRING('{$periode_gaji}', 4, 4)::INTEGER 
                AND tr_payroll.ms_wajibpajak_id = {$wp_id}
                ) as total_salary_periode_terakhir")
                , DB::raw("(SELECT SUM(tr_payroll.payroll_deduction_pph21) FROM tr_payroll
                WHERE tr_payroll.payroll_active='1'
                AND EXTRACT(MONTH FROM tr_payroll.payroll_period) = SUBSTRING('{$periode_gaji}', 1, 2)::INTEGER
                AND EXTRACT(YEAR FROM tr_payroll.payroll_period) = SUBSTRING('{$periode_gaji}', 4, 4)::INTEGER 
                AND tr_payroll.ms_wajibpajak_id = {$wp_id}
                ) as total_pajak_periode_terakhir
                ")
                , DB::raw("(SELECT SUM(tr_payroll.payroll_allowance_bpjskes) FROM tr_payroll
                WHERE tr_payroll.payroll_active='1'
                AND EXTRACT(MONTH FROM tr_payroll.payroll_period) = SUBSTRING('{$periode_gaji}', 1, 2)::INTEGER
                AND EXTRACT(YEAR FROM tr_payroll.payroll_period) = SUBSTRING('{$periode_gaji}', 4, 4)::INTEGER 
                AND tr_payroll.ms_wajibpajak_id = {$wp_id}
                ) as total_bpjskes_periode_terakhir
                ")
                , DB::raw("(SELECT SUM(tr_payroll.payroll_allowance_bpjstk) FROM tr_payroll
                WHERE tr_payroll.payroll_active='1'
                AND EXTRACT(MONTH FROM tr_payroll.payroll_period) = SUBSTRING('{$periode_gaji}', 1, 2)::INTEGER
                AND EXTRACT(YEAR FROM tr_payroll.payroll_period) = SUBSTRING('{$periode_gaji}', 4, 4)::INTEGER 
                AND tr_payroll.ms_wajibpajak_id = {$wp_id}
                ) as total_bpjstk_periode_terakhir
                ")
                , DB::raw("(SELECT json_agg(ptable) FROM (
                    SELECT to_char(m, 'Month') as mn, 
                        COALESCE(SUM(tr_payroll.payroll_total_netto), 0) as payroll_total_netto
                    FROM generate_series(
                        '{$periode_chart}-01-01'::date, 
                        '{$periode_chart}-12-31'::date, '1 month'
                    ) s(m)
                    LEFT JOIN tr_payroll ON 
                        date_trunc('MONTH', tr_payroll.payroll_period) = m
                        AND tr_payroll.ms_wajibpajak_id = {$wp_id}
                        AND tr_payroll.payroll_active='1'
                    GROUP BY to_char(m, 'Month')
                ) as ptable) as pengeluaran_tahunberjalan_json")
                , DB::raw("(SELECT json_agg(ptable) FROM (
                    SELECT to_char(m, 'Month') as mn, 
                        COALESCE(SUM(tr_payroll.payroll_deduction_pph21), 0) as payroll_deduction_pph21
                        FROM generate_series(
                            '{$periode_chart}-01-01'::date, 
                            '{$periode_chart}-12-31'::date, '1 month'
                        ) s(m)
                    LEFT JOIN tr_payroll ON 
                        date_trunc('MONTH', tr_payroll.payroll_period) = m
                        AND tr_payroll.ms_wajibpajak_id = {$wp_id}
                        AND tr_payroll.payroll_active='1'
                    GROUP BY to_char(m, 'Month')
                ) as ptable) as pengeluaran_pphberjalan_json")
                , DB::raw("(SELECT json_agg(ptable) FROM (
                    SELECT to_char(m, 'Month') as mn, 
                        COALESCE(SUM(tr_payroll.payroll_allowance_bpjskes), 0) as payroll_allowance_bpjskes
                        FROM generate_series(
                            '{$periode_chart}-01-01'::date, 
                            '{$periode_chart}-12-31'::date, '1 month'
                        ) s(m)
                    LEFT JOIN tr_payroll ON 
                        date_trunc('MONTH', tr_payroll.payroll_period) = m
                        AND tr_payroll.ms_wajibpajak_id = {$wp_id}
                        AND tr_payroll.payroll_active='1'
                    GROUP BY to_char(m, 'Month')
                ) as ptable) as pengeluaran_bpjskesberjalan_json")
                , DB::raw("(SELECT json_agg(ptable) FROM (
                    SELECT to_char(m, 'Month') as mn, 
                        COALESCE(SUM(tr_payroll.payroll_allowance_bpjstk), 0) as payroll_allowance_bpjstk
                        FROM generate_series(
                            '{$periode_chart}-01-01'::date, 
                            '{$periode_chart}-12-31'::date, '1 month'
                        ) s(m)
                    LEFT JOIN tr_payroll ON 
                        date_trunc('MONTH', tr_payroll.payroll_period) = m
                        AND tr_payroll.ms_wajibpajak_id = {$wp_id}
                        AND tr_payroll.payroll_active='1'
                    GROUP BY to_char(m, 'Month')
                ) as ptable) as pengeluaran_bpjstkberjalan_json")
                , DB::raw("(SELECT COUNT(DISTINCT tr_leave_karyawan.ms_karyawan_id) FROM tr_leave_karyawan
                    JOIN ms_karyawan ON tr_leave_karyawan.ms_karyawan_id = ms_karyawan.karyawan_id
                    JOIN st_attendance ON ms_karyawan.st_attendance_id = st_attendance.attendance_id
                    WHERE ms_karyawan.ms_wajibpajak_id = {$wp_id}
                    AND ms_karyawan.karyawan_active = '1'
                        AND tr_leave_karyawan.leavekaryawan_status='APPROVED'
                        AND '{$today}' BETWEEN tr_leave_karyawan.leavekaryawan_start_date AND tr_leave_karyawan.leavekaryawan_end_date
                        AND st_attendance.attendance_working_day LIKE '%{$dayName}%'
                ) as total_leave")
                , DB::raw("(SELECT COUNT(DISTINCT tr_attendance_karyawan.ms_karyawan_id) FROM tr_attendance_karyawan
                    JOIN ms_karyawan ON tr_attendance_karyawan.ms_karyawan_id = ms_karyawan.karyawan_id
                    WHERE ms_karyawan.ms_wajibpajak_id = {$wp_id}
                    AND ms_karyawan.karyawan_active = '1'
                        AND DATE('{$today}') = DATE(tr_attendance_karyawan.attendancekaryawan_check_in)
                ) as total_attendance")
                , DB::raw("(SELECT COUNT(DISTINCT tr_attendance_karyawan.ms_karyawan_id) FROM tr_attendance_karyawan
                JOIN ms_karyawan ON tr_attendance_karyawan.ms_karyawan_id = ms_karyawan.karyawan_id
                WHERE ms_karyawan.ms_wajibpajak_id = {$wp_id}
                    AND ms_karyawan.karyawan_active = '1'
                    AND DATE('{$today}') = DATE(tr_attendance_karyawan.attendancekaryawan_check_in)
                    AND tr_attendance_karyawan.attendancekaryawan_check_in_late IS NOT NULL
                    AND tr_attendance_karyawan.attendancekaryawan_check_in_late != '00:00'
                ) as total_attendance_late")
                , DB::raw("(SELECT COUNT(*) FROM (SELECT ms_karyawan.karyawan_id FROM ms_karyawan WHERE ms_karyawan.st_attendance_id IN (
                    SELECT st_attendance.attendance_id FROM st_attendance
                    WHERE st_attendance.attendance_working_day LIKE '%{$dayName}%'
                )
                    AND ms_karyawan.karyawan_active = '1'
                    AND ms_karyawan.ms_wajibpajak_id = {$wp_id}
                    AND ms_karyawan.karyawan_status != 'NONKARYAWAN'
                    AND ms_karyawan.karyawan_contract_begin <= '{$today}'
                    AND (ms_karyawan.karyawan_contract_end >= '{$today}' OR ms_karyawan.karyawan_contract_end IS NULL)
                ) AS subquery_alias) as total_karyawan_onduty")
            )->where(['wajibpajak_id' => $wp_id])->first();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data Dashboard',
            'data' => $data
        ]);
    }

    public function datamasuk(Request $request)
    {
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
        $wp_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $periode_attendance = ($request->input('periode_attendance')) ? $request->input('periode_attendance') : date('d-m-Y');
        $today = \DateTime::createFromFormat('d-m-Y', $periode_attendance)->format('Y-m-d'); 
        
        $data = KaryawanModel::select(
            'ms_karyawan.karyawan_name',
            'ms_karyawan.karyawan_enid',
            DB::raw("(SELECT tr_attendance_karyawan.attendancekaryawan_check_in 
                      FROM tr_attendance_karyawan
                      WHERE tr_attendance_karyawan.ms_karyawan_id = ms_karyawan.karyawan_id
                        AND tr_attendance_karyawan.attendancekaryawan_check_in::date = '{$today}'
                      LIMIT 1) as attendancekaryawan_check_in")
        )
        ->where('ms_karyawan.ms_wajibpajak_id', $wp_id)
        ->where('ms_karyawan.karyawan_active', '1')
        ->whereHas('attendanceKaryawans', function ($query) use ($today) {
            $query->whereRaw('tr_attendance_karyawan.attendancekaryawan_check_in::date = ?', [$today]);
        })
        ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Data Dashboard',
            'data' => $data->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $data->total(),
            'recordsTotal' => $data->total(),
        ]);
    }

    public function dataterlambat(Request $request)
    {
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
        $wp_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $periode_attendance = ($request->input('periode_attendance')) ? $request->input('periode_attendance') : date('d-m-Y');
        $today = \DateTime::createFromFormat('d-m-Y', $periode_attendance)->format('Y-m-d'); 
        
        $data = KaryawanModel::select(
            'ms_karyawan.karyawan_name',
            'ms_karyawan.karyawan_enid',
            DB::raw("(SELECT tr_attendance_karyawan.attendancekaryawan_check_in_late 
                      FROM tr_attendance_karyawan
                      WHERE tr_attendance_karyawan.ms_karyawan_id = ms_karyawan.karyawan_id
                        AND tr_attendance_karyawan.attendancekaryawan_check_in_late IS NOT NULL
                        AND tr_attendance_karyawan.attendancekaryawan_check_in_late != '00:00'
                        AND tr_attendance_karyawan.attendancekaryawan_check_in::date = '{$today}'
                      LIMIT 1) as attendancekaryawan_check_in_late")
        )
        ->where('ms_karyawan.ms_wajibpajak_id', $wp_id)
        ->where('ms_karyawan.karyawan_active', '1')
        ->whereHas('attendanceKaryawans', function ($query) use ($today) {
            $query->whereRaw('tr_attendance_karyawan.attendancekaryawan_check_in::date = ?', [$today]);
            $query->whereRaw('tr_attendance_karyawan.attendancekaryawan_check_in_late IS NOT NULL');
            $query->whereRaw("tr_attendance_karyawan.attendancekaryawan_check_in_late != '00:00'");
        })
        ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Data Dashboard',
            'data' => $data->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $data->total(),
            'recordsTotal' => $data->total(),
        ]);
    }

    public function datamia(Request $request)
    {
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
        $wp_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $periode_attendance = ($request->input('periode_attendance')) ? $request->input('periode_attendance') : date('d-m-Y');
        $today = \DateTime::createFromFormat('d-m-Y', $periode_attendance)->format('Y-m-d');
        $dayName = \DateTime::createFromFormat('Y-m-d', $today)->format('l');
        
        // $data = KaryawanModel::select(
        //     'ms_karyawan.karyawan_name',
        //     'ms_karyawan.karyawan_enid'
        // )
        // ->join('st_attendance', 'ms_karyawan.st_attendance_id', '=', 'st_attendance.attendance_id')
        // ->where('st_attendance.attendance_working_day', 'LIKE', "%{$dayName}%")
        // ->where('ms_karyawan.karyawan_active', '1')
        // ->where('ms_karyawan.ms_wajibpajak_id', $wp_id)
        // ->paginate($perPage, ['*'], 'page', $page);

        $data = KaryawanModel::select(
            'ms_karyawan.karyawan_name',
            'ms_karyawan.karyawan_enid'
        )
        ->join('st_attendance', 'ms_karyawan.st_attendance_id', '=', 'st_attendance.attendance_id')
        ->leftJoin('tr_leave_karyawan', function ($join) use ($today) {
            $join->on('ms_karyawan.karyawan_id', '=', 'tr_leave_karyawan.ms_karyawan_id')
                 ->where('tr_leave_karyawan.leavekaryawan_status', 'APPROVED')
                 ->whereDate('tr_leave_karyawan.leavekaryawan_start_date', '<=', $today)
                 ->whereDate('tr_leave_karyawan.leavekaryawan_end_date', '>=', $today);
        })
        ->where('st_attendance.attendance_working_day', 'LIKE', "%{$dayName}%")
        ->where('ms_karyawan.karyawan_active', '1')
        ->where('ms_karyawan.ms_wajibpajak_id', $wp_id)
        ->where(function ($query) use ($today) {
            $query->where('ms_karyawan.karyawan_contract_end', '>=', $today)
                  ->orWhereNull('ms_karyawan.karyawan_contract_end');
        })
        ->where(function ($query) use ($today) {
            // Filter out records where the employee has taken leave on $today
            $query->whereNull('tr_leave_karyawan.ms_karyawan_id')
                  ->orWhere(function ($subquery) use ($today) {
                      $subquery->whereNotNull('tr_leave_karyawan.ms_karyawan_id')
                               ->whereDate('tr_leave_karyawan.leavekaryawan_end_date', '<', $today);
                  });
        })
        ->paginate($perPage, ['*'], 'page', $page);


        return response()->json([
            'success' => true,
            'message' => 'Data Dashboard',
            'data' => $data->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $data->total(),
            'recordsTotal' => $data->total(),
        ]);
    }

    public function datacuti(Request $request)
    {
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
        $wp_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $periode_attendance = ($request->input('periode_attendance')) ? $request->input('periode_attendance') : date('d-m-Y');
        $today = \DateTime::createFromFormat('d-m-Y', $periode_attendance)->format('Y-m-d');
        $dayName = \DateTime::createFromFormat('Y-m-d', $today)->format('l');
        
        $data = KaryawanModel::select(
            'ms_karyawan.karyawan_name',
            'ms_karyawan.karyawan_enid',
            'st_leave.leave_description'
        )
        ->join('tr_leave_karyawan', 'ms_karyawan.karyawan_id', '=', 'tr_leave_karyawan.ms_karyawan_id')
        ->join('st_leave', 'tr_leave_karyawan.st_leave_id', '=', 'st_leave.leave_id') // Add this join
        ->join('st_attendance', 'ms_karyawan.st_attendance_id', '=', 'st_attendance.attendance_id')
        ->where('ms_karyawan.ms_wajibpajak_id', $wp_id)
        ->where('ms_karyawan.karyawan_active', '1')
        ->where('tr_leave_karyawan.leavekaryawan_status', 'APPROVED')
        ->where('tr_leave_karyawan.leavekaryawan_start_date', '<=', $today)
        ->where('tr_leave_karyawan.leavekaryawan_end_date', '>=', $today)
        ->where('st_attendance.attendance_working_day', 'LIKE', "%{$dayName}%")
        ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Data Dashboard',
            'data' => $data->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $data->total(),
            'recordsTotal' => $data->total(),
        ]);
    }

    public function migratedata(Request $request)
    {
        if(!$request->get('code') || $request->get('code') != env('APP_INTERNALCODE')) {
            abort(404);
        }
        $users = DB::select(DB::raw("SELECT temp_ms_user.*, temp_ms_wajib_pajak.* FROM temp_ms_user JOIN temp_ms_wajib_pajak ON user_id=ms_user_id"));
        // dd($users);

        DB::beginTransaction();
        try {
            foreach($users as $user) {
                $insertuser = User::create([
                    'user_email' => $user->user_email,
                    'user_password' => Hash::make($user->user_password),
                    'user_verified_token' => $user->user_verified_token,
                    'user_active' => $user->user_active, // registration incomplete
                ]);
                $wajibpajak_save_data = [
                    'wajibpajak_npwp' => $user->wajibpajak_npwp,
                    'wajibpajak_nik' => $user->wajibpajak_nik,
                    'wajibpajak_name' => $user->wajibpajak_name,
                    'wajibpajak_type' => ($user->wajibpajak_type == 'COMPANY') ? 'BADAN' : 'INDIVIDU',
                    'wajibpajak_address' => $user->wajibpajak_address,
                    'wajibpajak_email' => $user->wajibpajak_email,
                    'wajibpajak_phone' => $user->wajibpajak_phone,
                    'ms_country_id' => 100,
                    'ms_regency_id' => $user->ms_regency_id,
                    'wajibpajak_intercity' => null,
                    'ms_user_id' => $insertuser->user_id,
                ];
                // dd($wajibpajak_save_data);
                $insertwajibpajak = WajibPajakModel::create($wajibpajak_save_data);

                // insert mr user wajib pajak
                MrUserWajibPajakModel::create([
                    'ms_user_id' => $insertuser->user_id,
                    'ms_wajibpajak_id' => $insertwajibpajak->wajibpajak_id,
                    'userwajibpajak_name' => 'Admin',
                    'userwajibpajak_owner' => '1',
                    // 'userwajibpajak_name' => $wajibpajak->wajibpajak_name,
                    'userwajibpajak_phone' => $insertwajibpajak->wajibpajak_phone,
                ]);
                // dd($insertwajibpajak);
                $insertorder = UserOrderModel::create([
                    'ms_user_id' => $insertuser->user_id,
                    'ms_wajibpajak_id' => $insertwajibpajak->wajibpajak_id,
                    'ms_subscription_id' => ($user->wajibpajak_type == 'COMPANY') ? 1 : 6, // FREE
                    'userorder_qty' => 12,
                    'userorder_price' => 0,
                    'userorder_discount' => 0,
                    'userorder_total' => 0,
                    'userorder_subscriptiontype' => 'FREE',
                    'userorder_paymentperiode' => 12,
                    'userorder_expired_at' => date('Y-m-d H:i:s'),
                    'userorder_description' => 'Rekkaa - Order Subscription FREE',
                    'userorder_xenditurl' => null,
                    'userorder_xenditdata' => null,
                    'userorder_subscriptiondata' => '',
                    'userorder_status' => 'PENDING',
                ]);
                // dd($insertorder);
                $order = UserOrderModel::where(['userorder_id' => $insertorder->userorder_id])->first();
                // dd($order);
                
                $request->request->add([
                    'external_id' => $order->userorder_no,
                    'status' => 'PAID',
                    'paid_amount' => 0,
                    'payment_method' => 'REKKAA_DIRECT',
                    'payment_channel' => 'REKKAA_REGISTER',
                ]);
                $xendictcallback = new XenditCallbackController();
                // dd($request->all());
                $checkcallback = $xendictcallback->callback($request);
                // $decodecallback = ($checkcallback) ? json_decode($checkcallback) : null;
                // dd($checkcallback->getData()->success);
                if(!$checkcallback->getData()->success) {
                    return response()->json([
                        'success' => false,
                        'message' => $checkcallback->getData()->message
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil.',
                'data' => [
                    // 'user_id' => $user->user_id
                ]
            ]);

        } catch(Error $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function downloadLocale(Request $request) {
        // $allowed_types = ['xls', 'pdf'];
        // $type = $request->get('type');
        $file = basename($request->get('file')); // basename prevent traversal attack. DON'T DELETE
        // if(!in_array($type, $allowed_types)) {
        //     abort(404);
        // }

        $location = public_path('assets/export/');
        if(!file_exists($location.$file)) {
            abort(404);
        }
        $content = file_get_contents($location.$file); // get content
        header("Content-Disposition: attachment; filename=".$file);
        unlink($location.$file); // remove file
        exit($content);
    }
}