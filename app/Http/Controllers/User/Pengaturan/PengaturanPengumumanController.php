<?php

namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingAnnouncementModel;
use App\Model\Transaction\AnnouncementKaryawanModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Error;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PengaturanPengumumanController extends Controller
{
    public function index(Request $request)
    {
        $data = [
            'title' => 'Pengaturan Pengumuman',
            'content' => 'user.pengaturan.pengumuman.index',
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
            // $page = ($request->input('page')) ? intval($request->input('page')) : 1;
            // $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
            $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
            $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
            $search = (isset($request->input('search')['value']) && $request->input('search')['value']) ? $request->input('search')['value'] : null;
            $order_columns = ['announcement_title','announcement_description','announcement_start_date','announcement_end_date'];
            $order_col = $order_columns[0];
            $order_type = 'asc';
            if(isset($request->input('order')[0])) {
                $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
                $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
                $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
            }

            $getAnnouncement = SettingAnnouncementModel::
                when($search, function ($q, $search) {
                    $lowercaseSearch = strtolower($search);
                    return $q->where(function ($query) use ($lowercaseSearch) {
                        $query->where(DB::raw('LOWER(announcement_title)'), 'like', '%' . $lowercaseSearch . '%');
                    });
                })
                ->where(['ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'], 'announcement_active' => TRUE])
                ->orderBy($order_col, $order_type)
                ->take($limit)->skip($offset)->get();
            foreach($getAnnouncement as $announcement) {
                $karyawan = array();
                $dataEmployee = json_decode($announcement->announcement_karyawan);

                foreach($dataEmployee as $employee) {
                    $karyawanData = KaryawanModel::where('karyawan_id', intval($employee))
                    ->select('karyawan_id', 'karyawan_name')
                    ->first();

                    $karyawan[] = $karyawanData;
                }

                $announcement->karyawan_data = $karyawan;
            }

            $totalData = SettingAnnouncementModel::
                when($search, function ($q, $search) {
                    $lowercaseSearch = strtolower($search);
                    return $q->where(function ($query) use ($lowercaseSearch) {
                        $query->where(DB::raw('LOWER(announcement_title)'), 'like', '%' . $lowercaseSearch . '%');
                    });
                })
                ->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                ->count();

            // Log::debug(session()->get('user_data'));
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data Pengaturan Pengumuman.',
                'data' => $getAnnouncement,
                'recordsFiltered' => $totalData,
                'recordsTotal' => $totalData,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error get list announcement ' . $e->getMessage());

            return response()->json(['error' => 'Error get announcement. Please try again later.', 'message' => $e],  500);
        }
    }

    public function save(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'announcement_title' => 'required|string',
                'announcement_description' => 'required|string',
                'announcement_start_date' => 'required',
                'announcement_end_date' => 'required'
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            
            $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

            if($request->input('isEdit') === false) {
                $checkAnnouncementDesc = SettingAnnouncementModel::where('announcement_title', $request->input('announcement_title'))->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])->first();

                if($checkAnnouncementDesc) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Judul Pengumuman sudah digunakan, mohon gunakan yang lain.'
                    ], 400); 
                }

                DB::beginTransaction();
                try {
                    $createAnnouncementSetting = SettingAnnouncementModel::create([
                        'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                        'announcement_title' => $request->input('announcement_title'),
                        'announcement_description' => $request->input('announcement_description'),
                        'announcement_start_date' => date('Y-m-d', strtotime($request->input('announcement_start_date'))),
                        'announcement_end_date' => date('Y-m-d', strtotime($request->input('announcement_end_date'))),
                        'announcement_is_all' => $request->input('announcement_is_all')
                    ]);

                    $tempArrKaryawan = array();
                    $tempArrIsRead = array();
                    $tempArrIsClear = array();

                    if($request->input('announcement_is_all') === true) {
                        $karyawanRecords = KaryawanModel::where('karyawan_active', 1)
                            ->where('karyawan_status', '!=', 'NONKARYAWAN')
                            ->where('ms_wajibpajak_id', $wajibpajak_id)->get();

                        foreach ($karyawanRecords as $karyawan) {
                            $tempArrKaryawan[] = $karyawan->karyawan_id;
                            $tempArrIsRead[] = strval($karyawan->karyawan_id) . ',0';
                            $tempArrIsClear[] = strval($karyawan->karyawan_id) . ',0';
                        }
                    } else {
                        $arrKaryawan = $request->input('announcement_employee');

                        foreach($arrKaryawan as $karyawan_id) {
                            $tempArrKaryawan[] = intval($karyawan_id);
                            $tempArrIsRead[] = strval($karyawan_id) . ',0';
                            $tempArrIsClear[] = strval($karyawan_id) . ',0';
                        }
                    }

                    $createAnnouncementSetting->update([
                        'announcement_karyawan' => $tempArrKaryawan,
                        'announcement_karyawan_is_read' => $tempArrIsRead,
                        'announcement_karyawan_is_clear' => $tempArrIsClear
                    ]);
                    
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil membuat daftar Pengumuman.',
                        'data' => $createAnnouncementSetting
                    ], 200);
                } catch (Error $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => $e->getMessage()
                    ], 500);
                }
                
            } else {
                $checkAnnouncement = SettingAnnouncementModel::where('announcement_id', $request->input('announcement_id'))->first();

                if(!$checkAnnouncement) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data Pengumuman tidak ditemukan.'
                    ], 404);
                }

                $checkAnnouncementDesc = SettingAnnouncementModel::where('announcement_title', $request->input('announcement_title'))
                    ->where('announcement_id', '!=', $request->input('announcement_id'))->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                    ->first();

                // dd($request->input('announcement_id'), $checkAnnouncementDesc);
                if($checkAnnouncementDesc) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Judul Pengumuman sudah digunakan, mohon gunakan yang lain.'
                    ], 400); 
                }

                DB::beginTransaction();
                try {
                    $statusBefore = $checkAnnouncement->announcement_is_all;
                    $statusCurrent = $request->input('announcement_is_all');

                    $checkAnnouncement->update([
                        'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                        'announcement_title' => $request->input('announcement_title'),
                        'announcement_description' => $request->input('announcement_description'),
                        'announcement_start_date' => date('Y-m-d', strtotime($request->input('announcement_start_date'))),
                        'announcement_end_date' => date('Y-m-d', strtotime($request->input('announcement_end_date'))),
                        'announcement_is_all' => $statusCurrent
                    ]);

                    if($statusBefore === false && $statusCurrent === true) {
                        $karyawanRecords = KaryawanModel::where('karyawan_active', 1)
                            ->where('ms_wajibpajak_id', $wajibpajak_id)
                            ->where('karyawan_status', '!=', 'NONKARYAWAN')
                            ->get();

                        $tempArrKaryawan = json_decode($checkAnnouncement->announcement_karyawan, true);
                        $tempArrIsRead = json_decode($checkAnnouncement->announcement_karyawan_is_read, true);
                        $tempArrIsClear = json_decode($checkAnnouncement->announcement_karyawan_is_clear, true);

                        // Create announcements for each matching KaryawanModel record
                        foreach ($karyawanRecords as $karyawan) {
                            if(!in_array($karyawan->karyawan_id, $tempArrKaryawan)) {
                                $tempArrKaryawan[] = $karyawan->karyawan_id;
                                $tempArrIsRead[] = strval($karyawan->karyawan_id) . ',0';
                                $tempArrIsClear[] = strval($karyawan->karyawan_id) . ',0';
                            }

                            $checkAnnouncement->update([
                                'announcement_karyawan' => $tempArrKaryawan,
                                'announcement_karyawan_is_read' => $tempArrIsRead,
                                'announcement_karyawan_is_clear' => $tempArrIsClear
                            ]);
                        }
                    } else if($statusBefore === true && $statusCurrent === false) {
                        $announcementEmployees = $request->input('announcement_employee');

                        $tempArrKaryawan = array();
                        $tempArrIsRead = array();
                        $tempArrIsClear = array();

                        $decodeKaryawan = json_decode($checkAnnouncement->announcement_karyawan, true);
                        $decodeIsRead = json_decode($checkAnnouncement->announcement_karyawan_is_read, true);
                        $decodeIsClear = json_decode($checkAnnouncement->announcement_karyawan_is_clear, true);
                        foreach ($decodeKaryawan as $karyawan) {
                            $key = array_search($karyawan, $announcementEmployees);
                            if ($key !== false) {
                                unset($announcementEmployees[$key]); // Remove $karyawan from $announcementEmployees
                                $tempArrKaryawan[] = intval($karyawan);

                                if (in_array(strval($karyawan) . ',1', $decodeIsRead)) {
                                    $tempArrIsRead[] = strval($karyawan) . ',1';
                                } else {
                                    $tempArrIsRead[] = strval($karyawan) . ',0';
                                }

                                if (in_array(strval($karyawan) . ',1', $decodeIsClear)) {
                                    $tempArrIsClear[] = strval($karyawan) . ',1';
                                } else {
                                    $tempArrIsClear[] = strval($karyawan) . ',0';
                                }
                            }
                        }

                        foreach ($announcementEmployees as $leftKaryawan) {
                            $tempArrKaryawan[] = intval($leftKaryawan);
                            $tempArrIsRead[] = strval($leftKaryawan) . ',0';
                            $tempArrIsClear[] = strval($leftKaryawan) . ',0';
                        }

                        $checkAnnouncement->update([
                            'announcement_karyawan' => $tempArrKaryawan,
                            'announcement_karyawan_is_read' => $tempArrIsRead,
                            'announcement_karyawan_is_clear' => $tempArrIsClear
                        ]);
                    } else if($statusBefore === false && $statusCurrent === false) {
                        $announcementEmployees = $request->input('announcement_employee');

                        $tempArrKaryawan = array();
                        $tempArrIsRead = array();
                        $tempArrIsClear = array();

                        $decodeKaryawan = json_decode($checkAnnouncement->announcement_karyawan, true);
                        $decodeIsRead = json_decode($checkAnnouncement->announcement_karyawan_is_read, true);
                        $decodeIsClear = json_decode($checkAnnouncement->announcement_karyawan_is_clear, true);
                        foreach ($decodeKaryawan as $karyawan) {
                            $key = array_search($karyawan, $announcementEmployees);
                            if ($key !== false) {
                                unset($announcementEmployees[$key]); // Remove $karyawan from $announcementEmployees
                                $tempArrKaryawan[] = intval($karyawan);

                                if (in_array(strval($karyawan) . ',1', $decodeIsRead)) {
                                    $tempArrIsRead[] = strval($karyawan) . ',1';
                                } else {
                                    $tempArrIsRead[] = strval($karyawan) . ',0';
                                }

                                if (in_array(strval($karyawan) . ',1', $decodeIsClear)) {
                                    $tempArrIsClear[] = strval($karyawan) . ',1';
                                } else {
                                    $tempArrIsClear[] = strval($karyawan) . ',0';
                                }
                            }
                        }

                        foreach ($announcementEmployees as $leftKaryawan) {
                            $tempArrKaryawan[] = intval($leftKaryawan);
                            $tempArrIsRead[] = strval($leftKaryawan) . ',0';
                            $tempArrIsClear[] = strval($leftKaryawan) . ',0';
                        }

                        $checkAnnouncement->update([
                            'announcement_karyawan' => $tempArrKaryawan,
                            'announcement_karyawan_is_read' => $tempArrIsRead,
                            'announcement_karyawan_is_clear' => $tempArrIsClear
                        ]);
                    }
                    
                    DB::commit();
                    return response()->json([
                        'success' => true,
                        'message' => 'Berhasil melakukan update pada data Pengumuman.',
                        'data' => $checkAnnouncement
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
            $getAnnouncement = SettingAnnouncementModel::find($id);

            if(is_null($getAnnouncement)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengaturan Pengumuman tidak ditemukan.',
                ], 404);
            }

            if(session()->get('wajibpajak_current')['wajibpajak_id'] !== $getAnnouncement->ms_wajibpajak_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak memiliki akses ke Pengaturan Pengumuman yang diminta.',
                ], 403);
            }
                
            // Log::debug(session()->get('user_data'));
            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengambil data Pengaturan Pengumuman.',
                'data' => $getAnnouncement
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error get list announcement ' . $e->getMessage());

            return response()->json(['error' => 'Error get announcement. Please try again later.', 'message' => $e],  500);
        }
    }

    public function destroy($id)
    {
        $getAnnouncement = SettingAnnouncementModel::find($id);

        if(is_null($getAnnouncement)) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Pengumuman tidak ditemukan.',
            ], 404);
        }

        if(session()->get('wajibpajak_current')['wajibpajak_id'] !== $getAnnouncement->ms_wajibpajak_id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak memiliki akses ke Pengaturan Pengumuman yang diminta.',
            ], 403);
        }

        $success = $getAnnouncement->update(['announcement_active' => 0]);
        
        return response()->json([
            'success' => true,
            'message' => 'Berhasil menghapus data Pengaturan Pengumuman.'
        ], 200);
    }
}
