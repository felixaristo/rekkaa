<?php
namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\KepemilikanNpwpModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KepemilikanNpwpController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Kepemilikan Npwp',
            'content' => 'admin.master.kepemilikan-npwp.index',
            // 'wajib_pajak_user' => WajibPajakUserModel::where(['ms_user_id' => session()->get('user_data')['user_id']])->count()
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('admin.index', $data);
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

        $where = [
            'kepemilikannpwp_active' => 1,
        ];
        $list = KepemilikanNpwpModel::select('ms_kepemilikan_npwp.*')
        ->where($where)
        ->take($limit)->skip($offset)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = KepemilikanNpwpModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {   
        // dd($request->all());
        $this->validate($request, [
            'kepemilikannpwp_code' => 'required|in:NPWP,NO-NPWP',
            'kepemilikannpwp_name' => 'required',
            'kepemilikannpwp_value' => 'required|numeric',
        ], [
            'kepemilikannpwp_code.required' => 'Kode wajib diisi!',
            'kepemilikannpwp_name.required' => 'Nama wajib diisi!',
            'kepemilikannpwp_value.numeric' => 'Nilai tunjangan harus angka!',
        ]);

        $save_data = [
            'kepemilikannpwp_code' => $request->input('kepemilikannpwp_code'),
            'kepemilikannpwp_name' => $request->input('kepemilikannpwp_name'),
            'kepemilikannpwp_value' => $request->input('kepemilikannpwp_value'),
        ];
        // dd($save_data);

        DB::beginTransaction();
        try {
            $kepemilikannpwp = KepemilikanNpwpModel::create($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kepemilikan Npwp berhasil disimpan',
                'data' => $kepemilikannpwp
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($kepemilikannpwpId, Request $request)
    {
        $this->validate($request, [
            'kepemilikannpwp_name' => 'required',
            'kepemilikannpwp_value' => 'required|numeric',
        ], [
            'kepemilikannpwp_name.required' => 'Nama wajib diisi!',
            'kepemilikannpwp_value.numeric' => 'Nilai tunjangan harus angka!',
        ]);
        $kepemilikannpwp = KepemilikanNpwpModel::where([
            'kepemilikannpwp_id' => $kepemilikannpwpId, 
            'kepemilikannpwp_active' => 1,
        ])->first();
        if(!$kepemilikannpwp) {
            return response()->json([
                'success' => false,
                'message' => 'Kepemilikan Npwp tidak ditemukan!'
            ]);
        }

        $save_data = [
            'kepemilikannpwp_name' => $request->input('kepemilikannpwp_name'),
            'kepemilikannpwp_value' => $request->input('kepemilikannpwp_value'),
        ];

        DB::beginTransaction();
        try {
            
            $kepemilikannpwp->update($save_data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kepemilikan Npwp berhasil disimpan',
                'data' => $kepemilikannpwp
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($kepemilikannpwpId, Request $request)
    {
        $kepemilikannpwp = KepemilikanNpwpModel::where([
            'kepemilikannpwp_id' => $kepemilikannpwpId, 
            'kepemilikannpwp_active' => 1,
        ])->first();
        if(!$kepemilikannpwp) {
            return response()->json([
                'success' => false,
                'message' => 'Kepemilikan Npwp tidak ditemukan!'
            ]); 
        }
        
        // dd($karyawan_tunjangans);
        DB::beginTransaction();
        try {
            $kepemilikannpwp->update(['kepemilikannpwp_active' => 0]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kepemilikan Npwp berhasil dihapus',
                'data' => $kepemilikannpwp
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