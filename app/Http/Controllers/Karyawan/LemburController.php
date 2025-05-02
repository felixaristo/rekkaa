<?php

namespace App\Http\Controllers\Karyawan;

use App\Http\Controllers\Controller;
use App\Libraries\AppDurationLibrary;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingHolidayModel;
use App\Model\Setting\SettingLemburKaryawanModel;
use App\Model\Setting\SettingModel;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use App\Model\Transaction\LemburKaryawanModel;
use App\Model\Transaction\PayrollModel;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LemburController extends Controller
{
    public function indexEmployee(Request $request)
    {
        $data = [
            'title' => 'Pengajuan Lembur',
            'content' => 'karyawan.lembur.lembur-karyawan',
        ];
        
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('karyawan.index', $data);
        }
    }

    public function overtimeEmployee(Request $request)
    {
        $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
        $status = $request->input('status');
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
            'ms_karyawan_id' => $karyawan_id,
        ];
        if($status) {
            $where['lemburkaryawan_status'] = $status;
        }
        
        $list = LemburKaryawanModel::select('tr_lembur_karyawan.*')
        ->with(['manager', 'admin', 'admin.userwajibpajak', 'canceladmin', 'canceladmin.userwajibpajak'])->where(function($q) use ($start_date) {
            return $q->where(DB::raw("TO_CHAR(lemburkaryawan_created_at, 'YYYY-MM')"), '=', Carbon::parse($start_date)->format('Y-m'));
        })
        ->where($where)
        ->orderBy($order_col, $order_type)
        ->take($limit)->skip($offset)->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = LemburKaryawanModel::where($where)->where(function($q) use ($start_date) {
            return $q->where(DB::raw("TO_CHAR(lemburkaryawan_created_at, 'YYYY-MM')"), '=', Carbon::parse($start_date)->format('Y-m'));
        })->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function indexManager(Request $request)
    {
        // Check if karyawan_ismanager is equal to 0
        if(request()->get('karyawan')->karyawan_ismanager == 0) {
            // Redirect to the desired URL
            return redirect(url('karyawan/cuti/view/karyawan'));
        }
        $data = [
            'title' => 'Daftar Lembur',
            'content' => 'karyawan.lembur.lembur-manager',
        ];

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('karyawan.index', $data);
        }
    }

    public function overtimeManager(Request $request)
    {
        $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
        $status = $request->input('status');
        $karyawan_ids = $request->input('karyawan_ids');
        $start_date = '01-'.$request->input('start_date');
        $order_columns = ['lemburkaryawan_id','lemburkaryawan_created_at','lemburkaryawan_start_time', 'lemburkaryawan_end_time','lemburkaryawan_status', 'lemburkaryawan_approval_date'];
        $order_col = $order_columns[0];
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $where = [
            'lemburkaryawan_manager_id' => $karyawan_id,
        ];
        if($status) {
            $where['lemburkaryawan_status'] = $status;
        }
        
        $list = LemburKaryawanModel::select('tr_lembur_karyawan.*')
        ->with(['karyawan', 'manager', 'admin', 'admin.userwajibpajak', 'canceladmin', 'canceladmin.userwajibpajak'])->where(function($q) use ($start_date) {
            return $q->where(DB::raw("TO_CHAR(lemburkaryawan_created_at, 'YYYY-MM')"), '=', Carbon::parse($start_date)->format('Y-m'));
        })
        ->when($karyawan_ids, function($q) use ($karyawan_ids) {
            return $q->whereIn('ms_karyawan_id', $karyawan_ids);
        })
        ->where($where)
        ->orderBy($order_col, $order_type)
        ->take($limit)->skip($offset)->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = LemburKaryawanModel::when($karyawan_ids, function($q) use ($karyawan_ids) {
            return $q->whereIn('ms_karyawan_id', $karyawan_ids);
        })
        ->where(function($q) use ($start_date) {
            return $q->where(DB::raw("TO_CHAR(lemburkaryawan_created_at, 'YYYY-MM')"), '=', Carbon::parse($start_date)->format('Y-m'));
        })
        ->where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }
    
    public function selectEmployee(Request $request)
    {
        $q = $request->get('q');
        $karyawan_id = $request->get('karyawan_id');
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $where = [
            'karyawan_active' => 1,
            'ms_wajibpajak_id' => session()->get('karyawan_data')['wajibpajak_id'],
            'karyawan_manager_id' => session()->get('karyawan_data')['karyawan_id'],
        ];
        // if($is_manager == '1') {
        //     $where['karyawan_ismanager'] = 1;
        //     $data = KaryawanModel::select('karyawan_id', 'karyawan_name', 'karyawan_nik', 'karyawan_npwp')
        //     ->when($q, function ($query, $q) {
        //         return $query->where('karyawan_name', 'ilike', '%'.$q.'%');
        //     })
        //     ->where($where)
            
        //     ->limit($limit)->get();
        // } else {
        $data = KaryawanModel::select('karyawan_id', 'karyawan_name', 'karyawan_nik', 'karyawan_npwp')
        ->when($q, function ($query, $q) {
            return $query->where('karyawan_name', 'ilike', '%'.$q.'%');
        })
        ->when($karyawan_id, function($query, $karyawan_id) {
            return $query->whereNotIn('karyawan_id', [$karyawan_id]);
        })
        ->where($where)
        ->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
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
        // dd($start_timeymdhis, $end_timeymdhis);
        if(strtotime($start_timeymdhis) >= strtotime($end_timeymdhis)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal mulai harus lebih kecil dari tanggal selesai.',
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
                     sa.attendance_status_active,
                     sa.attendance_is_used,
                    FROM st_attendance sa
                   WHERE sa.ms_wajibpajak_id = ms_karyawan.ms_wajibpajak_id AND sa.attendance_id = ms_karyawan.st_attendance_id
                  LIMIT 1) statttable) AS stattendancetbl"))->where('karyawan_id', $karyawan_id)->first();

        $wajibpajak_id = $karyawan->ms_wajibpajak_id;
        $karyawan_id = $karyawan->karyawan_id;
        $manager_id = $karyawan->karyawan_manager_id;
        $attendance = ($karyawan->stattendancetbl) ? json_decode($karyawan->stattendancetbl) : null;

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

        if(!$karyawan->penggajian) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan penggajian tidak ditemukan.',
            ], 200);
        }

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

        $calculate_overtime = $this->calculateovertime($start_time, $end_time, $karyawan, $attendance);
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
                'lemburkaryawan_status' => 'WAITING',
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
        $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
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
        $lembur = LemburKaryawanModel::where(['ms_karyawan_id' => $karyawan_id, 'lemburkaryawan_id' => $lemburId])
        ->whereIn('lemburkaryawan_status', ['WAITING'])->first();
        if(!$lembur) {
            return response()->json([
                'success' => false,
                'message' => 'Lembur tidak ditemukan.',
            ], 200);
        }
        
        // dd($request->all());
        $start_time = Carbon::parse($lemburkaryawan_start_time);
        $start_timeymdhis = $start_time->format('Y-m-d H:i:s');
        $start_timeym = $start_time->format('Y-m');
        $start_timeymd = $start_time->format('Y-m-d');
        $start_timedmy = $start_time->format('d-m-Y');
        $end_time = Carbon::parse($lemburkaryawan_end_time);
        $end_timeym = $end_time->format('Y-m');
        $end_timeymdhis = $end_time->format('Y-m-d H:i:s');
        $total_duration = $end_time->diffInMinutes($start_time);

        if(strtotime($start_timeymdhis) >= strtotime($end_timeymdhis)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal mulai harus lebih kecil dari tanggal selesai.',
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
                  LIMIT 1) statttable) AS stattendancetbl"))->where('karyawan_id', $karyawan_id)->first();

        $wajibpajak_id = $karyawan->ms_wajibpajak_id;
        $karyawan_id = $karyawan->karyawan_id;
        $manager_id = $karyawan->karyawan_manager_id;
        $attendance = ($karyawan->stattendancetbl) ? json_decode($karyawan->stattendancetbl) : null;

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

        $calculate_overtime = $this->calculateovertime($start_time, $end_time, $karyawan, $attendance);
        // dd($calculate_overtime);
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
                'lemburkaryawan_status' => 'WAITING',
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

    public function cancel($lemburId, Request $request)
    {
        $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
        $lemburkaryawan_cancel_note = $request->input('lemburkaryawan_cancel_note');

        $validator = Validator::make($request->all(), [
            'lemburkaryawan_cancel_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        // dd($request->all());
        $lembur = LemburKaryawanModel::where(['ms_karyawan_id' => $karyawan_id, 'lemburkaryawan_id' => $lemburId])
        ->whereIn('lemburkaryawan_status', ['WAITING', 'APPROVEDMANAGER'])->first();

        $wajibpajak_id = $lembur->ms_wajibpajak_id;
        $karyawan_id = $lembur->ms_karyawan_id;

        DB::beginTransaction();
        try {
            $lembur->update([
                'lemburkaryawan_cancel_note' => $lemburkaryawan_cancel_note,
                'lemburkaryawan_cancel_date' => date('Y-m-d H:i:s'),
                'lemburkaryawan_cancel_by' => $karyawan_id,
                'lemburkaryawan_status' => 'CANCEL',
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

    public function cancelmanager($lemburId, Request $request)
    {
        // $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
        $lemburkaryawan_cancel_note = $request->input('lemburkaryawan_cancel_note');

        $validator = Validator::make($request->all(), [
            'lemburkaryawan_cancel_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        // dd($request->all());
        $lembur = LemburKaryawanModel::where(['lemburkaryawan_id' => $lemburId])
        ->whereIn('lemburkaryawan_status', ['WAITING', 'APPROVEDMANAGER'])->first();

        $wajibpajak_id = $lembur->ms_wajibpajak_id;
        $karyawan_id = $lembur->ms_karyawan_id;

        DB::beginTransaction();
        try {
            $lembur->update([
                'lemburkaryawan_cancel_note' => $lemburkaryawan_cancel_note,
                'lemburkaryawan_cancel_date' => date('Y-m-d H:i:s'),
                'lemburkaryawan_cancel_by' => $karyawan_id,
                'lemburkaryawan_status' => 'CANCEL',
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

    public function rejectmanager($lemburId, Request $request)
    {
        // $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
        $lemburkaryawan_cancel_note = $request->input('lemburkaryawan_cancel_note');

        $validator = Validator::make($request->all(), [
            'lemburkaryawan_cancel_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        // dd($request->all());
        $lembur = LemburKaryawanModel::where(['lemburkaryawan_id' => $lemburId])
        ->whereIn('lemburkaryawan_status', ['WAITING', 'APPROVEDMANAGER'])->first();

        $wajibpajak_id = $lembur->ms_wajibpajak_id;
        $karyawan_id = $lembur->ms_karyawan_id;

        DB::beginTransaction();
        try {
            $lembur->update([
                'lemburkaryawan_cancel_note' => $lemburkaryawan_cancel_note,
                'lemburkaryawan_cancel_date' => date('Y-m-d H:i:s'),
                'lemburkaryawan_cancel_by' => $karyawan_id,
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

    public function approvemanager($lemburId, Request $request)
    {
        $karyawan_id = session()->get('karyawan_data')['karyawan_id'];
        $lemburkaryawan_approval_note = $request->input('lemburkaryawan_approval_note');

        $validator = Validator::make($request->all(), [
            'lemburkaryawan_approval_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        // dd($request->all());
        $lembur = LemburKaryawanModel::where(['lemburkaryawan_manager_id' => $karyawan_id, 'lemburkaryawan_id' => $lemburId])
        ->whereIn('lemburkaryawan_status', ['WAITING'])->first();

        $wajibpajak_id = $lembur->ms_wajibpajak_id;
        $karyawan_id = $lembur->ms_karyawan_id;

        DB::beginTransaction();
        try {
            $lembur->update([
                'lemburkaryawan_approval_note' => $lemburkaryawan_approval_note,
                'lemburkaryawan_approval_date' => date('Y-m-d H:i:s'),
                'lemburkaryawan_approval_by' => $karyawan_id,
                'lemburkaryawan_status' => 'APPROVEDMANAGER',
            ]);

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

    // public function exportManager(Request $request)
    // {
    //     try {
    //         $query = LeaveKaryawanModel::query();
    //     $startDate = $request->input('start_date');
    //     $endDate = $request->input('end_date');

    //     if ($startDate && $endDate) {
    //         // Apply date filtering to the query
    //         $endDate = $request->input('end_date');
    //         $endDateObj = new DateTime($endDate);
    //         $endDateObj->modify('+1 day');
    //         $endDate = $endDateObj->format('Y-m-d');
    //         $formattedStartDate = date('Y-m-d', strtotime($startDate));

    //         $query->whereBetween('leavekaryawan_request_date', [$formattedStartDate, $endDate]);
    //     }

    //     $query->select(
    //         'ms_karyawan.karyawan_id',
    //         'ms_karyawan.karyawan_name',
    //         'tr_leave_karyawan.leavekaryawan_approval_date',
    //         'tr_leave_karyawan.leavekaryawan_approval_note',
    //         'tr_leave_karyawan.leavekaryawan_end_date',
    //         'tr_leave_karyawan.leavekaryawan_half_leave_status',
    //         'tr_leave_karyawan.leavekaryawan_id',
    //         'tr_leave_karyawan.leavekaryawan_manager_id',
    //         'tr_leave_karyawan.leavekaryawan_quota_use',
    //         'tr_leave_karyawan.leavekaryawan_request_date',
    //         'tr_leave_karyawan.leavekaryawan_request_note',
    //         'tr_leave_karyawan.leavekaryawan_start_date',
    //         'tr_leave_karyawan.leavekaryawan_status',
    //         'tr_leave_karyawan.st_leave_id',
    //         'tr_leave_karyawan.leavekaryawan_cancel_note',
    //         'tr_leave_karyawan.leavekaryawan_is_hide',
    //     );

    //     // Join with ms_karyawan to retrieve karyawan_name
    //     $query->join('ms_karyawan', 'tr_leave_karyawan.ms_karyawan_id', '=', 'ms_karyawan.karyawan_id');

    //     $getKaryawan = KaryawanModel::where('karyawan_id', session()->get('karyawan_data')['karyawan_id'])->first();
    //     $karyawan_id = $getKaryawan->karyawan_id;

    //     $leaveData = $query
    //         ->where('tr_leave_karyawan.leavekaryawan_manager_id', $karyawan_id)
    //         ->where('tr_leave_karyawan.leavekaryawan_is_hide', false)
    //         ->with(['st_leave' => function ($query) {
    //             $query->select('leave_id', 'leave_description'); // Select only the desired columns
    //         }])
    //         ->orderBy('leavekaryawan_request_date', 'desc')->get();


    //         $unixtime = time();
    //         $title = 'Daftar_Cuti_'.$unixtime;

    //         $htmlString = view('karyawan.cuti.export-manager', ['title' => $title,'cuti' => $leaveData])->render();

    //         $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
    //         $spreadsheet = $reader->loadFromString($htmlString);

    //         $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
    //         $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);

    //         $writer = new Xls($spreadsheet);

    //         $filename = $title.'.xls';
    //         $location = public_path('assets/export/');
    //         ob_start();
    //         $writer->save($location.$filename);
    //         // $xlsData = ob_get_contents();
    //         ob_end_clean();

    //         return url('/assets/export/'.$filename);
    //     } catch (Error $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function storemanager(Request $request)
    {
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
        $end_timeymdhis = $end_time->format('Y-m-d H:i:s');
        $total_duration = $end_time->diffInMinutes($start_time);

        if(strtotime($start_timeymdhis) >= strtotime($end_timeymdhis)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal mulai harus lebih kecil dari tanggal selesai.',
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
                     sa.attendance_status_active,
                     sa.attendance_is_used,
                    FROM st_attendance sa
                   WHERE sa.ms_wajibpajak_id = ms_karyawan.ms_wajibpajak_id AND sa.attendance_id = ms_karyawan.st_attendance_id
                  LIMIT 1) statttable) AS stattendancetbl"))->where(['karyawan_id' => $karyawan_id])->first();

        // dd($karyawan);
        $karyawan_id = $karyawan->karyawan_id;
        $wajibpajak_id = $karyawan->ms_wajibpajak_id;
        $manager_id = $karyawan->karyawan_manager_id;
        $attendance = ($karyawan->stattendancetbl) ? json_decode($karyawan->stattendancetbl) : null;

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

        $calculate_overtime = $this->calculateovertime($start_time, $end_time, $karyawan, $attendance);
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

    public function updatemanager($lemburId, Request $request)
    {
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
        $lembur = LemburKaryawanModel::where(['ms_karyawan_id' => $karyawan_id, 'lemburkaryawan_id' => $lemburId])
        ->whereIn('lemburkaryawan_status', ['APPROVEDMANAGER','WAITING'])
        ->first();
        if(!$lembur) {
            return response()->json([
                'success' => false,
                'message' => 'Lembur tidak ditemukan.',
            ], 200);
        }
        
        // dd($request->all());
        $start_time = Carbon::parse($lemburkaryawan_start_time);
        $start_timeymdhis = $start_time->format('Y-m-d H:i:s');
        $start_timeym = $start_time->format('Y-m');
        $start_timeymd = $start_time->format('Y-m-d');
        $start_timedmy = $start_time->format('d-m-Y');
        $end_time = Carbon::parse($lemburkaryawan_end_time);
        $end_timeym = $end_time->format('Y-m');
        $end_timeymdhis = $end_time->format('Y-m-d H:i:s');
        $total_duration = $end_time->diffInMinutes($start_time);

        if(strtotime($start_timeymdhis) >= strtotime($end_timeymdhis)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal mulai harus lebih kecil dari tanggal selesai.',
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
                     sa.attendance_status_active,
                     sa.attendance_is_used,
                    FROM st_attendance sa
                   WHERE sa.ms_wajibpajak_id = ms_karyawan.ms_wajibpajak_id AND sa.attendance_id = ms_karyawan.st_attendance_id
                  LIMIT 1) statttable) AS stattendancetbl"))->where('karyawan_id', $karyawan_id)->first();

        $wajibpajak_id = $karyawan->ms_wajibpajak_id;
        $karyawan_id = $karyawan->karyawan_id;
        $manager_id = $karyawan->karyawan_manager_id;
        $attendance = ($karyawan->stattendancetbl) ? json_decode($karyawan->stattendancetbl) : null;

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

        $calculate_overtime = $this->calculateovertime($start_time, $end_time, $karyawan, $attendance);
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

    public function calculateovertime($start_time, $end_time, $karyawan, $attendance)
    {
        $total_duration = $end_time->diffInMinutes($start_time); // in minutes
        $total_working_overtime = ceil($total_duration / 60); // in hours;
        $wajibpajak_id = $karyawan->ms_wajibpajak_id;
        $stpenggajian = $karyawan->penggajian;
        $contract_begin = $karyawan->karyawan_contract_begin;
        $start_timed = $start_time->format('d');
        $start_timem = $start_time->format('m');
        $start_timey = $start_time->format('Y');
        $start_timeym = $start_time->format('Y-m');
        $start_timeymd = $start_time->format('Y-m-d');

        $end_timed = $end_time->format('d');
        $end_timem = $end_time->format('m');
        $end_timey = $end_time->format('y');
        $end_timeym = $end_time->format('Y-m');
        $end_timeymd = $end_time->format('Y-m-d');

        $attendance_days = [];
        $working_start_time = Carbon::parse('00:00:00');
        $working_end_time = Carbon::parse('00:00:00');

        // check if match
        $day = ucwords(date('l', strtotime($start_time)));
        // echo $day;
        $attendance_days = json_decode($attendance->attendance_working_day);
        $working_start_time = Carbon::parse($attendance->attendance_check_in);
        $working_end_time = Carbon::parse($attendance->attendance_check_out);
        
        // dd($day, $attendance_days);
        $total_working_minutes = $working_end_time->diffInMinutes($working_start_time);
        $total_working_hours = ceil($total_working_minutes / 60);
        $prorate_days = 0;
        $working_days = 0;
        if($stpenggajian->stpenggajiankaryawan_method == 'TETAP') {
            $prorate_days = $stpenggajian->stpenggajiankaryawan_day;
            $t_days = cal_days_in_month(CAL_GREGORIAN, $start_timem, $start_timey);
            $sd = $start_timeym.'-01';
            $ed = $start_timeym.'-'.$t_days;
            while(strtotime($sd) <= strtotime($ed)) {
                if(strtotime($sd) >= strtotime($contract_begin)) {
                    $working_days += 1;
                }
                $sd = date("Y-m-d", strtotime("+1 day", strtotime($sd)));
            }
            $working_days = ($working_days > $prorate_days) ? $prorate_days : $working_days;
        } else if($stpenggajian->stpenggajiankaryawan_method == 'KALENDER') {
            $prorate_days = cal_days_in_month(CAL_GREGORIAN, $start_timem, $start_timey);
            $sd = $start_timeym.'-01';
            $ed = $start_timeym.'-'.$prorate_days;
            while(strtotime($sd) <= strtotime($ed)) {
                if(strtotime($sd) >= strtotime($contract_begin)) {
                    $working_days += 1;
                }
                $sd = date("Y-m-d", strtotime("+1 day", strtotime($sd)));
            }
            $working_days = ($working_days > $prorate_days) ? $prorate_days : $working_days;
        } else if($stpenggajian->stpenggajiankaryawan_method == 'KERJA') {
            if($stpenggajian->stpenggajiankaryawan_period == 'TANGGAL') {
                if($stpenggajian->stpenggajiankaryawan_enddate <= $stpenggajian->stpenggajiankaryawan_startdate) {
                    // get days from previous month
                    $prev_month = date('Y-m', strtotime(date('Y-m', strtotime($start_time))." -1 month"));
                    $prev_date = str_pad($stpenggajian->stpenggajiankaryawan_startdate, 2, "0", STR_PAD_LEFT);
                    $prev_start_date = $prev_month.'-'.$prev_date;
                    $prev_end_date = $prev_month.'-'.date('t', strtotime($prev_start_date));
                    
                    $current_month = date('Y-m', strtotime($start_time));
                    $current_date = str_pad($stpenggajian->stpenggajiankaryawan_enddate, 2, "0", STR_PAD_LEFT);
                    $current_start_date = $current_month.'-01';
                    $current_end_date = $current_month.'-'.$current_date;
                    while(strtotime($prev_start_date) <= strtotime($prev_end_date)) {
                                
                        // check if match
                        $day = ucwords(date('l', strtotime($prev_start_date)));
                        if(count($attendance_days) > 0) {
                            if(in_array($day, $attendance_days)) {
                                $prorate_days += 1;
                                if(strtotime($prev_start_date) >= strtotime($contract_begin)) {
                                    $working_days += 1;
                                }
                            }
                        }
                        $prev_start_date = date("Y-m-d", strtotime("+1 day", strtotime($prev_start_date)));
                    }
                    while(strtotime($current_start_date) <= strtotime($current_end_date)) {
                                
                        // check if match
                        $day = ucwords(date('l', strtotime($current_start_date)));
                        // echo $day;
                        if(count($attendance_days) > 0) {
                            if(in_array($day, $attendance_days)) {
                                $prorate_days += 1;
                                if(strtotime($current_start_date) >= strtotime($contract_begin)) {
                                    $working_days += 1;
                                }
                            }
                        }
                        $current_start_date = date("Y-m-d", strtotime("+1 day", strtotime($current_start_date)));
                    }
                } else {
                    $prorate_days = $stpenggajian->stpenggajiankaryawan_enddate - $stpenggajian->stpenggajiankaryawan_startdate;
                }
            } else if($stpenggajian->stpenggajiankaryawan_period == 'KALENDER') {
                $start_date = date('Y-m', strtotime($start_time)).'-01';
                $end_date = date('Y-m', strtotime($start_time)).'-'.date('t', strtotime($start_date));
                while(strtotime($start_date) <= strtotime($end_date)) {
                    // dd($start_date, $end_date, $attendance_days, $holy_date, $absence_dates, $trattendances, $contract_begin);
                    
                    // check if match
                    $day = ucwords(date('l', strtotime($start_date)));
                    // echo $day;
                    
                    if(count($attendance_days) > 0) {
                        if(in_array($day, $attendance_days)) {
                            $prorate_days += 1;
                            if(strtotime($start_date) >= strtotime($contract_begin)) {
                                $working_days += 1;
                            }
                        }
                    }

                    $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
                }
            }
        }

        // dd($attendance);
        if($attendance->attendance_is_used == 0) {
            $working_days = $prorate_days;
        }
        
        $currentday = ucwords(date('l', strtotime($start_timeymd)));
        // dd($attendance_days, $currentday);
        $stlemburtype = 0;
        if(count($attendance_days) > 0) {
            $wherestholiday = ['holiday_status_active' => true, 'ms_wajibpajak_id' => $wajibpajak_id];
            $holiday_karyawan = SettingHolidayModel::where($wherestholiday)->whereRaw(DB::raw("TO_CHAR(holiday_start_date, 'YYYY') = ?"), [$start_timey])->get();
            if(in_array($currentday, $attendance_days)) {
                
                $stlemburtype = 1;
                // dd($stlemburtype);
                // check holiday
                foreach($holiday_karyawan as $hk) {
                    if($start_timeymd >= $hk->holiday_start_date && $start_timeymd <= $hk->holiday_end_date) {
                        $stlemburtype = 3;
                        break;
                    }
                }
            } else { // hari minggu
                $stlemburtype = 4;
                
                // check holiday
                foreach($holiday_karyawan as $hk) {
                    if($start_timeymd >= $hk->holiday_start_date && $start_timeymd <= $hk->holiday_end_date) {
                        $stlemburtype = 2;
                        break;
                    }
                }
            }
        }

        $stlemburkaryawan = SettingLemburKaryawanModel::where([
            'stlemburkaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->orderBy('stlemburkaryawan_id', 'ASC')->first();
        if(!$stlemburkaryawan) {
            return [
                'success' => false,
                'message' => 'Pengaturan lembur karyawan tidak ditemukan.',
            ];
        }

        $setting_lembur = SettingModel::where('setting_active', 1)->where('setting_key', 'LEMBUR')->first();
        $stlembur = json_decode($setting_lembur->setting_value); 
        usort($stlembur, function($a, $b) {
            return $b->period <=> $a->period;
        });
        $stlmburcurrent = $stlembur[0];
        $stlmburcurrent->taxable = $stlemburkaryawan->stlemburkaryawan_taxable;
        // dd($stlemburtype, $stlembur[0]->value);
        $stlemburtype = 4;
        $stlemburnew = null;
        foreach($stlmburcurrent->value as $val) {
            if($val->stlemburkaryawan_type == $stlemburtype) {
                $stlemburnew = $val;
                break;
            }
        }
        // dd($stlemburnew);
        if(!$stlemburnew) {
            return [
                'success' => false,
                'message' => 'Pengaturan lembur sistem tidak ditemukan. Silahkan hubungi admin!',
            ];
        }
        // hardcode, should be fixed
        $nilailembur = $stlemburnew->stlemburkaryawan_value;
        // $basevaluelembur = json_decode($stlemburnew->stlemburkaryawan_basevalue);
        $baselembur = 0;
        // dd($basevaluelembur, $nilailembur);
        $grouptunjanganids = [];
        // foreach($basevaluelembur as $blm) {
            // if($blm->name == 'GAJI_POKOK') {
                $baselembur += $karyawan->karyawan_salary * $working_days / $prorate_days;
            // } else if($blm->name == 'GROUP_TUNJANGAN') {
            //     array_push($grouptunjanganids, $blm->value);
            // }
        // }

        $tunjangans = null;
        // if($grouptunjanganids) {
        //     $tunjangans = SettingTunjanganKaryawanModel::whereIn('st_grouptunjangankaryawan_id', $grouptunjanganids)->where(['sttunjangankaryawan_active' => 1])->get();
        //     // dd($baselembur, $tunjangans);
        //     foreach($tunjangans as $tj) {
        //         if($tj->sttunjangankaryawan_period == 'BULAN') {
        //             $baselembur += $tj->sttunjangankaryawan_value * $working_days / $prorate_days;
        //         } else if($tj->sttunjangankaryawan_period == 'HARI') {
        //             $baselembur += $tj->sttunjangankaryawan_value * $working_days;
        //         }
        //     }
        // }

        // dd($nilailembur);
        $jam_awals = [];
        $jam_akhirs = [];
        $xupahs = [];
        foreach($nilailembur as $nlembur) {
            array_push($jam_awals, $nlembur->jam_awal);
            array_push($jam_akhirs, $nlembur->jam_akhir);
            array_push($xupahs, $nlembur->upah);
        }
        sort($jam_awals);
        sort($jam_akhirs);

        $lemburkaryawan_overtime_amount = ceil(($baselembur / $prorate_days) / $total_working_hours);
        $lemburkaryawan_total_overtime_amount = 0;

        $progresive_hours = $total_working_overtime;
        $i = 0;
        $last_upah = 0;
        // dd($progresive_hours);
        // dd($jam_akhirs);
        foreach($jam_akhirs as $ak) {
            if($progresive_hours < 0) {
                break;
            }
            $diff = $ak - $jam_awals[$i];
            $diffhours = ($progresive_hours >= $diff) ? $diff : $progresive_hours;
            
            $lemburkaryawan_total_overtime_amount += ($lemburkaryawan_overtime_amount * $diffhours) * $xupahs[$i]; 
            
            $progresive_hours -= $diff;
            $last_upah = $xupahs[$i];
            $i++;
        }
        // dd($last_upah, $progresive_hours, $lemburkaryawan_overtime_amount, $lemburkaryawan_total_overtime_amount, 'oo');
        if($progresive_hours > 0) {
            $lemburkaryawan_total_overtime_amount += ($lemburkaryawan_overtime_amount * $progresive_hours) * $last_upah;
        }

        return [
            'prorate_days' => $prorate_days,
            'working_days' => $working_days,
            'total_duration' => $total_duration,
            'overtime_amount' => $lemburkaryawan_overtime_amount,
            'total_overtime_amount' => $lemburkaryawan_total_overtime_amount,
            'baseovertime' => $baselembur,
            'salary' => $karyawan->karyawan_salary,
            'stlembur' => $stlmburcurrent,
            'sttunjangan' => $tunjangans,
            'stattendance' => $attendance,
            'stpenggajian' => $stpenggajian,
        ];
    }
}
