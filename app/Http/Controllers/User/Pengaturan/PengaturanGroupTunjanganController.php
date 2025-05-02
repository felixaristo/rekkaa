<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Setting\SettingGroupTunjanganKaryawanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PengaturanGroupTunjanganController extends Controller
{
    //  /**
    //  * Display a index of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function index(Request $request)
    // {
    //     $data = [
    //         'title' => 'Pengaturan Group Tunjangan',
    //         // 'content' => 'user.pengaturan.tunjangan.index',
    //         // 'wajib_pajak_user' => WajibPajakUserModel::where(['ms_user_id' => session()->get('user_data')['user_id']])->count()
    //     ];
    //     if($request->ajax()) {
    //         return view($data['content'], $data)->render();
    //     } else {
    //         return view('user.index', $data);
    //     }
    // }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;

        $where = [
            'stgrouptunjangankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ];
        $list = SettingGroupTunjanganKaryawanModel::select('st_group_tunjangan_karyawan.*')
        ->where($where)
        ->take($limit)->skip($offset)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = SettingGroupTunjanganKaryawanModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $stgrouptunjangankaryawan_name = $request->input('stgrouptunjangankaryawan_name');
        $rules = [
            'stgrouptunjangankaryawan_name' => ['required',
                Rule::unique('st_group_tunjangan_karyawan')->where(function ($query) use ($wajibpajak_id, $stgrouptunjangankaryawan_name) {
                    return $query->where([
                        'ms_wajibpajak_id' => $wajibpajak_id,
                        'stgrouptunjangankaryawan_active' => 1,
                    ]);
                })
            ],
        ];
        $this->validate($request, $rules, [
            'stgrouptunjangankaryawan_name.required' => 'Nama Group wajib diisi!',
            'stgrouptunjangankaryawan_name.unique' => 'Nama Group sudah ada!',
        ]);

        // check if duplicate name
        $stgrouptunjangankaryawan = SettingGroupTunjanganKaryawanModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'stgrouptunjangankaryawan_active' => 1,
        ])
        ->whereRaw('(LOWER(stgrouptunjangankaryawan_name) = ?)', [strtolower(trim($stgrouptunjangankaryawan_name))])->first();
        if($stgrouptunjangankaryawan) {
            return response()->json([
                'errors' => [
                    'stgrouptunjangankaryawan_name' => ['Nama Group sudah ada!']
                ],
                'message' => 'Nama Group sudah ada.'
            ], 422);
        }

        $save_data = [
            'stgrouptunjangankaryawan_name' => ucwords($stgrouptunjangankaryawan_name),
            'ms_wajibpajak_id' => $wajibpajak_id
        ];

        DB::beginTransaction();
        try {
            $sttunjangankaryawan = SettingGroupTunjanganKaryawanModel::create($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Grup Slip Gaji karyawan berhasil disimpan',
                'data' => $sttunjangankaryawan
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($stgrouptunjangankaryawanId, Request $request)
    {
        // dd($request->all());
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $stgrouptunjangankaryawan_name = $request->input('stgrouptunjangankaryawan_name');
        $rules = [
            'stgrouptunjangankaryawan_name' => 'required',
        ];

        $this->validate($request, $rules, [
            'stgrouptunjangankaryawan_name.required' => 'Nama Group wajib diisi!',
        ]);

        // check if duplicate name
        $stgrouptunjangankaryawan = SettingGroupTunjanganKaryawanModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'stgrouptunjangankaryawan_active' => 1,
        ])->whereRaw('(LOWER(stgrouptunjangankaryawan_name) = ?)', [strtolower(trim($stgrouptunjangankaryawan_name))])->where('stgrouptunjangankaryawan_id', '<>', $stgrouptunjangankaryawanId)->first();

        if($stgrouptunjangankaryawan) {
            return response()->json([
                'errors' => [
                    'stgrouptunjangankaryawan_name' => ['Nama Group sudah ada!']
                ],
                'message' => 'Nama Group sudah ada.'
            ], 422);
        }

        $stgrouptunjangankaryawan = SettingGroupTunjanganKaryawanModel::where([
            'stgrouptunjangankaryawan_id' => $stgrouptunjangankaryawanId, 
            'stgrouptunjangankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$stgrouptunjangankaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Group karyawan tidak ditemukan!'
            ]);
        }

        $save_data = [
            'stgrouptunjangankaryawan_name' => ucwords($stgrouptunjangankaryawan_name),
        ];

        DB::beginTransaction();
        try {
            
            $stgrouptunjangankaryawan->update($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Group karyawan berhasil disimpan',
                'data' => $stgrouptunjangankaryawan
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($stgrouptunjangankaryawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $stgrouptunjangankaryawan = SettingGroupTunjanganKaryawanModel::select('stgrouptunjangankaryawan_id', DB::raw("(SELECT COUNT(*) FROM st_tunjangan_karyawan WHERE st_grouptunjangankaryawan_id = stgrouptunjangankaryawan_id AND sttunjangankaryawan_active='1') as totaltunjangan"))
        ->where([
            'stgrouptunjangankaryawan_id' => $stgrouptunjangankaryawanId, 
            'stgrouptunjangankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$stgrouptunjangankaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Group karyawan tidak ditemukan!'
            ]); 
        }

        // dd($stgrouptunjangankaryawan);
        if($stgrouptunjangankaryawan->totaltunjangan >= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Group karyawan tidak dapat dihapus! <br>Group sudah digunakan.'
            ]); 
        }
        
        // dd($karyawan_tunjangans);
        DB::beginTransaction();
        try {
            $stgrouptunjangankaryawan->update(['stgrouptunjangankaryawan_active' => 0]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Group karyawan berhasil dihapus',
                'data' => $stgrouptunjangankaryawan
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
     * Display a select of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        $q = $request->get('q');
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $data = SettingGroupTunjanganKaryawanModel::select('stgrouptunjangankaryawan_id','stgrouptunjangankaryawan_name')
        ->when($q, function ($query, $q) {
            return $query->where('stgrouptunjangankaryawan_name', 'ilike', '%'.$q.'%');
        })
        ->where([
            'stgrouptunjangankaryawan_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])
        ->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    // public function updatekaryawan($sttunjangankaryawanId, Request $request)
    // {
    //     // dd($request->all());
    //     $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
    //     $karyawan_id = $request->input('karyawan_id');
    //     $rules = [
    //         'karyawan_id' => 'required'
    //     ];

    //     $this->validate($request, $rules, [
    //         'karyawan_id.required' => 'Karyawan wajib diisi!',
    //     ]);
    //     $sttunjangankaryawan = SettingTunjanganKaryawanModel::where([
    //         'sttunjangankaryawan_id' => $sttunjangankaryawanId, 
    //         'sttunjangankaryawan_active' => 1,
    //         'ms_wajibpajak_id' => $wajibpajak_id,
    //     ])->first();
    //     if(!$sttunjangankaryawan) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Pengaturan Tunjangan karyawan tidak ditemukan!'
    //         ]);
    //     }
    //     // check if the employees belong to entities
    //     $karyawan = KaryawanModel::select('karyawan_id', 'st_tunjangan_id')
    //     ->where([
    //         'karyawan_id' => $karyawan_id,
    //         'karyawan_active' => 1,
    //         'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
    //     ])->first();

    //     if(!$karyawan) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Karyawan tidak ditemukan!'
    //         ]);
    //     }
    //     $explode_karyawantunjanganids = ($karyawan->st_tunjangan_id) ? explode(',', $karyawan->st_tunjangan_id) : [];
    //     array_push($explode_karyawantunjanganids, $sttunjangankaryawan->sttunjangankaryawan_id); // insert to current employees tunjangan.

    //     DB::beginTransaction();
    //     try {
    //         // Update the employees allowance
    //         KaryawanModel::where(['karyawan_id' => $karyawan_id])
    //         ->update([
    //             'st_tunjangan_id' => implode(',', $explode_karyawantunjanganids)
    //         ]);
            
    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Karyawan berhasil ditambahkan',
    //             'data' => $sttunjangankaryawan
    //         ]);
            

    //     } catch(Error $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    // public function deletekaryawan($sttunjangankaryawanId, Request $request)
    // {
    //     // dd($request->all());
    //     $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
    //     $karyawan_id = $request->input('karyawan_id');
    //     $rules = [
    //         'karyawan_id' => 'required'
    //     ];

    //     $this->validate($request, $rules, [
    //         'karyawan_id.required' => 'Karyawan wajib diisi!',
    //     ]);
    //     $sttunjangankaryawan = SettingTunjanganKaryawanModel::where([
    //         'sttunjangankaryawan_id' => $sttunjangankaryawanId, 
    //         'sttunjangankaryawan_active' => 1,
    //         'ms_wajibpajak_id' => $wajibpajak_id,
    //     ])->first();
    //     if(!$sttunjangankaryawan) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Pengaturan Tunjangan karyawan tidak ditemukan!'
    //         ]);
    //     }
    //     // check if the employees belong to entities
    //     $karyawan = KaryawanModel::select('karyawan_id', 'st_tunjangan_id')
    //     ->where([
    //         'karyawan_id' => $karyawan_id,
    //         'karyawan_active' => 1,
    //         'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
    //     ])->first();

    //     if(!$karyawan) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Karyawan tidak ditemukan!'
    //         ]);
    //     }
    //     $explode_karyawantunjanganids = ($karyawan->st_tunjangan_id) ? explode(',', $karyawan->st_tunjangan_id) : [];
    //     // array_push($explode_karyawantunjanganids, $sttunjangankaryawan->sttunjangankaryawan_id); // insert to current employees tunjangan.
    //     // check if allowance exist on employee
    //     $check_tunjangankey = array_search($sttunjangankaryawan->sttunjangankaryawan_id, $explode_karyawantunjanganids);
    //     if(!$check_tunjangankey) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Tunjangan Karyawan tidak ditemukan!'
    //         ]);
    //     }

    //     // remove tunjangan
    //     unset($explode_karyawantunjanganids[$check_tunjangankey]);

    //     DB::beginTransaction();
    //     try {
    //         // Update the employees allowance
    //         KaryawanModel::where(['karyawan_id' => $karyawan_id])
    //         ->update([
    //             'st_tunjangan_id' => implode(',', $explode_karyawantunjanganids)
    //         ]);
            
    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Karyawan berhasil dihapus',
    //         ]);
            

    //     } catch(Error $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
}