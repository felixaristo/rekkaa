<?php

namespace App\Http\Controllers\User\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanDivisiModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KaryawanDivisiController extends Controller
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
            'karyawandivisi_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
            'ms_user_id' => $user_id,
        ];
        $list = KaryawanDivisiModel::select('ms_karyawan_divisi.*')
        ->where($where)
        ->take($limit)->skip($offset)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = KaryawanDivisiModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawandivisi_name = $request->input('karyawandivisi_name');
        $rules = [
            'karyawandivisi_name' => ['required',
                Rule::unique('ms_karyawan_divisi')->where(function ($query) use ($wajibpajak_id) {
                    return $query->where([
                        'ms_wajibpajak_id' => $wajibpajak_id,
                    ]);
                })
            ],
        ];
        $this->validate($request, $rules, [
            'karyawandivisi_name.required' => 'Nama Divisi wajib diisi!',
            'karyawandivisi_name.unique' => 'Nama Divisi sudah ada!',
        ]);

        // check if duplicate name
        $divisi = KaryawanDivisiModel::where(['ms_wajibpajak_id' => $wajibpajak_id])
        ->whereRaw('(LOWER(karyawandivisi_name) = ?)', [strtolower(trim($karyawandivisi_name))])->first();
        if($divisi) {
            return response()->json([
                'errors' => [
                    'karyawandivisi_name' => ['Nama Divisi sudah ada!']
                ],
                'message' => 'The given data was invalid.'
            ], 422);
        }

        $save_data = [
            'karyawandivisi_name' => ucwords($karyawandivisi_name),
            'ms_user_id' => $user_id,
            'ms_wajibpajak_id' => $wajibpajak_id
        ];

        DB::beginTransaction();
        try {
            $karyawandivisi = KaryawanDivisiModel::create($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Divisi karyawan berhasil disimpan',
                'data' => $karyawandivisi
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($karyawandivisiId, Request $request)
    {
        // dd($request->all());
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawandivisi_name = $request->input('karyawandivisi_name');
        $rules = [
            'karyawandivisi_name' => 'required',
        ];

        $this->validate($request, $rules, [
            'karyawandivisi_name.required' => 'Nama Divisi wajib diisi!',
        ]);

        // check if duplicate name
        $divisi = KaryawanDivisiModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->where('karyawandivisi_id', '<>', $karyawandivisiId)
        ->whereRaw('(LOWER(karyawandivisi_name) = ?)', [strtolower(trim($karyawandivisi_name))])->first();

        if($divisi) {
            return response()->json([
                'errors' => [
                    'karyawandivisi_name' => ['Nama Divisi sudah ada!']
                ],
                'message' => 'The given data was invalid.'
            ], 422);
        }

        $karyawandivisi = KaryawanDivisiModel::where([
            'karyawandivisi_id' => $karyawandivisiId, 
            'karyawandivisi_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$karyawandivisi) {
            return response()->json([
                'success' => false,
                'message' => 'Divisi karyawan tidak ditemukan!'
            ]);
        }

        $save_data = [
            'karyawandivisi_name' => ucwords($karyawandivisi_name),
        ];

        DB::beginTransaction();
        try {
            
            $karyawandivisi->update($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Divisi karyawan berhasil disimpan',
                'data' => $karyawandivisi
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($karyawandivisiId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawandivisi = KaryawanDivisiModel::select('karyawandivisi_id')
        ->with(['karyawan'])
        ->where([
            'karyawandivisi_id' => $karyawandivisiId, 
            'karyawandivisi_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$karyawandivisi) {
            return response()->json([
                'success' => false,
                'message' => 'Divisi karyawan tidak ditemukan!'
            ]); 
        }

        if($karyawandivisi->karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Divisi karyawan tidak dapat dihapus! <br>Divisi sudah digunakan pada karyawan.'
            ]); 
        }
        
        // dd($karyawan_tunjangans);
        DB::beginTransaction();
        try {
            $karyawandivisi->update(['karyawandivisi_active' => 0]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Divisi karyawan berhasil dihapus',
                'data' => $karyawandivisi
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

        $data = KaryawanDivisiModel::select('karyawandivisi_id', 'karyawandivisi_name')
        ->when($q, function ($query, $q) {
            return $query->where('karyawandivisi_name', 'ilike', '%'.$q.'%');
        })->where([
            'ms_wajibpajak_id' => $wajibpajak_id, 
            'karyawandivisi_active' => 1
        ])->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}
