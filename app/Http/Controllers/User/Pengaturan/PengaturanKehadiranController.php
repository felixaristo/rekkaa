<?php

namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingAttendanceModel;
use App\Model\Transaction\AttendanceKaryawanModel;
use Illuminate\Http\Request;
use Error;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PengaturanKehadiranController extends Controller
{
    public function index(Request $request)
    {
        $setting = SettingAttendanceModel::select('st_attendance.*')
            // ->where('attendance_status_active', TRUE)
            ->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
            ->orderBy('attendance_created_at', 'desc')
            ->first();

        $data = [
            'data'=> $setting,
            'title' => 'Pengaturan Absensi',
            'content' => 'user.pengaturan.kehadiran.jadwal',
            // 'wajib_pajak_user' => WajibPajakUserModel::where(['ms_user_id' => session()->get('user_data')['user_id']])->count()
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function datatable(Request $request)
    {
        try {
            $page = ($request->input('page')) ? intval($request->input('page')) : 1;
            $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
            $search = (isset($request->input('search')['value']) && $request->input('search')['value']) ? $request->input('search')['value'] : null;
            $order_columns = ['attendance_id','attendance_description','attendance_check_in','attendance_start_break','attendance_end_break','attendance_check_out','attendance_status_active'];
            $order_col = $order_columns[0];
            $order_type = 'asc';
            if(isset($request->input('order')[0])) {
                $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
                $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
                $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
            }
           
            $getAttendance = SettingAttendanceModel::
                when($search, function ($q, $search) {
                    $lowercaseSearch = strtolower($search);
                    return $q->where(function ($query) use ($lowercaseSearch) {
                        $query->where(DB::raw('LOWER(attendance_id::text)'), 'like', '%' . $lowercaseSearch . '%')
                        ->orWhere(DB::raw('LOWER(attendance_description)'), 'like', '%' . $lowercaseSearch . '%');
                    });
                })
                ->where(['ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'], 'attendance_status_active' => TRUE])
                ->orderBy($order_col, $order_type)
                ->paginate($perPage, ['*'], 'page', $page);

            foreach ($getAttendance as $attendance) {
                $karyawanData = KaryawanModel::where('st_attendance_id', $attendance->attendance_id)
                    ->select('karyawan_id', 'karyawan_name')
                    ->get();
            
                // Attach karyawanData to each attendance record
                $attendance->karyawan = $karyawanData;
            }

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data Setting Kehadiran.',
                'data' => $getAttendance->items(),
                'draw' => $request->input('draw'),
                'recordsFiltered' => $getAttendance->total(),
                'recordsTotal' => $getAttendance->total(),
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error get list jadwal ' . $e->getMessage());

            return response()->json(['error' => 'Error get jadwal. Please try again later.', 'message' => $e],  500);
        }
    }

    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'isEdit' => 'required|boolean',
                'attendance_description' => $request->input('isEdit') === false ? 'required|string' : '',
                'attendance_working_day' => $request->input('isEdit') === false ? 'required' : '',
                'attendance_check_in' => $request->input('isEdit') === false ? 'required|date_format:H:i' : '',
                'attendance_check_in_tolerance' => $request->input('isEdit') === false ? 'required|date_format:H:i' : '',
                'attendance_check_out' => $request->input('isEdit') === false ? 'required|date_format:H:i' : '',
                'attendance_break_status' => 'boolean',
                'attendance_start_break' => 'required_if:attendance_break_status,true|date_format:H:i',
                'attendance_end_break' => 'required_if:attendance_break_status,true|date_format:H:i',
                'attendance_check_in_status_photo' => 'boolean',
                'attendance_check_out_status_photo' => 'boolean',
                'attendance_break_status_photo' => 'boolean',
                'attendance_break_type' => 'required_if:attendance_break_status,true|in:SPECIFIC,DURATION',
                'attendance_is_used' => 'required',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            if($request->input('isEdit') === false) {
                $checkAttendanceDesc = SettingAttendanceModel::where('attendance_description', $request->input('attendance_description'))->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();

                if($checkAttendanceDesc) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Deskripsi Jadwal Kehadiran sudah digunakan, mohon gunakan yang lain.'
                    ], 400); 
                }

                $checkAnother = SettingAttendanceModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                    ->where('attendance_is_all', true)
                    ->first();

                $attendance_location_address = null;
                $attendance_location_longitude = null;
                $attendance_location_latitude = null;
                $attendance_location_status = null;

                // check if has permission then set the location
                if(in_array('TRACK_LOCATION', request()->get('permission_codes'))) {
                    $attendance_location_address = $request->input('attendance_location_address');
                    $attendance_location_longitude = $request->input('attendance_location_longitude');
                    $attendance_location_latitude = $request->input('attendance_location_latitude');
                    $attendance_location_status = $request->input('attendance_location_status');
                }

                $attendance_break_status_photo = false;
                $attendance_check_in_status_photo = false;
                $attendance_check_out_status_photo = false;
                // check if has permission to upload image
                if(in_array('UPLOAD_IMAGE', request()->get('permission_codes'))) {
                    $attendance_break_status_photo = $request->input('attendance_break_status_photo');
                    $attendance_check_in_status_photo = $request->input('attendance_check_in_status_photo');
                    $attendance_check_out_status_photo = $request->input('attendance_check_out_status_photo');
                }

                DB::beginTransaction();
                try {
                    $createAttendanceSetting = SettingAttendanceModel::create([
                        'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                        'attendance_working_day' => json_encode($request->input('attendance_working_day')),
                        'attendance_description' => 'Absensi',//$request->input('attendance_description'),
                        'attendance_check_in' => $request->input('attendance_check_in'),
                        'attendance_check_in_tolerance' => $request->input('attendance_check_in_tolerance'),
                        'attendance_check_out' => $request->input('attendance_check_out'),
                        'attendance_location_address' => $attendance_location_address,
                        'attendance_location_longitude' => $attendance_location_longitude,
                        'attendance_location_latitude' => $attendance_location_latitude,
                        'attendance_location_status' => $attendance_location_status,
                        'attendance_break_status' => $request->input('attendance_break_status') ? $request->input('attendance_break_status') : false,
                        'attendance_start_break' => $request->input('attendance_start_break'),
                        'attendance_end_break' => $request->input('attendance_end_break'),
                        'attendance_check_in_status_photo' => $attendance_check_in_status_photo,
                        'attendance_check_out_status_photo' => $attendance_check_out_status_photo,
                        'attendance_break_type' => $request->input('attendance_break_type'),
                        'attendance_break_status_photo' => $attendance_break_status_photo,
                        // 'attendance_status_active' => $request->input('attendance_status_active'),
                        'attendance_is_used' => $request->input('attendance_is_used'),
                        // 'attendance_is_all' => $request->input('attendance_is_all')
                        'attendance_is_all' => TRUE
                    ]);

                    // $attendanceEmployees = $request->input('attendance_employee');

                    // if($request->input('attendance_is_all') === true) {
                    //     KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])                                                    
                    //         ->where('karyawan_status', '!=', 'NONKARYAWAN')
                    //         ->update(['st_attendance_id' => $createAttendanceSetting->attendance_id]);
                    // } else {
                    //     if (is_array($attendanceEmployees) && count($attendanceEmployees) > 0) {
                    //         foreach ($attendanceEmployees as $index => $employeeId) {
                    //             // Assuming $employeeId is the ID of an employee in the KaryawanModel
                    //             $karyawan = KaryawanModel::find($employeeId);
                                
                    //             if ($karyawan) {
                    //                 $karyawan->st_attendance_id = $createAttendanceSetting->attendance_id;
                    //                 $karyawan->save();
                    //             }
                    //         }
                    //     }
                    // }
                    // dd($createAttendanceSetting);
                    KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])                                                    
                        ->where('karyawan_status', '!=', 'NONKARYAWAN')
                        ->update(['st_attendance_id' => $createAttendanceSetting->attendance_id]);

                    // if($checkAnother && $request->input('attendance_is_all') === true) {
                    //     $checkAnother->update([
                    //         'attendance_is_all' => false
                    //     ]);
                    // } else {
                    //     if(is_array($attendanceEmployees) && count($attendanceEmployees) > 0 && $checkAnother) {
                    //         $checkAnother->update([
                    //             'attendance_is_all' => false
                    //         ]);
                    //     }
                    // }

                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil membuat Jadwal.',
                        'data' => $createAttendanceSetting
                    ], 200);
                } catch (Error $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 500);
                }
            } else {
                $checkSetting = SettingAttendanceModel::where('attendance_id', $request->input('attendance_id'))
                ->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();

                if(!$checkSetting) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data Jadwal tidak ditemukan.'
                    ], 404);
                }

                $checkAttendanceDesc = SettingAttendanceModel::where('attendance_description', $request->input('attendance_description'))
                    ->where('attendance_id', '!=', $request->input('attendance_id'))->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                    ->first();

                if($checkAttendanceDesc) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Deskripsi Jadwal Kehadiran sudah digunakan, mohon gunakan yang lain.'
                    ], 400); 
                }

                $attendance_location_address = null;
                $attendance_location_longitude = null;
                $attendance_location_latitude = null;
                $attendance_location_status = false;
                // check if has permission to track location
                // dd(request()->get('permission_codes'));
                if(in_array('TRACK_LOCATION', request()->get('permission_codes'))) {
                    $attendance_location_address = $request->input('attendance_location_address');
                    $attendance_location_longitude = $request->input('attendance_location_longitude');
                    $attendance_location_latitude = $request->input('attendance_location_latitude');
                    $attendance_location_status = $request->input('attendance_location_status');
                }
                // dd($attendance_location_longitude);

                $attendance_break_status_photo = false;
                $attendance_check_in_status_photo = false;
                $attendance_check_out_status_photo = false;
                // check if has permission to upload image
                if(in_array('UPLOAD_IMAGE', request()->get('permission_codes'))) {
                    $attendance_break_status_photo = $request->input('attendance_break_status_photo');
                    $attendance_check_in_status_photo = $request->input('attendance_check_in_status_photo');
                    $attendance_check_out_status_photo = $request->input('attendance_check_out_status_photo');
                }
                
                DB::beginTransaction();
                try {
                    // if($request->input('statusOnly') === true) {
                    //     $checkSetting->update([
                    //         'attendance_status_active' => $request->input('attendance_status_active')
                    //     ]);
                    // } else {
                        // $statusBefore = $checkSetting->attendance_is_all;
                        // $statusCurrent = $request->input('attendance_is_all');

                        $checkAnother = SettingAttendanceModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                            ->where('attendance_is_all', true)
                            ->first();

                        $checkSetting->update([
                            'attendance_working_day' => json_encode($request->input('attendance_working_day')),
                            'attendance_description' => 'Absensi',//$request->input('attendance_description'),
                            'attendance_check_in' => $request->input('attendance_check_in'),
                            'attendance_check_in_tolerance' => $request->input('attendance_check_in_tolerance'),
                            'attendance_check_out' => $request->input('attendance_check_out'),
                            'attendance_break_status' => $request->input('attendance_break_status'),
                            'attendance_start_break' => $request->input('attendance_start_break'),
                            'attendance_end_break' => $request->input('attendance_end_break'),
                            'attendance_check_in_status_photo' => $attendance_check_in_status_photo,
                            'attendance_check_out_status_photo' => $attendance_check_out_status_photo,
                            'attendance_break_type' => $request->input('attendance_break_type'),
                            'attendance_break_status_photo' => $attendance_break_status_photo,
                            // 'attendance_status_active' => $request->input('attendance_status_active'),
                            'attendance_is_used' => $request->input('attendance_is_used'),
                            'attendance_location_status' => $attendance_location_status,
                            'attendance_location_address' => $attendance_location_address,
                            'attendance_location_longitude' => $attendance_location_longitude,
                            'attendance_location_latitude' => $attendance_location_latitude,
                            // 'attendance_is_all' => $request->input('attendance_is_all')
                            'attendance_is_all' => TRUE
                        ]);
                        
                        // dd($checkSetting);
                        KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])                                                    
                        ->where('karyawan_status', '!=', 'NONKARYAWAN')
                        ->update(['st_attendance_id' => $checkSetting->attendance_id]);
                        // $attendanceEmployees = $request->input('attendance_employee');

                        // if($statusBefore === false && $statusCurrent === true) {
                        //     KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])                        
                        //         ->where('karyawan_status', '!=', 'NONKARYAWAN')
                        //         ->update(['st_attendance_id' => $checkSetting->attendance_id]);

                        //     if($checkAnother) {
                        //         $checkAnother->update([
                        //             'attendance_is_all' => false
                        //         ]);
                        //     }
                        // } else if(!$statusCurrent === true){
                        //     KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                        //         ->where('st_attendance_id', $checkSetting->attendance_id)                            
                        //         ->where('karyawan_status', '!=', 'NONKARYAWAN')
                        //         ->update(['st_attendance_id' => null]);
                            
                        //     KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                        //         ->whereIn('karyawan_id', $attendanceEmployees)                               
                        //         ->where('karyawan_status', '!=', 'NONKARYAWAN')  
                        //         ->update(['st_attendance_id' => $checkSetting->attendance_id]);

                        //     if(count($attendanceEmployees) > 0 && $checkAnother) {
                        //         $checkAnother->update([
                        //             'attendance_is_all' => false
                        //         ]);
                        //     }
                        // }

                        // if($request->input('attendance_location_status') === true) {
                        //     $checkSetting->update([
                        //         'attendance_location_address' => $attendance_location_address,
                        //         'attendance_location_longitude' => $attendance_location_longitude,
                        //         'attendance_location_latitude' => $attendance_location_latitude,
                        //     ]);
                        // }
                    // }
                    
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil melakukan update pada Jadwal.',
                        'data' => $checkSetting
                    ], 200);
                } catch (Error $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 500);
                }
                
            }
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $getAttendance = SettingAttendanceModel::find($id);

            if(is_null($getAttendance)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Setting Attendance tidak ditemukan.',
                ], 404);
            }

            if(session()->get('wajibpajak_current')['wajibpajak_id'] !== $getAttendance->ms_wajibpajak_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak memiliki akses ke Setting Attendance yang diminta.',
                ], 403);
            }
                
            // Log::debug(session()->get('user_data'));
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data Setting Attendance.',
                'data' => $getAttendance
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error get list jadwal ' . $e->getMessage());

            return response()->json(['error' => 'Error get jadwal. Please try again later.', 'message' => $e],  500);
        }
    }

    public function destroy($id)
    {
        $getAttendance = SettingAttendanceModel::find($id);

        if(is_null($getAttendance)) {
            return response()->json([
                'success' => false,
                'message' => 'Setting Attendance tidak ditemukan.',
            ], 404);
        }

        if(session()->get('wajibpajak_current')['wajibpajak_id'] !== $getAttendance->ms_wajibpajak_id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses ke Setting Attendance yang diminta.',
            ], 403);
        }

        $checkRelation = AttendanceKaryawanModel::where('st_attendance_id', $id)->first();

        if(!is_null($checkRelation)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus jadwal yang pernah terpakai.',
            ], 403);
        }

        $success = $getAttendance->update(['attendance_status_active' => FALSE]);
        
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus data Setting Attendance.'
        ], 200);
    }

    /**
     * Display a select of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $q = $request->get('q');
        $limit = $request->get('limit') ? $request->get('limit') : 10;

        $data = SettingAttendanceModel::select('attendance_id', 'attendance_description')
        ->when($q, function ($query, $q) {
            return $query->where('attendance_description', 'ilike', '%'.$q.'%');
        })
        ->where([
            'ms_wajibpajak_id' => $wajibpajak_id, 
            // 'attendance_break_status' => true
        ])->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}
