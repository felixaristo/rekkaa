<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Karyawan\LemburController as KaryawanLemburController;
use App\Http\Controllers\User\Penggajian\PenggajianController;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingLemburKaryawanModel;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use App\Model\Transaction\LemburKaryawanModel;
use App\Model\Transaction\PayrollModel;
use App\User;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LemburController extends Controller
{
    public function indexAdmin(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $user_id = session()->get('user_data')['user_id'];
        $periode = date('m-Y');
        $user = User::with(['wajibpajak.wpstpenggajian'])->where([
            'user_id' => $user_id
        ])->first();
        $data = [
            'title' => 'Daftar Lembur Karyawan',
            'content' => 'user.lembur.lembur-admin',
            'user' => $user,
        ];

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function lemburAdmin(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
        $status = $request->input('status');
        $karyawan_ids = $request->input('karyawan_ids');
        $start_date = '01-'.$request->input('start_date');
        // $end_date = $request->input('end_date');
        $order_columns = ['lemburkaryawan_id','lemburkaryawan_created_at','lemburkaryawan_start_time', 'lemburkaryawan_end_time','lemburkaryawan_status', 'lemburkaryawan_approval_date'];
        $order_col = $order_columns[0];
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $where = [
            'ms_wajibpajak_id' => $wajibpajak_id,
        ];
        if($status) {
            $where['lemburkaryawan_status'] = $status;
        }
        
        $list = LemburKaryawanModel::select('tr_lembur_karyawan.*')
        ->with(['karyawan', 'manager', 'admin', 'admin.userwajibpajak', 'canceladmin', 'canceladmin.userwajibpajak'])
        ->when($karyawan_ids, function($q) use ($karyawan_ids) {
            return $q->whereIn('ms_karyawan_id', $karyawan_ids);
        })
        ->where(function($q) use ($start_date) {
            return $q->where(DB::raw("TO_CHAR(lemburkaryawan_created_at, 'YYYY-MM')"), '=', Carbon::parse($start_date)->format('Y-m'));
        })
        ->where($where)
        ->whereIn('lemburkaryawan_status', ['APPROVEDMANAGER', 'APPROVED', 'REJECTED'])
        ->orderBy($order_col, $order_type)
        ->take($limit)->skip($offset)->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = LemburKaryawanModel::when($karyawan_ids, function($q) use ($karyawan_ids) {
            return $q->whereIn('ms_karyawan_id', $karyawan_ids);
        })
        ->where($where)->where(function($q) use ($start_date) {
            return $q->where(DB::raw("TO_CHAR(lemburkaryawan_created_at, 'YYYY-MM')"), '=', Carbon::parse($start_date)->format('Y-m'));
        })
        ->whereIn('lemburkaryawan_status', ['APPROVEDMANAGER', 'APPROVED', 'REJECTED'])->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function cancel($lemburId, Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $lemburkaryawan_cancel_note = $request->input('lemburkaryawan_cancel_note');

        $validator = Validator::make($request->all(), [
            'lemburkaryawan_cancel_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        // dd($request->all());
        $lembur = LemburKaryawanModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'lemburkaryawan_id' => $lemburId])
        ->whereIn('lemburkaryawan_status', ['APPROVEDMANAGER'])->first();

        DB::beginTransaction();
        try {
            $lembur->update([
                'lemburkaryawan_canceladmin_note' => $lemburkaryawan_cancel_note,
                'lemburkaryawan_canceladmin_date' => date('Y-m-d H:i:s'),
                'lemburkaryawan_canceladmin_by' => $user_id,
                'lemburkaryawan_status' => 'REJECTED',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan pembatalan Pengajuan Lembur.',
            ], 200);
        } catch (Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }  
    }

    public function approve($lemburId, Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $lemburkaryawan_approval_note = $request->input('lemburkaryawan_approval_note');

        $validator = Validator::make($request->all(), [
            'lemburkaryawan_approval_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        // dd($request->all());
        $lembur = LemburKaryawanModel::with(['karyawan', 'karyawan.penggajian'])->where(['ms_wajibpajak_id' => $wajibpajak_id, 'lemburkaryawan_id' => $lemburId])
        ->whereIn('lemburkaryawan_status', ['APPROVEDMANAGER'])->first();

        // check if setting penggajian period according to start date
        // dd($lembur->karyawan->penggajian);
        $stpenggajian = $lembur->karyawan->penggajian;
        if(!$stpenggajian) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Penggajian belum ada. Silahkan lakukan penyesuaian di menu Pengaturan Penggajian.',
            ], 200);
        }
        $start_time = Carbon::parse($lembur->lemburkaryawan_start_time);
        $start_timed = $start_time->format('d');
        $start_timeym = $start_time->format('Y-m');
        $start_timemy = $start_time->format('m-Y');
        $start_timeymd = $start_time->format('Y-m-d');
        $end_time = Carbon::parse($lembur->lemburkaryawan_end_time);
        // if($stpenggajian->stpenggajiankaryawan_period == 'TANGGAL') {
            
        // }
        // check if payroll exist
        $payroll = PayrollModel::whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$start_timemy])
        ->where(['ms_wajibpajak_id' => $lembur->ms_wajibpajak_id, 'ms_karyawan_id' => $lembur->ms_karyawan_id])->first();
        $payroll_uuid = null;
        if($payroll) {
            if($payroll->payroll_lock == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penggajian periode '.$start_timemy.' sudah di locked. Silahkan ajukan lembur dengan tanggal di periode selanjutnya.',
                ], 200);
            }

            $payroll_uuid = $payroll->payroll_uuid;
        }

        DB::beginTransaction();
        try {
            $lembur->update([
                'lemburkaryawan_approvaladmin_note' => $lemburkaryawan_approval_note,
                'lemburkaryawan_approvaladmin_date' => date('Y-m-d H:i:s'),
                'lemburkaryawan_approval_adminby' => $user_id,
                'lemburkaryawan_status' => 'APPROVED',
                'tr_payroll_uuid' => $payroll_uuid,
            ]);

            if($payroll_uuid) {
                $request->request->add(['payroll_uuids' => [$payroll_uuid]]);
                $penggajianctrl = new PenggajianController();
                $penggajianctrl->calculate($request);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan Pengajuan Lembur.',
            ], 200);
        } catch (Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }  
    }

    public function store(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawan_id = $request->input('karyawan_ids');
        $lemburkaryawan_start_time = $request->input('lemburkaryawan_start_time');
        $lemburkaryawan_end_time = $request->input('lemburkaryawan_end_time');

        $validator = Validator::make($request->all(), [
            'lemburkaryawan_start_time' => 'required',
            'lemburkaryawan_end_time' => 'required',
            'lemburkaryawan_request_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        // dd($request->all());
        $start_time = Carbon::parse($lemburkaryawan_start_time);
        $start_timeymdhis = $start_time->format('Y-m-d H:i:s');
        $start_timeym = $start_time->format('Y-m');
        $start_timeymd = $start_time->format('Y-m-d');
        $start_timedmy = $start_time->format('d-m-Y');
        $end_time = Carbon::parse($lemburkaryawan_end_time);
        $end_timeym = $end_time->format('Y-m');
        $end_timeymd = $end_time->format('Y-m-d');
        $end_timeymdhis = $end_time->format('Y-m-d H:i:s');
        $total_duration = $end_time->diffInMinutes($start_time);

        if(strtotime($start_timeymdhis) > strtotime($end_timeymdhis)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal mulai harus lebih kecil dari tanggal selesai.',
            ], 200);
        }

        // check existing date
        $existinglembur = LemburKaryawanModel::where(['ms_karyawan_id' => $karyawan_id, 'ms_wajibpajak_id' => $wajibpajak_id])
        ->whereNotIn('lemburkaryawan_status', ['REJECTED','CANCEL'])
        ->whereRaw("(TO_CHAR(lemburkaryawan_start_time, 'YYYY-MM-DD') = ?)", [$start_timeymd])->first();
        // dd($existinglembur);
        if($existinglembur) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan lembur sudah ada di tanggal '.$start_timedmy.'. Silahkan ajukan ditanggal lain!',
            ], 200);
        }

        $karyawan = KaryawanModel::select("ms_karyawan.*"
        ,   DB::raw("( SELECT row_to_json(statttable.*) AS row_to_json
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
                     sa.attendance_is_used,
                     sa.attendance_status_active
                    FROM st_attendance sa
                   WHERE sa.ms_wajibpajak_id = ms_karyawan.ms_wajibpajak_id AND sa.attendance_id = ms_karyawan.st_attendance_id
                  LIMIT 1) statttable) AS stattendancetbl"))->where(['karyawan_id' => $karyawan_id, 'ms_wajibpajak_id' => $wajibpajak_id])->first();

        // dd($karyawan);
        $karyawan_id = $karyawan->karyawan_id;
        $manager_id = $karyawan->karyawan_manager_id;
        $attendance = ($karyawan->stattendancetbl) ? json_decode($karyawan->stattendancetbl) : null;
        
        $start_timemy = $start_time->format('m-Y');
        $payroll = PayrollModel::whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$start_timemy])
        ->where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_karyawan_id' => $karyawan_id])->first();
        
        if($payroll) {
            if($payroll->payroll_lock == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penggajian periode '.$start_timemy.' sudah di locked. Silahkan ajukan lembur dengan tanggal di periode selanjutnya.',
                ], 200);
            }
        }

        
        if(!$karyawan->penggajian) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan penggajian tidak ditemukan.',
            ], 200);
        }

        $lemburkaryawanctrl = new KaryawanLemburController();
        $calculate_overtime = $lemburkaryawanctrl->calculateovertime($start_time, $end_time, $karyawan, $attendance);
        if(isset($stlembur['success']) && $stlembur['success'] == false) {
            return response()->json([
                'success' => $stlembur['success'],
                'message' => $stlembur['message'],
            ], 200);
        }

        DB::beginTransaction();
        try {
            
            if($manager_id == 0) {
                $manager_id = null;
            }

            $createLembur = LemburKaryawanModel::create([
                'ms_karyawan_id' => $karyawan_id,
                'ms_wajibpajak_id' => $wajibpajak_id,
                'lemburkaryawan_start_time' => $start_timeymdhis,
                'lemburkaryawan_end_time' => $end_timeymdhis,
                'lemburkaryawan_manager_id' => $manager_id,
                'lemburkaryawan_request_note' => $request->input('lemburkaryawan_request_note'),
                'lemburkaryawan_status' => 'APPROVEDMANAGER',
                'lemburkaryawan_approval_date' => date('Y-m-d H:i:s'),
                'lemburkaryawan_prorate_days' => $calculate_overtime['prorate_days'], // in days
                'lemburkaryawan_working_days' => $calculate_overtime['working_days'], // in days
                'lemburkaryawan_baseovertime' => $calculate_overtime['baseovertime'], // in rupiahs
                'lemburkaryawan_salary' => $calculate_overtime['salary'], // in rupiahs
                'lemburkaryawan_total_time' => $calculate_overtime['total_duration'], // in minutes
                'lemburkaryawan_overtime_amount' => $calculate_overtime['overtime_amount'],
                'lemburkaryawan_total_overtime_amount' => $calculate_overtime['total_overtime_amount'],
                'lemburkaryawan_sttunjangankaryawan_data' => ($calculate_overtime['sttunjangan']) ? json_encode($calculate_overtime['sttunjangan']) : null,
                'lemburkaryawan_stpenggajiankaryawan_data' => ($calculate_overtime['stpenggajian']) ? json_encode($calculate_overtime['stpenggajian']) : null,
                'lemburkaryawan_stattendancekaryawan_data' => ($calculate_overtime['stattendance']) ? json_encode($calculate_overtime['stattendance']) : null,
                'lemburkaryawan_stlemburkaryawan_data' => json_encode($calculate_overtime['stlembur']),
            ]);

            //temporary hard code, it will check auto by group karyawan

            // if($manager_id !== null) {
            //     $getManager = KaryawanModel::where('karyawan_id', $manager_id)->first();

            //     $currentHour = date('G'); // Get the current hour in 24-hour format

            //     if ($currentHour >= 0 && $currentHour < 12) {
            //         $timeOfDay = 'Pagi';
            //     } elseif ($currentHour >= 12 && $currentHour < 18) {
            //         $timeOfDay = 'Siang';
            //     } else {
            //         $timeOfDay = 'Malam';
            //     }

            //     // Format the start_date from YYYY-MM-DD to DD Month YYYY
            //     $startDate = date('d F Y', strtotime($createCuti->leavekaryawan_start_date));
                
            //     NotificationModel::create([
            //         'notification_title' => 'Rekkaa - Pengajuan Cuti',
            //         'notification_type' => 'EMAIL',
            //         // 'notification_from' => env("MAIL_FROM_ADDRESS"),
            //         'notification_from' => env("MAIL_FROM_ADDRESS"),
            //         'notification_to' => $getManager->karyawan_email,
            //         'ms_user_id' => $getManager->ms_user_id,
            //         'ms_wajibpajak_id' => $getManager->ms_wajibpajak_id,
            //         'notification_view' => 'email.email-request-leave',
            //         'notification_data' => json_encode([
            //             'content' => [
            //                 'time' => $timeOfDay,
            //                 'manager_name' => $getManager->karyawan_name,
            //                 'staff_name' => $getKaryawan->karyawan_name,
            //                 'start_date' => $startDate,
            //                 'quota_use' => $createCuti->leavekaryawan_another_id !== null ? $createCuti->leavekaryawan_quota_use + $createCuti->leavekaryawan_another_quota_use : $createCuti->leavekaryawan_quota_use,
            //                 'reason' => $createCuti->leavekaryawan_request_note
            //             ],
            //             'st_leave_id' => $request->input('st_leave_id'),
            //             'leavekaryawan_id' => $createCuti->leavekaryawan_id 
            //         ])
            //     ]);
            // }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan Pengajuan Lembur.',
                'data' => $createLembur
            ], 200);
        } catch (Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }  
    }

    public function update($lemburId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawan_id = $request->input('karyawan_ids');
        $lemburkaryawan_start_time = $request->input('lemburkaryawan_start_time');
        $lemburkaryawan_end_time = $request->input('lemburkaryawan_end_time');

        $validator = Validator::make($request->all(), [
            'lemburkaryawan_start_time' => 'required',
            'lemburkaryawan_end_time' => 'required',
            'lemburkaryawan_request_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        // dd($request->all());
        $start_time = Carbon::parse($lemburkaryawan_start_time);
        $start_timeymdhis = $start_time->format('Y-m-d H:i:s');
        $start_timeym = $start_time->format('Y-m');
        $start_timeymd = $start_time->format('Y-m-d');
        $start_timedmy = $start_time->format('d-m-Y');
        $end_time = Carbon::parse($lemburkaryawan_end_time);
        $end_timeym = $end_time->format('Y-m');
        $end_timeymd = $end_time->format('Y-m-d');
        $end_timeymdhis = $end_time->format('Y-m-d H:i:s');
        $total_duration = $end_time->diffInMinutes($start_time);

        if(strtotime($start_timeymdhis) > strtotime($end_timeymdhis)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal mulai harus lebih kecil dari tanggal selesai.',
            ], 200);
        }

        // check existing date
        $existinglembur = LemburKaryawanModel::where(['ms_karyawan_id' => $karyawan_id, 'ms_wajibpajak_id' => $wajibpajak_id])
        ->whereNotIn('lemburkaryawan_status', ['REJECTED','CANCEL'])
        ->whereNotIn('lemburkaryawan_id', [$lemburId])
        ->whereRaw("(TO_CHAR(lemburkaryawan_start_time, 'YYYY-MM-DD') = ?)", [$start_timeymd])->first();
        // dd($existinglembur);
        if($existinglembur) {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan lembur sudah ada di tanggal '.$start_timedmy.'. Silahkan ajukan ditanggal lain!',
            ], 200);
        }

        $lembur = LemburKaryawanModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'lemburkaryawan_id' => $lemburId])
        ->whereIn('lemburkaryawan_status', ['APPROVEDMANAGER'])->first();

        $karyawan = KaryawanModel::select("ms_karyawan.*"
        ,   DB::raw("( SELECT row_to_json(statttable.*) AS row_to_json
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
                     sa.attendance_is_used,
                     sa.attendance_status_active
                    FROM st_attendance sa
                   WHERE sa.ms_wajibpajak_id = ms_karyawan.ms_wajibpajak_id AND sa.attendance_id = ms_karyawan.st_attendance_id
                  LIMIT 1) statttable) AS stattendancetbl"))->where(['karyawan_id' => $karyawan_id, 'ms_wajibpajak_id' => $wajibpajak_id])->first();

        // dd($karyawan);
        $karyawan_id = $karyawan->karyawan_id;
        $manager_id = $karyawan->karyawan_manager_id;
        $attendance = ($karyawan->stattendancetbl) ? json_decode($karyawan->stattendancetbl) : null;
        
        $start_timemy = $start_time->format('m-Y');
        $payroll = PayrollModel::whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$start_timemy])
        ->where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_karyawan_id' => $karyawan_id])->first();
        
        if($payroll) {
            if($payroll->payroll_lock == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Penggajian periode '.$start_timemy.' sudah di locked. Silahkan ajukan lembur dengan tanggal di periode selanjutnya.',
                ], 200);
            }
        }

        
        if(!$karyawan->penggajian) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan penggajian tidak ditemukan.',
            ], 200);
        }

        $lemburkaryawanctrl = new KaryawanLemburController();
        $calculate_overtime = $lemburkaryawanctrl->calculateovertime($start_time, $end_time, $karyawan, $attendance);
        if(isset($stlembur['success']) && $stlembur['success'] == false) {
            return response()->json([
                'success' => $stlembur['success'],
                'message' => $stlembur['message'],
            ], 200);
        }

        DB::beginTransaction();
        try {
            
            if($manager_id == 0) {
                $manager_id = null;
            }

            $lembur->update([
                'ms_karyawan_id' => $karyawan_id,
                'ms_wajibpajak_id' => $wajibpajak_id,
                'lemburkaryawan_start_time' => $start_timeymdhis,
                'lemburkaryawan_end_time' => $end_timeymdhis,
                'lemburkaryawan_manager_id' => $manager_id,
                'lemburkaryawan_request_note' => $request->input('lemburkaryawan_request_note'),
                'lemburkaryawan_status' => 'APPROVEDMANAGER',
                'lemburkaryawan_approval_date' => date('Y-m-d H:i:s'),
                'lemburkaryawan_prorate_days' => $calculate_overtime['prorate_days'], // in days
                'lemburkaryawan_working_days' => $calculate_overtime['working_days'], // in days
                'lemburkaryawan_baseovertime' => $calculate_overtime['baseovertime'], // in rupiahs
                'lemburkaryawan_salary' => $calculate_overtime['salary'], // in rupiahs
                'lemburkaryawan_total_time' => $calculate_overtime['total_duration'], // in minutes
                'lemburkaryawan_overtime_amount' => $calculate_overtime['overtime_amount'],
                'lemburkaryawan_total_overtime_amount' => $calculate_overtime['total_overtime_amount'],
                'lemburkaryawan_sttunjangankaryawan_data' => ($calculate_overtime['sttunjangan']) ? json_encode($calculate_overtime['sttunjangan']) : null,
                'lemburkaryawan_stpenggajiankaryawan_data' => ($calculate_overtime['stpenggajian']) ? json_encode($calculate_overtime['stpenggajian']) : null,
                'lemburkaryawan_stattendancekaryawan_data' => ($calculate_overtime['stattendance']) ? json_encode($calculate_overtime['stattendance']) : null,
                'lemburkaryawan_stlemburkaryawan_data' => json_encode($calculate_overtime['stlembur']),
            ]);

            //temporary hard code, it will check auto by group karyawan

            // if($manager_id !== null) {
            //     $getManager = KaryawanModel::where('karyawan_id', $manager_id)->first();

            //     $currentHour = date('G'); // Get the current hour in 24-hour format

            //     if ($currentHour >= 0 && $currentHour < 12) {
            //         $timeOfDay = 'Pagi';
            //     } elseif ($currentHour >= 12 && $currentHour < 18) {
            //         $timeOfDay = 'Siang';
            //     } else {
            //         $timeOfDay = 'Malam';
            //     }

            //     // Format the start_date from YYYY-MM-DD to DD Month YYYY
            //     $startDate = date('d F Y', strtotime($createCuti->leavekaryawan_start_date));
                
            //     NotificationModel::create([
            //         'notification_title' => 'Rekkaa - Pengajuan Cuti',
            //         'notification_type' => 'EMAIL',
            //         // 'notification_from' => env("MAIL_FROM_ADDRESS"),
            //         'notification_from' => env("MAIL_FROM_ADDRESS"),
            //         'notification_to' => $getManager->karyawan_email,
            //         'ms_user_id' => $getManager->ms_user_id,
            //         'ms_wajibpajak_id' => $getManager->ms_wajibpajak_id,
            //         'notification_view' => 'email.email-request-leave',
            //         'notification_data' => json_encode([
            //             'content' => [
            //                 'time' => $timeOfDay,
            //                 'manager_name' => $getManager->karyawan_name,
            //                 'staff_name' => $getKaryawan->karyawan_name,
            //                 'start_date' => $startDate,
            //                 'quota_use' => $createCuti->leavekaryawan_another_id !== null ? $createCuti->leavekaryawan_quota_use + $createCuti->leavekaryawan_another_quota_use : $createCuti->leavekaryawan_quota_use,
            //                 'reason' => $createCuti->leavekaryawan_request_note
            //             ],
            //             'st_leave_id' => $request->input('st_leave_id'),
            //             'leavekaryawan_id' => $createCuti->leavekaryawan_id 
            //         ])
            //     ]);
            // }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil melakukan perubahan Pengajuan Lembur.',
                'data' => $lembur
            ], 200);
        } catch (Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }  
    }
}