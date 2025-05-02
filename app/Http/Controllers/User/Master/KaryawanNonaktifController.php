<?php
namespace App\Http\Controllers\User\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanInfoFieldModel;
use App\Model\Master\KaryawanKalkulasiModel;
use App\Model\Master\KaryawanMasakerjaModel;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingAttendanceModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingLeaveDetailModel;
use App\Model\Setting\SettingLeaveModel;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use App\Model\Setting\SettingPotonganKaryawanDetailModel;
use App\Model\Setting\SettingPotonganKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanDetailModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class KaryawanNonaktifController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Non Aktif Karyawan',
            'content' => 'user.master.karyawan.nonaktif.index',
        ];

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }
    
    public function profile($karyawanId, Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $karyawan = KaryawanModel::
        select("*"
        , DB::raw("(SELECT json_agg(ttable) 
        FROM (
            SELECT std.st_tunjangankaryawan_id, stk.sttunjangankaryawan_name
            FROM st_tunjangan_karyawan_detail as std
            JOIN st_tunjangan_karyawan as stk ON std.st_tunjangankaryawan_id = stk.sttunjangankaryawan_id
            WHERE std.ms_karyawan_id = ms_karyawan.karyawan_id
                AND std.sttunjangankaryawandet_active = '1'
                AND stk.sttunjangankaryawan_active = '1'
        ) as ttable) as tunjangan_json")
        , DB::raw("(SELECT json_agg(ttable) 
        FROM (
            SELECT spd.st_potongankaryawan_id, spk.stpotongankaryawan_name
            FROM st_potongan_karyawan_detail as spd
            JOIN st_potongan_karyawan as spk ON spd.st_potongankaryawan_id = spk.stpotongankaryawan_id
            WHERE spd.ms_karyawan_id = ms_karyawan.karyawan_id
                AND spd.stpotongankaryawandet_active = '1'
                AND spk.stpotongankaryawan_active = '1'
        ) as ttable) as potongan_json"))
        ->with(['bank', 'penggajian', 'ptkp', 'manager', 'divisi', 'jabatan', 'attendance', 'country'])
        ->where([
            'karyawan_id' => $karyawanId, 
            'karyawan_active' => 0,
            // 'karyawan_contract_end' => null,
            'ms_wajibpajak_id' => $wajibpajak_id,
            // 'ms_user_id' => $user_id,
        ])->first();
        if(!$karyawan) {
            abort(404);
        }

        // $karyawan_infofield = KaryawanInfoFieldModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_user_id' => $user_id])->first();
        $stbpjskaryawan_data = ($karyawan->setting_bpjspegawai_json) ? json_decode($karyawan->setting_bpjspegawai_json) : null;
        $data = [
            'title' => 'Profile Karyawan',
            'content' => 'user.master.karyawan.profile',
            'karyawan' => $karyawan,
            // 'karyawan_infofield' => $karyawan_infofield,
            // 'sttunjangankaryawan_data' => ($setting[0]->setting_tunjangankaryawan_json) ? json_decode($setting[0]->setting_tunjangankaryawan_json) : null,
            'stbpjskaryawan_data' => ($stbpjskaryawan_data) ? json_decode($stbpjskaryawan_data->stbpjskaryawan_value) : null,
        ];
        // return response()->json($data);
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
        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
        $search = (isset($request->input('search')['value']) && $request->input('search')['value']) ? $request->input('search')['value'] : null;
        
        $where = [
            'karyawan_active' => 0,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];
        $list = KaryawanModel::select('karyawan_id', 'karyawan_name', 'karyawan_nik', 'karyawan_npwp', 'karyawan_address', 'karyawan_phone', 'karyawan_status')
        ->where($where)
        ->whereNotNull('karyawan_contract_end')
        ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
        ->where(function ($q) use ($search){
            return $q->where(DB::raw('LOWER(karyawan_nik)'), 'like', '%'.strtolower($search).'%')
            ->orWhere(DB::raw('LOWER(karyawan_npwp)'), 'like', '%'.strtolower($search).'%')
            ->orWhere(DB::raw('LOWER(karyawan_name)'), 'like', '%'.strtolower($search).'%');
        })
        ->take($limit)->skip($offset)->orderBy('karyawan_id', 'desc')->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = KaryawanModel::where($where)
        ->whereNotNull('karyawan_contract_end')
        ->where(function ($q) use ($search){
            return $q->where(DB::raw('LOWER(karyawan_nik)'), 'like', '%'.strtolower($search).'%')
            ->orWhere(DB::raw('LOWER(karyawan_npwp)'), 'like', '%'.strtolower($search).'%')
            ->orWhere(DB::raw('LOWER(karyawan_name)'), 'like', '%'.strtolower($search).'%');
        })->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function activate($karyawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawan_status = $request->input('aktif_status');
        $tipe_kontrak = $request->input('tipe_kontrak');
        
        $karyawan = KaryawanModel::where([
            'karyawan_id' => $karyawanId, 
            'karyawan_active' => 0,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])
        ->whereNotNull('karyawan_contract_end')
        ->first();
        // dd($karyawan);
        if(!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan!'
            ]); 
        }
        
        $validation = [
            'aktif_tgl' => 'required',
            'aktif_status' => 'required|in:TETAP,KONTRAK,PERCOBAAN,NONKARYAWAN',
            'tipe_kontrak' => 'required|in:BARU,PERPANJANG',
        ];
        
        if($karyawan_status != 'TETAP' && $karyawan_status != 'NONKARYAWAN') {
            $validation['akhir_tgl'] = 'required';
        }

        $validator = Validator::make($request->all(), $validation, [
            'aktif_tgl.required' => 'Tanggal wajib diisi!',
            'akhir_tgl.required' => 'Tanggal Berakhir Kontrak wajib diisi!',
            'aktif_status.required' => 'Status wajib diisi!',
            'tipe_kontrak.required' => 'Tipe kontrak wajib diisi!',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'success' => false,
                'message' => $error
            ]);
        }

        $karyawan_calculation_method = $karyawan->karyawan_calculation_method;
        if($karyawan_status === 'NONKARYAWAN') {
            $karyawan_calculation_method = 'GROSS';
        }
        if($tipe_kontrak == 'PERPANJANG') {
            $dt_bend_date = $karyawan->karyawan_contract_begin;
        } else {
            $dt_bend = \Carbon\Carbon::parse($request->input('aktif_tgl'));
            $dt_bend_date = $dt_bend->translatedFormat('Y-m-d');
        }

        $dt_end_date = null;
        if($karyawan_status != 'TETAP') {
            $dt_end = \Carbon\Carbon::parse($request->input('akhir_tgl'));
            $dt_end_date = $dt_end->translatedFormat('Y-m-d');
        }

        // check setting penggajian, absen, cuti, tunjangan, potongan 
        // $temp_data_leave = [];
        $temp_existing_data_leave = [];
        $temp_data_allowance = [];
        $temp_existing_data_allowance = [];
        $temp_data_deduction = [];
        $temp_existing_data_deduction = [];
        if($karyawan_status != 'NONKARYAWAN') {
            $stpenggajiankaryawan = SettingPenggajianKaryawanModel::where([
                'stpenggajiankaryawan_active' => 1,
                'stpenggajiankaryawan_is_all' => true,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])->first();
            $karyawan->st_penggajian_id = ($stpenggajiankaryawan) ? $stpenggajiankaryawan->stpenggajiankaryawan_id : null;

            $stattendancekaryawan = SettingAttendanceModel::where([
                'attendance_status_active' => true,
                'attendance_is_all' => true,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])->first();
            $karyawan->st_attendance_id = ($stattendancekaryawan) ? $stattendancekaryawan->attendance_id : null;
            
            // // check if there leave
            // $stleavekaryawan = SettingLeaveModel::with(['leaveDetails'])->where([
            //     'leave_status_active' => true,
            //     'leave_is_all' => true,
            //     'ms_wajibpajak_id' => $wajibpajak_id,
            // ])->get();
            // if(count($stleavekaryawan) > 0) {
            //     foreach($stleavekaryawan as $leave) {
            //         $stdetkr_ids = [];
            //         foreach($leave->leaveDetails as $leavedet) {
            //             array_push($stdetkr_ids, $leavedet->ms_karyawan_id);
            //         }

            //         if(!in_array($karyawan->karyawan_id, $stdetkr_ids)) {
            //             array_push($temp_data_leave, [
            //                 'st_leave_id' => $leave->leave_id,
            //                 'ms_karyawan_id' => $karyawan->karyawan_id,
            //                 'leavedetail_active' => true,
            //             ]);
            //         } else {
            //             array_push($temp_existing_data_leave, [
            //                 'st_leave_id' => $leave->leave_id,
            //                 'ms_karyawan_id' => $karyawan->karyawan_id,
            //                 'leavedetail_active' => true,
            //             ]);
            //         }
            //     }
            // }

            // // check if there is allowance
            // $stallowances = SettingTunjanganKaryawanModel::with(['sttunjangandetail'])->where([
            //     'sttunjangankaryawan_active' => 1,
            //     'sttunjangankaryawan_is_all' => true,
            //     'ms_wajibpajak_id' => $wajibpajak_id,
            // ])->get();
            // if(count($stallowances) > 0) {
            //     foreach($stallowances as $allow) {
            //         $stdetkr_ids = [];
            //         foreach($allow->sttunjangandetail as $allowdet) {
            //             array_push($stdetkr_ids, $allowdet->ms_karyawan_id);
            //         }
            //         if(!in_array($karyawan->karyawan_id, $stdetkr_ids)) {
            //             array_push($temp_data_allowance, [
            //                 'st_tunjangankaryawan_id' => $allow->sttunjangankaryawan_id,
            //                 'ms_karyawan_id' => $karyawan->karyawan_id,
            //                 'sttunjangankaryawandet_active' => 1
            //             ]);
            //         } else {
            //             array_push($temp_existing_data_allowance, [
            //                 'st_tunjangankaryawan_id' => $allow->sttunjangankaryawan_id,
            //                 'ms_karyawan_id' => $karyawan->karyawan_id
            //             ]);
            //         }
            //     }
            // }

            // // check if there is deduction
            // $deductions = SettingPotonganKaryawanModel::with(['stpotongandetail'])->where([
            //     'stpotongankaryawan_active' => 1,
            //     'stpotongankaryawan_is_all' => true,
            //     'ms_wajibpajak_id' => $wajibpajak_id,
            // ])->get();

            // if(count($deductions) > 0) {
            //     foreach($deductions as $deduc) {
            //         $stdetkr_ids = [];
            //         foreach($deduc->stpotongandetail as $deducdet) {
            //             array_push($stdetkr_ids, $deducdet->ms_karyawan_id);
            //         }
            //         if(!in_array($karyawan->karyawan_id, $stdetkr_ids)) {
            //             array_push($temp_data_deduction, [
            //                 'st_potongankaryawan_id' => $deduc->stpotongankaryawan_id,
            //                 'ms_karyawan_id' => $karyawan->karyawan_id,
            //                 'stpotongankaryawandet_active' => 1
            //             ]);
            //         } else {
            //             array_push($temp_existing_data_deduction, [
            //                 'st_potongankaryawan_id' => $allow->stpotongankaryawan_id,
            //                 'ms_karyawan_id' => $karyawan->karyawan_id
            //             ]);
            //         }
            //     }
            // }
        }
        // remove column
        unset($karyawan->karyawan_password);
        unset($karyawan->karyawan_forgot_password);

        DB::beginTransaction();
        try {
            KaryawanModel::
            where(['karyawan_id' => $karyawan->karyawan_id])
            ->update([
                'karyawan_active' => 1,
                'karyawan_end_type' => null,
                'karyawan_end_reason' => null,
                'st_penggajian_id' => $karyawan->st_penggajian_id,
                'st_attendance_id' => $karyawan->st_attendance_id,
                'karyawan_contract_begin' => $dt_bend_date,
                'karyawan_contract_end' => $dt_end_date,
            ]);

            KaryawanMasakerjaModel::create([
                'ms_karyawan_id' => $karyawan->karyawan_id,
                'karyawanmasakerja_salary' => $karyawan->karyawan_salary,
                'karyawanmasakerja_contract_begin' => $dt_bend_date,
                'karyawanmasakerja_contract_end' => $dt_end_date,
                'karyawanmasakerja_status' => $karyawan_status,
                'karyawanmasakerja_contracttype' => $tipe_kontrak,
                'karyawanmasakerja_calculation_method' => $karyawan_calculation_method,
                'karyawanmasakerja_salary' => $karyawan->karyawan_salary,
                'karyawanmasakerja_position' => $karyawan->karyawan_position,
                'karyawanmasakerja_nik' => $karyawan->karyawan_nik,
                'karyawanmasakerja_npwp' => $karyawan->karyawan_npwp,
                'karyawanmasakerja_data' => json_encode($karyawan),
                'ms_objekpajak_code' => $karyawan->ms_objekpajak_code,
                'ms_ptkp_id' => $karyawan->ms_ptkp_id,
                'ms_user_id' => $karyawan->ms_user_id,
                'ms_wajibpajak_id' => $karyawan->ms_wajibpajak_id,
            ]);

            // add leave                
            // if(count($temp_data_leave) > 0) {
            //     SettingLeaveDetailModel::insert($temp_data_leave);
            // }
            // if(count($temp_existing_data_leave) > 0) {
            //     foreach($temp_existing_data_leave as $existleave) {
            //         SettingLeaveDetailModel::where(['st_leave_id' => $existleave['st_leave_id'], 'ms_karyawan_id' => $existleave['ms_karyawan_id']])
            //         ->update([
            //             'leavedetail_active' => true
            //         ]);
            //     }
            // }

            // // add allowances                
            // if(count($temp_data_allowance) > 0) {
            //     SettingTunjanganKaryawanDetailModel::insert($temp_data_allowance);
            // }
            // // dd($temp_existing_data_allowance, $temp_existing_data_deduction);
            // if(count($temp_existing_data_allowance) > 0) {
            //     foreach($temp_existing_data_allowance as $existallow) {
            //         SettingTunjanganKaryawanDetailModel::where(['st_tunjangankaryawan_id' => $existallow['st_tunjangankaryawan_id'], 'ms_karyawan_id' => $existallow['ms_karyawan_id']])
            //         ->update([
            //             'sttunjangankaryawandet_active' => 1
            //         ]);
            //     }
            // }

            // // add deductions                
            // if(count($temp_data_deduction) > 0) {
            //     SettingPotonganKaryawanDetailModel::insert($temp_data_deduction);
            // }
            // if(count($temp_existing_data_deduction) > 0) {
            //     foreach($temp_existing_data_deduction as $existdeduc) {
            //         SettingPotonganKaryawanDetailModel::where(['st_potongankaryawan_id' => $existdeduc['st_potongankaryawan_id'], 'ms_karyawan_id' => $existdeduc['ms_karyawan_id']])
            //         ->update([
            //             'stpotongankaryawandet_active' => 1
            //         ]);
            //     }
            // }
            
            $karyawanctrl = new KaryawanController();

            // processed the leave setting not all employees
            $sttunjangan = SettingTunjanganKaryawanModel::where([
                'sttunjangankaryawan_active' => true,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])
            ->whereIn('sttunjangankaryawan_used_for', [1,2,3,4,5])->get();
            if($sttunjangan) {
                $tempdata_tunjangan = $karyawanctrl->sttunjangan_tempdata($sttunjangan, [
                    [
                        'karyawan_id' => $karyawan->karyawan_id,
                        'karyawan_gender' => $karyawan->karyawan_gender,
                        'ms_karyawandivisi_id' => $karyawan->ms_karyawandivisi_id,
                        'ms_karyawanjabatan_id' => $karyawan->ms_karyawanjabatan_id,
                    ]
                ]);
                // check detail leave
                $sttunjangandetail = SettingTunjanganKaryawanDetailModel::where([
                    'ms_karyawan_id' => $karyawan->karyawan_id,
                ])->get();
                
                $sttunjanganids = [];
                // dd($tempdata_leave);
                foreach($tempdata_tunjangan as $tdtunjangan) {
                    array_push($sttunjanganids, $tdtunjangan['st_tunjangankaryawan_id']);
                    SettingTunjanganKaryawanDetailModel::updateOrInsert(
                        [
                            'st_tunjangankaryawan_id' => $tdtunjangan['st_tunjangankaryawan_id'],
                            'ms_karyawan_id' => $tdtunjangan['ms_karyawan_id'],
                        ],
                        $tdtunjangan
                    );
                }
                $deletedsttunjanganids = []; 
                foreach($sttunjangandetail as $tjdet) {
                    if(!in_array($tjdet->st_tunjangankaryawan_id, $deletedsttunjanganids)) {
                        array_push($deletedsttunjanganids, $tjdet->st_tunjangankaryawan_id);
                    }
                }
                if($deletedsttunjanganids) {
                    SettingTunjanganKaryawanDetailModel::whereIn('st_tunjangankaryawan_id', $deletedsttunjanganids)
                    ->where(['ms_karyawan_id' => $karyawan->karyawan_id])
                    ->update(['sttunjangankaryawandet_active' => false]);
                }
            }

            // processed the leave setting not all employees
            $stpotongan = SettingPotonganKaryawanModel::where([
                'stpotongankaryawan_active' => true,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])
            ->whereIn('stpotongankaryawan_used_for', [1,2,3,4,5])->get();
            if($stpotongan) {
                $tempdata_potongan = $karyawanctrl->stpotongan_tempdata($stpotongan, [
                    [
                        'karyawan_id' => $karyawan->karyawan_id,
                        'karyawan_gender' => $karyawan->karyawan_gender,
                        'ms_karyawandivisi_id' => $karyawan->ms_karyawandivisi_id,
                        'ms_karyawanjabatan_id' => $karyawan->ms_karyawanjabatan_id,
                    ]
                ]);
                // check detail leave
                $stpotongandetail = SettingPotonganKaryawanDetailModel::where([
                    'ms_karyawan_id' => $karyawan->karyawan_id,
                ])->get();
                
                $stpotonganids = [];
                // dd($tempdata_leave);
                foreach($tempdata_potongan as $tdpotongan) {
                    array_push($stpotonganids, $tdpotongan['st_potongankaryawan_id']);
                    SettingPotonganKaryawanDetailModel::updateOrInsert(
                        [
                            'st_potongankaryawan_id' => $tdpotongan['st_potongankaryawan_id'],
                            'ms_karyawan_id' => $tdpotongan['ms_karyawan_id'],
                        ],
                        $tdpotongan
                    );
                }
                $deletedstpotonganids = []; 
                foreach($stpotongandetail as $ptdet) {
                    if(!in_array($ptdet->st_potongankaryawan_id, $deletedstpotonganids)) {
                        array_push($deletedstpotonganids, $ptdet->st_potongankaryawan_id);
                    }
                }
                if($deletedstpotonganids) {
                    SettingPotonganKaryawanDetailModel::whereIn('st_potongankaryawan_id', $deletedstpotonganids)
                    ->where(['ms_karyawan_id' => $karyawan->karyawan_id])
                    ->update(['stpotongankaryawandet_active' => false]);
                }
            }
            
            // processed the leave setting not all employees
            $stleave = SettingLeaveModel::where([
                'leave_status_active' => true,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])
            ->whereIn('leave_used_for', [1,2,3,4,5])->get();
            if($stleave) {
                $tempdata_leave = $karyawanctrl->stleave_tempdata($stleave, [
                    [
                        'karyawan_id' => $karyawan->karyawan_id,
                        'karyawan_gender' => $karyawan->karyawan_gender,
                        'ms_karyawandivisi_id' => $karyawan->ms_karyawandivisi_id,
                        'ms_karyawanjabatan_id' => $karyawan->ms_karyawanjabatan_id,
                    ]
                ]);
                // check detail leave
                $stleavedetail = SettingLeaveDetailModel::where([
                    'ms_karyawan_id' => $karyawan->karyawan_id,
                ])->get();
                
                $stleaveids = [];
                // dd($tempdata_leave);
                foreach($tempdata_leave as $tdleave) {
                    array_push($stleaveids, $tdleave['st_leave_id']);
                    SettingLeaveDetailModel::updateOrInsert(
                        [
                            'st_leave_id' => $tdleave['st_leave_id'],
                            'ms_karyawan_id' => $tdleave['ms_karyawan_id'],
                        ],
                        $tdleave
                    );
                }
                $deletedstleaveids = []; 
                foreach($stleavedetail as $lvdet) {
                    if(!in_array($lvdet->st_leave_id, $stleaveids)) {
                        array_push($deletedstleaveids, $lvdet->st_leave_id);
                    }
                }
                if($deletedstleaveids) {
                    SettingLeaveDetailModel::whereIn('st_leave_id', $deletedstleaveids)
                    ->where(['ms_karyawan_id' => $karyawan->karyawan_id])
                    ->update(['leavedetail_active' => false]);
                }
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil diaktifkan',
                'data' => $karyawan
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}