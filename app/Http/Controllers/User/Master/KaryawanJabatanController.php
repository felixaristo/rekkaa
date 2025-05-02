<?php

namespace App\Http\Controllers\User\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanJabatanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KaryawanJabatanController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;

        $where = [
            'karyawanjabatan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
            'ms_user_id' => $user_id,
        ];
        $list = KaryawanJabatanModel::select('ms_karyawan_jabatan.*')
        ->where($where)
        ->take($limit)->skip($offset)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = KaryawanJabatanModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawanjabatan_name = $request->input('karyawanjabatan_name');
        $rules = [
            'karyawanjabatan_name' => ['required',
                Rule::unique('ms_karyawan_jabatan')->where(function ($query) use ($wajibpajak_id) {
                    return $query->where([
                        'ms_wajibpajak_id' => $wajibpajak_id,
                    ]);
                })
            ],
        ];
        $this->validate($request, $rules, [
            'karyawanjabatan_name.required' => 'Nama Jabatan wajib diisi!',
            'karyawanjabatan_name.unique' => 'Nama Jabatan sudah ada!',
        ]);

        // check if duplicate name
        $divisi = KaryawanJabatanModel::where(['ms_wajibpajak_id' => $wajibpajak_id])
        ->whereRaw('(LOWER(karyawanjabatan_name) = ?)', [strtolower(trim($karyawanjabatan_name))])->first();
        if($divisi) {
            return response()->json([
                'errors' => [
                    'karyawanjabatan_name' => ['Nama Jabatan sudah ada!']
                ],
                'message' => 'The given data was invalid.'
            ], 422);
        }

        $save_data = [
            'karyawanjabatan_name' => ucwords($karyawanjabatan_name),
            'ms_user_id' => $user_id,
            'ms_wajibpajak_id' => $wajibpajak_id
        ];

        DB::beginTransaction();
        try {
            $karyawanjabatan = KaryawanJabatanModel::create($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Jabatan karyawan berhasil disimpan',
                'data' => $karyawanjabatan
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($karyawanjabatanId, Request $request)
    {
        // dd($request->all());
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawanjabatan_name = $request->input('karyawanjabatan_name');
        $rules = [
            'karyawanjabatan_name' => 'required',
        ];

        $this->validate($request, $rules, [
            'karyawanjabatan_name.required' => 'Nama Jabatan wajib diisi!',
        ]);

        
        // check if duplicate name
        $divisi = KaryawanJabatanModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->where('karyawanjabatan_id', '<>', $karyawanjabatanId)
        ->whereRaw('(LOWER(karyawanjabatan_name) = ?)', [strtolower(trim($karyawanjabatan_name))])->first();

        if($divisi) {
            return response()->json([
                'errors' => [
                    'karyawanjabatan_name' => ['Nama Jabatan sudah ada!']
                ],
                'message' => 'The given data was invalid.'
            ], 422);
        }

        $karyawanjabatan = KaryawanJabatanModel::where([
            'karyawanjabatan_id' => $karyawanjabatanId, 
            'karyawanjabatan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$karyawanjabatan) {
            return response()->json([
                'success' => false,
                'message' => 'Jabatan karyawan tidak ditemukan!'
            ]);
        }

        $save_data = [
            'karyawanjabatan_name' => ucwords($karyawanjabatan_name),
        ];

        DB::beginTransaction();
        try {
            
            $karyawanjabatan->update($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Jabatan karyawan berhasil disimpan',
                'data' => $karyawanjabatan
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($karyawanjabatanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawanjabatan = KaryawanJabatanModel::select('karyawanjabatan_id')
        ->with(['karyawan'])
        ->where([
            'karyawanjabatan_id' => $karyawanjabatanId, 
            'karyawanjabatan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$karyawanjabatan) {
            return response()->json([
                'success' => false,
                'message' => 'Jabatan karyawan tidak ditemukan!'
            ]); 
        }

        if($karyawanjabatan->karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Jabatan karyawan tidak dapat dihapus! <br>Jabatan sudah digunakan pada karyawan.'
            ]); 
        }
        
        // dd($karyawan_tunjangans);
        DB::beginTransaction();
        try {
            $karyawanjabatan->update(['karyawanjabatan_active' => 0]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Jabatan karyawan berhasil dihapus',
                'data' => $karyawanjabatan
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
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $q = $request->get('q');
        $limit = $request->get('limit') ? $request->get('limit') : 10;

        $data = KaryawanJabatanModel::select('karyawanjabatan_id', 'karyawanjabatan_name')
        ->when($q, function ($query, $q) {
            return $query->where('karyawanjabatan_name', 'ilike', '%'.$q.'%');
        })
        ->where(['ms_wajibpajak_id' => $wajibpajak_id, 'karyawanjabatan_active' => 1])
        ->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}
