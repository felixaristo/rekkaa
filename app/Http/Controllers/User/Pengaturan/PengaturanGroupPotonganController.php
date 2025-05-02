<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Setting\SettingGroupPotonganKaryawanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PengaturanGroupPotonganController extends Controller
{
    //  /**
    //  * Display a index of the resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function index(Request $request)
    // {
    //     $data = [
    //         'title' => 'Pengaturan Group Potongan',
    //         // 'content' => 'user.pengaturan.potongan.index',
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
            'stgrouppotongankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ];
        $list = SettingGroupPotonganKaryawanModel::select('st_group_potongan_karyawan.*')
        ->where($where)
        ->take($limit)->skip($offset)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = SettingGroupPotonganKaryawanModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $stgrouppotongankaryawan_name = $request->input('stgrouppotongankaryawan_name');
        $rules = [
            'stgrouppotongankaryawan_name' => ['required',
                Rule::unique('st_group_potongan_karyawan')->where(function ($query) use ($wajibpajak_id, $stgrouppotongankaryawan_name) {
                    return $query->where([
                        'ms_wajibpajak_id' => $wajibpajak_id,
                        'stgrouppotongankaryawan_active' => 1,
                    ]);
                })
            ],
        ];
        $this->validate($request, $rules, [
            'stgrouppotongankaryawan_name.required' => 'Nama Group wajib diisi!',
            'stgrouppotongankaryawan_name.unique' => 'Nama Group sudah ada!',
        ]);
        // check if duplicate name
        $stgrouppotongankaryawan = SettingGroupPotonganKaryawanModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'stgrouppotongankaryawan_active' => 1,
        ])
        ->whereRaw('(LOWER(stgrouppotongankaryawan_name) = ?)', [strtolower(trim($stgrouppotongankaryawan_name))])->first();
        if($stgrouppotongankaryawan) {
            return response()->json([
                'errors' => [
                    'stgrouppotongankaryawan_name' => ['Nama Group sudah ada!']
                ],
                'message' => 'Nama Group sudah ada.'
            ], 422);
        }
        $save_data = [
            'stgrouppotongankaryawan_name' => ucwords($stgrouppotongankaryawan_name),
            'ms_wajibpajak_id' => $wajibpajak_id
        ];

        DB::beginTransaction();
        try {
            $stpotongankaryawan = SettingGroupPotonganKaryawanModel::create($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Group Slip Gaji karyawan berhasil disimpan',
                'data' => $stpotongankaryawan
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($stgrouppotongankaryawanId, Request $request)
    {
        // dd($request->all());
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $stgrouppotongankaryawan_name = $request->input('stgrouppotongankaryawan_name');
        $rules = [
            'stgrouppotongankaryawan_name' => ['required'],
        ];

        $this->validate($request, $rules, [
            'stgrouppotongankaryawan_name.required' => 'Nama Group wajib diisi!',
        ]);
        // check if duplicate name
        $stgrouppotongankaryawan = SettingGroupPotonganKaryawanModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'stgrouppotongankaryawan_active' => 1,
        ])
        ->whereRaw('(LOWER(stgrouppotongankaryawan_name) = ?)', [strtolower(trim($stgrouppotongankaryawan_name))])
        ->where('stgrouppotongankaryawan_id', '<>', $stgrouppotongankaryawanId)->first();

        if($stgrouppotongankaryawan) {
            return response()->json([
                'errors' => [
                    'stgrouppotongankaryawan_name' => ['Nama Group sudah ada!']
                ],
                'message' => 'Nama Group sudah ada.'
            ], 422);
        }
        
        $stgrouppotongankaryawan = SettingGroupPotonganKaryawanModel::where([
            'stgrouppotongankaryawan_id' => $stgrouppotongankaryawanId, 
            'stgrouppotongankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$stgrouppotongankaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Group karyawan tidak ditemukan!'
            ]);
        }

        $save_data = [
            'stgrouppotongankaryawan_name' => ucwords($stgrouppotongankaryawan_name),
        ];

        DB::beginTransaction();
        try {
            
            $stgrouppotongankaryawan->update($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Group karyawan berhasil disimpan',
                'data' => $stgrouppotongankaryawan
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($stgrouppotongankaryawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $stgrouppotongankaryawan = SettingGroupPotonganKaryawanModel::select('stgrouppotongankaryawan_id', DB::raw("(SELECT COUNT(*) FROM st_potongan_karyawan WHERE st_grouppotongankaryawan_id = stgrouppotongankaryawan_id AND stpotongankaryawan_active='1') as totalpotongan"))
        ->with(['stpotongan' => function($q) {
            return $q->where(['stpotongankaryawan_active' => '1']);
        }])
        ->where([
            'stgrouppotongankaryawan_id' => $stgrouppotongankaryawanId, 
            'stgrouppotongankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$stgrouppotongankaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Group karyawan tidak ditemukan!'
            ]); 
        }

        // dd($stgrouppotongankaryawan);
        if($stgrouppotongankaryawan->totalpotongan >= 1) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Group karyawan tidak dapat dihapus! <br>Group sudah digunakan.'
            ]); 
        }
        
        // dd($karyawan_potongans);
        DB::beginTransaction();
        try {
            $stgrouppotongankaryawan->update(['stgrouppotongankaryawan_active' => 0]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Group karyawan berhasil dihapus',
                'data' => $stgrouppotongankaryawan
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

        $data = SettingGroupPotonganKaryawanModel::select('stgrouppotongankaryawan_id','stgrouppotongankaryawan_name')
        ->when($q, function ($query, $q) {
            return $query->where('stgrouppotongankaryawan_name', 'ilike', '%'.$q.'%');
        })
        ->where([
            'stgrouppotongankaryawan_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])
        ->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    // public function updatekaryawan($stpotongankaryawanId, Request $request)
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
    //     $stpotongankaryawan = SettingPotonganKaryawanModel::where([
    //         'stpotongankaryawan_id' => $stpotongankaryawanId, 
    //         'stpotongankaryawan_active' => 1,
    //         'ms_wajibpajak_id' => $wajibpajak_id,
    //     ])->first();
    //     if(!$stpotongankaryawan) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Pengaturan Potongan karyawan tidak ditemukan!'
    //         ]);
    //     }
    //     // check if the employees belong to entities
    //     $karyawan = KaryawanModel::select('karyawan_id', 'st_potongan_id')
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
    //     $explode_karyawanpotonganids = ($karyawan->st_potongan_id) ? explode(',', $karyawan->st_potongan_id) : [];
    //     array_push($explode_karyawanpotonganids, $stpotongankaryawan->stpotongankaryawan_id); // insert to current employees potongan.

    //     DB::beginTransaction();
    //     try {
    //         // Update the employees allowance
    //         KaryawanModel::where(['karyawan_id' => $karyawan_id])
    //         ->update([
    //             'st_potongan_id' => implode(',', $explode_karyawanpotonganids)
    //         ]);
            
    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Karyawan berhasil ditambahkan',
    //             'data' => $stpotongankaryawan
    //         ]);
            

    //     } catch(Error $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    // public function deletekaryawan($stpotongankaryawanId, Request $request)
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
    //     $stpotongankaryawan = SettingPotonganKaryawanModel::where([
    //         'stpotongankaryawan_id' => $stpotongankaryawanId, 
    //         'stpotongankaryawan_active' => 1,
    //         'ms_wajibpajak_id' => $wajibpajak_id,
    //     ])->first();
    //     if(!$stpotongankaryawan) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Pengaturan Potongan karyawan tidak ditemukan!'
    //         ]);
    //     }
    //     // check if the employees belong to entities
    //     $karyawan = KaryawanModel::select('karyawan_id', 'st_potongan_id')
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
    //     $explode_karyawanpotonganids = ($karyawan->st_potongan_id) ? explode(',', $karyawan->st_potongan_id) : [];
    //     // array_push($explode_karyawanpotonganids, $stpotongankaryawan->stpotongankaryawan_id); // insert to current employees potongan.
    //     // check if allowance exist on employee
    //     $check_potongankey = array_search($stpotongankaryawan->stpotongankaryawan_id, $explode_karyawanpotonganids);
    //     if(!$check_potongankey) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Potongan Karyawan tidak ditemukan!'
    //         ]);
    //     }

    //     // remove potongan
    //     unset($explode_karyawanpotonganids[$check_potongankey]);

    //     DB::beginTransaction();
    //     try {
    //         // Update the employees allowance
    //         KaryawanModel::where(['karyawan_id' => $karyawan_id])
    //         ->update([
    //             'st_potongan_id' => implode(',', $explode_karyawanpotonganids)
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