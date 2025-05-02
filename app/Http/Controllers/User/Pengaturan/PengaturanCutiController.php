<?php

namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanDivisiModel;
use App\Model\Master\KaryawanJabatanModel;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingLeaveDetailModel;
use App\Model\Setting\SettingLeaveModel;
use App\Model\Transaction\LeaveKaryawanModel;
use Illuminate\Http\Request;
use Error;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PengaturanCutiController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            'title' => 'Pengaturan Cuti',
            'content' => 'user.pengaturan.cuti.index',
            // 'wajib_pajak_user' => WajibPajakUserModel::where(['ms_user_id' => session()->get('user_data')['user_id']])->count()
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    // public function datatable(Request $request)
    // {
    //     try {
    //         $page = ($request->input('page')) ? intval($request->input('page')) : 1;
    //         $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
    //         $search = (isset($request->input('search')['value']) && $request->input('search')['value']) ? $request->input('search')['value'] : null;
    //         $order_columns = ['leave_id','leave_description','leave_status_active','leave_type','leave_active_start_date','leave_active_end_date','leave_grace_period','leave_quota'];
    //         $order_col = $order_columns[0];
    //         $order_type = 'asc';
    //         if(isset($request->input('order')[0])) {
    //             $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
    //             $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
    //             $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
    //         }

    //         $getLeave = SettingLeaveModel::where(['ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'], 'leave_status_active' => TRUE])
    //             ->when($search, function ($q, $search) {
    //                 $lowercaseSearch = strtolower($search);
    //                 return $q->where(function ($query) use ($lowercaseSearch) {
    //                     $query->where(DB::raw('LOWER(leave_id::text)'), 'like', '%' . $lowercaseSearch . '%')
    //                     ->orWhere(DB::raw('LOWER(leave_description)'), 'like', '%' . $lowercaseSearch . '%');
    //                 });
    //             })
    //             ->orderBy($order_col, $order_type)
    //             ->with('leaveDetailsActive.karyawan') // Include related models
    //             ->get();

    //         $result = [];
    //         foreach ($getLeave as $leave) {
    //             $karyawan = $leave->leaveDetailsActive->map(function ($detail) {
    //                 return $detail->karyawan;
    //             });
    //             // Associate the KaryawanModel instances with the SettingLeaveModel model
    //             $leave->karyawan = $karyawan;

    //             unset($leave->leaveDetailsActive);

    //             $result[] = $leave;
    //         }

    //         $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
    //             array_slice($result, ($page - 1) * $perPage, $perPage),
    //             count($result),
    //             $perPage,
    //             $page,
    //             ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
    //         );

    //         // Log::debug(session()->get('user_data'));
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Berhasil mengambil data Pengaturan Cuti.',
    //             'data' => $paginatedData->items(),
    //             'draw' => $request->input('draw'),
    //             'recordsFiltered' => $paginatedData->total(),
    //             'recordsTotal' => $paginatedData->total(),
    //         ], 200);
    //     } catch (\Exception $e) {
    //         Log::error('Error get list leave ' . $e->getMessage());

    //         return response()->json(['error' => 'Error get leave. Please try again later.', 'message' => $e],  500);
    //     }
    // }

    public function datatable(Request $request)
    {
        try {
            $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
            $offset = ($request->input('start')) ? intval($request->input('start')) : 0;

            $search = (isset($request->input('search')['value']) && $request->input('search')['value']) ? $request->input('search')['value'] : null;
            $order_columns = ['leave_id','leave_description','leave_status_active','leave_type','leave_active_start_date','leave_active_end_date','leave_grace_period','leave_quota'];
            $order_col = $order_columns[0];
            $order_type = 'asc';
            if(isset($request->input('order')[0])) {
                $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
                $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
                $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
            }

            $getLeave = SettingLeaveModel::
                when($search, function ($q, $search) {
                    $lowercaseSearch = strtolower($search);
                    return $q->where(function ($query) use ($lowercaseSearch) {
                        $query->where(DB::raw('LOWER(leave_id::text)'), 'like', '%' . $lowercaseSearch . '%')
                        ->orWhere(DB::raw('LOWER(leave_description)'), 'like', '%' . $lowercaseSearch . '%');
                    });
                })
                ->where(['ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id']])
                ->orderBy($order_col, $order_type)
                // ->with('leaveDetailsActive.karyawan') // Include related models
                ->take($limit)->skip($offset)
                ->get();

            // $result = [];
            // foreach ($getLeave as $leave) {
            //     $karyawan = $leave->leaveDetailsActive->map(function ($detail) {
            //         return $detail->karyawan;
            //     });
            //     // Associate the KaryawanModel instances with the SettingLeaveModel model
            //     $leave->karyawan = $karyawan;

            //     unset($leave->leaveDetailsActive);

            //     $result[] = $leave;
            // }

            $totalData = SettingLeaveModel::
            when($search, function ($q, $search) {
                $lowercaseSearch = strtolower($search);
                return $q->where(function ($query) use ($lowercaseSearch) {
                    $query->where(DB::raw('LOWER(leave_id::text)'), 'like', '%' . $lowercaseSearch . '%')
                    ->orWhere(DB::raw('LOWER(leave_description)'), 'like', '%' . $lowercaseSearch . '%');
                });
            })
            ->where(['ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id']])
            ->count();

            // Log::debug(session()->get('user_data'));
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data Pengaturan Cuti.',
                'data' => $getLeave,
                'draw' => $request->input('draw'),
                'recordsFiltered' => $totalData,
                'recordsTotal' => $totalData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error get list leave ' . $e->getMessage());

            return response()->json(['error' => 'Error get leave. Please try again later.', 'message' => $e],  500);
        }
    }

    public function save(Request $request)
    {
        // check validation if not all employee.
        if(!$request->input('leave_is_all') && $request->input('leave_used_for') == 0) {
            if(!$request->input('leave_employee')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan pilih karyawan.',
                ]);       
            }
        }
        
        try {
            $validator = Validator::make($request->all(), [
                'leave_status_active' => 'required|boolean',
                'leave_repeat_status' => 'required|boolean',
                'isEdit' => 'required|boolean',
                'leave_description' => $request->input('leave_repeat_status') === false ? 'required|string' : '',
                'leave_type' => $request->input('leave_repeat_status') === false ? 'required' : '',
                'leave_active_start_date' => $request->input('leave_repeat_status') === false ? 'required' : '',
                'leave_active_end_date' => $request->input('leave_repeat_status') === false ? 'required' : '',
                'leave_quota' => $request->input('leave_repeat_status') === false ? 'required' : ''
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            // check division and position
            $leave_used_for = $request->input('leave_used_for');
            $divisis = $request->input('leave_divisi');
            $jabatans = $request->input('leave_jabatan');
            $divisis_jabatan = null;
            if($leave_used_for == 4) {
                $divisis_jabatan = implode(',', $divisis);
            } else if($leave_used_for == 5) {
                $divisis_jabatan = implode(',', $jabatans);
            }

            if($request->input('isEdit') === false) {
                $checkLeaveDesc = SettingLeaveModel::where('leave_description', $request->input('leave_description'))->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->where('leave_status_active', true)->first();

                if($checkLeaveDesc) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Deskripsi Cuti sudah digunakan, mohon gunakan yang lain.'
                    ], 400); 
                }
                DB::beginTransaction();
                try {
                    $createLeaveSetting = SettingLeaveModel::create([
                        'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                        'leave_description' => $request->input('leave_description'),
                        'leave_type' => $request->input('leave_type'),
                        'leave_status_active' => $request->input('leave_status_active'),
                        'leave_repeat_status' => $request->input('leave_repeat_status'),
                        'leave_active_start_date' => date('Y-m-d', strtotime($request->input('leave_active_start_date'))),
                        'leave_active_end_date' => date('Y-m-d', strtotime($request->input('leave_active_end_date'))),
                        'leave_grace_period' => $request->input('leave_grace_period') ? date('Y-m-d', strtotime($request->input('leave_grace_period'))) : null,
                        'leave_quota' => $request->input('leave_quota'),
                        'leave_base_month' => $request->input('leave_base_month'),
                        'leave_probation_status' => $request->input('leave_probation_status'),
                        'leave_is_all' => $request->input('leave_is_all'),
                        'leave_used_for' => $request->input('leave_used_for'),
                        'leave_division_jabatan' => $divisis_jabatan,
                    ]);

                    // if($request->input('leave_is_all') === true) {
                    if(in_array($leave_used_for, [1,2,3,4,5])) { // all employee, women, men, division, position
                        $where_karyawan = [
                            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                            'karyawan_active' => 1
                        ];
                        if($leave_used_for == 2) { // perempuan
                            $where_karyawan['karyawan_gender'] = 'P';
                        } else if($leave_used_for == 3) { // laki-laki
                            $where_karyawan['karyawan_gender'] = 'L';
                        }

                        $karyawan = KaryawanModel::
                        when($leave_used_for, function($q) use ($leave_used_for, $divisis, $jabatans) {
                            if($leave_used_for == 4) { // divisi
                                return $q->whereIn('ms_karyawandivisi_id', $divisis);
                            } else if($leave_used_for == 5) { // jabatan
                                return $q->whereIn('ms_karyawanjabatan_id', $jabatans);
                            }
                        })
                        ->where($where_karyawan)                                                    
                        ->where('karyawan_status', '!=', 'NONKARYAWAN')
                        ->get();
                        
                        $leaveEmployees = [];
                        
                        foreach($karyawan as $karyawan_data) {
                            array_push($leaveEmployees, intval($karyawan_data->karyawan_id));
                        }
                    } else {
                        $leaveEmployees = $request->input('leave_employee');
                    }

                    if(is_array($leaveEmployees) && count($leaveEmployees) > 0) {
                        foreach ($leaveEmployees as $employeeId) {
                            $leaveDetails[] = [
                                'st_leave_id' => $createLeaveSetting->leave_id,
                                'ms_karyawan_id' => $employeeId,
                                'leavedetail_active' => true
                            ];
                        }

                        SettingLeaveDetailModel::insert($leaveDetails);
                    }
                    
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil membuat daftar Cuti.',
                        'data' => $createLeaveSetting,
                    ], 200);
                } catch (Error $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 500);
                }
                
            } else {
                $checkLeave = SettingLeaveModel::where('leave_id', $request->input('leave_id'))
                ->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                ->first();

                if(!$checkLeave) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data Cuti tidak ditemukan.'
                    ], 404);
                }

                $checkLeaveDesc = SettingLeaveModel::where('leave_description', $request->input('leave_description'))
                    ->where('leave_id', '!=', $request->input('leave_id'))->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->where('leave_status_active', true)
                    ->first();

                if($checkLeaveDesc) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Deskripsi Cuti sudah digunakan, mohon gunakan yang lain.'
                    ], 400); 
                }

                DB::beginTransaction();
                try {
                    $statusBefore = $checkLeave->leave_is_all;
                    $statusCurrent = $request->input('leave_is_all');
                    if($request->input('statusOnly')){
                        $checkLeave->update([
                            'leave_status_active' => $request->input('leave_status_active'),
                        ]);
                    } else if($request->input('leave_repeat_status') === false) {
                        $checkLeave->update([
                            'leave_description' => $request->input('leave_description'),
                            'leave_repeat_status' => $request->input('leave_repeat_status'),
                            'leave_status_active' => $request->input('leave_status_active'),
                            'leave_type' => $request->input('leave_type'),
                            'leave_active_start_date' => date('Y-m-d', strtotime($request->input('leave_active_start_date'))),
                            'leave_active_end_date' => date('Y-m-d', strtotime($request->input('leave_active_end_date'))),
                            'leave_grace_period' => $request->input('leave_grace_period') ? date('Y-m-d', strtotime($request->input('leave_grace_period'))) : $request->input('leave_grace_period'),
                            'leave_quota' => $request->input('leave_quota'),
                            'leave_probation_status' => $request->input('leave_probation_status'),
                            'leave_base_month' => $request->input('leave_base_month'),
                            'leave_is_all' => $request->input('leave_is_all'),
                            'leave_used_for' => $request->input('leave_used_for'),
                            'leave_division_jabatan' => $divisis_jabatan,
                        ]);
                    } else {
                        $checkLeave->update([
                            'leave_status_active' => $request->input('leave_status_active'),
                            'leave_is_all' => $request->input('leave_is_all')
                        ]);
                    }

                    $leaveEmployees = $request->input('leave_employee');

                    if($statusBefore === false && $statusCurrent === true) {
                        $allKaryawan = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])                        
                            ->where('karyawan_status', '!=', 'NONKARYAWAN')
                            ->pluck('karyawan_id')
                            ->toArray();

                        foreach ($allKaryawan as $employeeId) {
                            $existingLeaveDetail = SettingLeaveDetailModel::where('st_leave_id', $checkLeave->leave_id)
                                ->where('ms_karyawan_id', $employeeId)
                                ->first();
                    
                            if ($existingLeaveDetail) {
                                // Update the existing record
                                $existingLeaveDetail->update(['leavedetail_active' => true]);
                            } else {
                                // Create a new record
                                SettingLeaveDetailModel::create([
                                    'st_leave_id' => $checkLeave->leave_id,
                                    'ms_karyawan_id' => $employeeId,
                                    'leavedetail_active' => true,
                                ]);
                            }
                        }
                    } else if($statusBefore === true && $statusCurrent === false) {
                        $allKaryawan = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])                        
                            ->where('karyawan_status', '!=', 'NONKARYAWAN')
                            ->pluck('karyawan_id')
                            ->toArray();

                        foreach($allKaryawan as $employeeId) {
                            $existingLeaveDetail = SettingLeaveDetailModel::where('st_leave_id', $checkLeave->leave_id)
                                ->where('ms_karyawan_id', $employeeId)
                                ->first();
                            if(!in_array($employeeId, $leaveEmployees)) {
                                if($existingLeaveDetail && $existingLeaveDetail->leavedetail_active === true) {
                                    $existingLeaveDetail->update(['leavedetail_active' => false]);
                                }
                            } else {
                                if(!$existingLeaveDetail) {
                                    SettingLeaveDetailModel::create([
                                        'st_leave_id' => $checkLeave->leave_id,
                                        'ms_karyawan_id' => $employeeId,
                                        'leavedetail_active' => true,
                                    ]);
                                } else if($existingLeaveDetail && $existingLeaveDetail->leavedetail_active === false) {
                                    $existingLeaveDetail->update(['leavedetail_active' => true]);
                                }
                            }
                        }
                    } else if($statusBefore === false && $statusCurrent === false) {
                        if (is_array($leaveEmployees) && count($leaveEmployees) > 0) {
                            foreach ($leaveEmployees as $index => $employeeId) {
                                $existingLeaveDetail = SettingLeaveDetailModel::where('st_leave_id', $checkLeave->leave_id)
                                    ->where('ms_karyawan_id', $employeeId)
                                    ->first();

                                if(!$existingLeaveDetail) {
                                    SettingLeaveDetailModel::create([
                                        'st_leave_id' => $checkLeave->leave_id,
                                        'ms_karyawan_id' => $employeeId,
                                        'leavedetail_active' => true,
                                    ]);
                                } else if($existingLeaveDetail && $existingLeaveDetail->leavedetail_active === false) {
                                    $existingLeaveDetail->update(['leavedetail_active' => true]);
                                }
                            }
                        }
    
                        $leaveDeletedEmployee = $request->input('leave_deleted_employee');
    
                        if (is_array($leaveDeletedEmployee) && count($leaveDeletedEmployee) > 0) {
                            foreach ($leaveDeletedEmployee as $index => $employeeId) {
                                // Assuming $employeeId is the ID of an employee in the KaryawanModel
                                $existingLeaveDetail = SettingLeaveDetailModel::where('st_leave_id', $checkLeave->leave_id)
                                    ->where('ms_karyawan_id', $employeeId)
                                    ->first();

                                if($existingLeaveDetail && $existingLeaveDetail->leavedetail_active === true) {
                                    $existingLeaveDetail->update(['leavedetail_active' => false]);
                                }
                            }
                        }
                    }

                    if(in_array($leave_used_for, [2,3,4,5])) { // women, men, division, position
                        $leavedet = $checkLeave->leaveDetails;
                        $existingKaryawanDetId = [];
                        foreach($leavedet as $ldet) {
                            array_push($existingKaryawanDetId, $ldet->ms_karyawan_id);
                        }
                        // dd($existingKaryawanDetId);
                        $where_karyawan = [
                            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                            'karyawan_active' => 1
                        ];
                        if($leave_used_for == 2) { // perempuan
                            $where_karyawan['karyawan_gender'] = 'P';
                        } else if($leave_used_for == 3) { // laki-laki
                            $where_karyawan['karyawan_gender'] = 'L';
                        }

                        $karyawan = KaryawanModel::when($leave_used_for, function($q) use ($leave_used_for, $divisis, $jabatans) {
                            if($leave_used_for == 4) { // divisi
                                return $q->whereIn('ms_karyawandivisi_id', $divisis);
                            } else if($leave_used_for == 5) { // jabatan
                                return $q->whereIn('ms_karyawanjabatan_id', $jabatans);
                            }
                        })
                        ->where($where_karyawan)                                                    
                        ->where('karyawan_status', '!=', 'NONKARYAWAN')
                        ->get();

                        SettingLeaveDetailModel::where(['st_leave_id' => $checkLeave->leave_id])
                        ->update(['leavedetail_active' => false]);
                        
                        $leaveEmployees = [];
                        
                        $leaveDetails = [];
                        
                        foreach($karyawan as $karyawan_data) {
                            if(!in_array($karyawan_data->karyawan_id, $existingKaryawanDetId)) {
                                $leaveDetails[] = [
                                    'st_leave_id' => $checkLeave->leave_id,
                                    'ms_karyawan_id' => $karyawan_data->karyawan_id,
                                    'leavedetail_active' => true
                                ];
                            } else { // update
                                SettingLeaveDetailModel::where(['st_leave_id' => $checkLeave->leave_id, 'ms_karyawan_id' => $karyawan_data->karyawan_id])
                                ->update(['leavedetail_active' => true]);
                            }
                        }
                        if($leaveDetails)
                            SettingLeaveDetailModel::insert($leaveDetails);

                    }

                    if($request->input('leave_status_active') == false) {
                        // soft delete the detail
                        SettingLeaveDetailModel::where(['st_leave_id' => $checkLeave->leave_id])
                        ->update(['leavedetail_active' => false]);
                    }
                   
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil melakukan update pada data Cuti.',
                        'data' => $checkLeave
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
            $getLeave = SettingLeaveModel::find($id);

            if(is_null($getLeave)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengaturan cuti tidak ditemukan.',
                ], 404);
            }

            if(session()->get('wajibpajak_current')['wajibpajak_id'] !== $getLeave->ms_wajibpajak_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak memiliki akses ke Pengaturan cuti yang diminta.',
                ], 403);
            }
                
            // Log::debug(session()->get('user_data'));
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data Pengaturan cuti.',
                'data' => $getLeave
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error get list leave ' . $e->getMessage());

            return response()->json(['error' => 'Error get leave. Please try again later.', 'message' => $e],  500);
        }
    }

    public function destroy($id)
    {
        $getLeave = SettingLeaveModel::find($id);

        if(is_null($getLeave)) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan cuti tidak ditemukan.',
            ], 404);
        }

        if(session()->get('wajibpajak_current')['wajibpajak_id'] !== $getLeave->ms_wajibpajak_id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses ke Pengaturan cuti yang diminta.',
            ], 403);
        }

        $checkRelation = LeaveKaryawanModel::where('st_leave_id', $id)->first();

        if(!is_null($checkRelation)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus cuti yang pernah terpakai.',
                'data' => $checkRelation,
                'check' => !is_null($checkRelation)
            ], 403);
        }

        DB::beginTransaction();
            try {
            $success = $getLeave->update(['leave_status_active' => FALSE]);
            SettingLeaveDetailModel::where(['st_leave_id' => $getLeave->leave_id])
            ->update(['leavedetail_active' => false]);
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menghapus data Pengaturan cuti.'
            ], 200);

        } catch (Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listkaryawan of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listkaryawan($id, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $data = SettingLeaveDetailModel::select('karyawan_id', 'karyawan_name')
        ->join('ms_karyawan', 'karyawan_id', 'ms_karyawan_id')
        ->where([
            'st_leave_detail.st_leave_id' => $id,
            'karyawan_active' => 1,
            'leavedetail_active' => true,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    /**
     * Display a listdivisijabatan of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listdivisijabatan($stleavekaryawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $stleavekaryawan = SettingLeaveModel::select('leave_id', 'leave_used_for', 'leave_division_jabatan')
        ->where([
            'leave_id' => $stleavekaryawanId,
            'leave_status_active' => true,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        
        $data = [];
        if($stleavekaryawan) {
            if($stleavekaryawan->leave_used_for == 4) {
                $data = KaryawanDivisiModel::select('karyawandivisi_id', 'karyawandivisi_name')
                ->where([
                    'karyawandivisi_active' => 1,
                ])->whereIn('karyawandivisi_id', explode(',', $stleavekaryawan->leave_division_jabatan))->get();
            } else if($stleavekaryawan->leave_used_for == 5) {
                $data = KaryawanJabatanModel::select('karyawanjabatan_id', 'karyawanjabatan_name')
                ->where([
                    'karyawanjabatan_active' => 1,
                ])->whereIn('karyawanjabatan_id', explode(',', $stleavekaryawan->leave_division_jabatan))->get();
            }
        }
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}
