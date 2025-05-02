<?php
namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\PtkpModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PtkpController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'PTKP',
            'content' => 'admin.master.ptkp.index',
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
            'ptkp_active' => 1,
        ];
        $list = PtkpModel::select('ms_ptkp.*')
        ->where($where)
        ->take($limit)->skip($offset)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = PtkpModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {   
        // dd($request->all());
        $this->validate($request, [
            'ptkp_marriage_status' => 'required|in:Tidak Kawin (TK),Kawin (K),Kawin dengan penghasilan istri digabung (K/I)',
            'ptkp_description' => 'required',
            'ptkp_rate' => 'required|numeric',
        ], [
            'ptkp_marriage_status.required' => 'Status perkawinan wajib diisi!',
            'ptkp_description.required' => 'Deskripsi wajib diisi!',
            'ptkp_rate.numeric' => 'Rate harus angka!',
        ]);

        $save_data = [
            'ptkp_marriage_status' => $request->input('ptkp_marriage_status'),
            'ptkp_description' => $request->input('ptkp_description'),
            'ptkp_rate' => $request->input('ptkp_rate'),
        ];

        DB::beginTransaction();
        try {
            $ptkp = PtkpModel::create($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'PTKP berhasil disimpan',
                'data' => $ptkp
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($ptkpId, Request $request)
    {
        $this->validate($request, [
            'ptkp_description' => 'required',
            'ptkp_rate' => 'required|numeric',
        ], [
            'ptkp_description.required' => 'Deskripsi wajib diisi!',
            'ptkp_rate.numeric' => 'Rate harus angka!',
        ]);
        $ptkp = PtkpModel::where([
            'ptkp_id' => $ptkpId, 
            'ptkp_active' => 1,
        ])->first();
        if(!$ptkp) {
            return response()->json([
                'success' => false,
                'message' => 'PTKP tidak ditemukan!'
            ]);
        }

        $save_data = [
            'ptkp_description' => $request->input('ptkp_description'),
            'ptkp_rate' => $request->input('ptkp_rate'),
        ];

        DB::beginTransaction();
        try {
            
            $ptkp->update($save_data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'PTKP berhasil disimpan',
                'data' => $ptkp
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($ptkpId, Request $request)
    {
        $ptkp = PtkpModel::where([
            'ptkp_id' => $ptkpId, 
            'ptkp_active' => 1,
        ])->first();
        if(!$ptkp) {
            return response()->json([
                'success' => false,
                'message' => 'PTKP tidak ditemukan!'
            ]); 
        }
        
        // dd($karyawan_tunjangans);
        DB::beginTransaction();
        try {
            $ptkp->update(['ptkp_active' => 0]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'PTKP berhasil dihapus',
                'data' => $ptkp
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