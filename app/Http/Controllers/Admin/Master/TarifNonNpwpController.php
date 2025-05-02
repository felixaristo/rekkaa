<?php
namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\TarifNonNpwpModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TarifNonNpwpController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Tarif Non NPWP',
            'content' => 'admin.master.tarif-nonnpwp.index',
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
            'tarifnonnpwp_active' => 1,
        ];
        $list = TarifNonNpwpModel::select('ms_tarif_nonnpwp.*')
        ->where($where)
        ->take($limit)->skip($offset)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = TarifNonNpwpModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {   
        // dd($request->all());
        $this->validate($request, [
            'tarifnonnpwp_taxtype' => 'required|in:PPh21,PPh23,PPh42',
            // 'tarifnonnpwp_description' => 'required',
            'tarifnonnpwp_rate' => 'required|numeric',
        ], [
            'tarifnonnpwp_taxtype.required' => 'Tipe wajib diisi!',
            // 'tarifnonnpwp_description.required' => 'Deskripsi wajib diisi!',
            'tarifnonnpwp_rate.numeric' => 'Rate harus angka!',
        ]);

        $save_data = [
            'tarifnonnpwp_taxtype' => $request->input('tarifnonnpwp_taxtype'),
            // 'tarifnonnpwp_description' => $request->input('tarifnonnpwp_description'),
            'tarifnonnpwp_rate' => $request->input('tarifnonnpwp_rate'),
        ];

        DB::beginTransaction();
        try {
            $tarifnonnpwp = TarifNonNpwpModel::create($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tarif Non Npwp berhasil disimpan',
                'data' => $tarifnonnpwp
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($tarifnonnpwpId, Request $request)
    {
        $this->validate($request, [
            // 'tarifnonnpwp_taxtype' => 'required|in:PPh21,PPh23,PPh42',
            // 'tarifnonnpwp_description' => 'required',
            'tarifnonnpwp_rate' => 'required|numeric',
        ], [
            // 'tarifnonnpwp_taxtype.required' => 'Tipe wajib diisi!',
            // 'tarifnonnpwp_description.required' => 'Deskripsi wajib diisi!',
            'tarifnonnpwp_rate.numeric' => 'Rate harus angka!',
        ]);

        $tarifnonnpwp = TarifNonNpwpModel::where([
            'tarifnonnpwp_id' => $tarifnonnpwpId, 
            'tarifnonnpwp_active' => 1,
        ])->first();
        if(!$tarifnonnpwp) {
            return response()->json([
                'success' => false,
                'message' => 'Tarif Non Npwp tidak ditemukan!'
            ]);
        }

        $save_data = [
            // 'tarifnonnpwp_taxtype' => $request->input('tarifnonnpwp_taxtype'),
            // 'tarifnonnpwp_description' => $request->input('tarifnonnpwp_description'),
            'tarifnonnpwp_rate' => $request->input('tarifnonnpwp_rate'),
        ];

        DB::beginTransaction();
        try {
            
            $tarifnonnpwp->update($save_data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tarif Non Npwp berhasil disimpan',
                'data' => $tarifnonnpwp
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($tarifnonnpwpId, Request $request)
    {
        $tarifnonnpwp = TarifNonNpwpModel::where([
            'tarifnonnpwp_id' => $tarifnonnpwpId, 
            'tarifnonnpwp_active' => 1,
        ])->first();
        if(!$tarifnonnpwp) {
            return response()->json([
                'success' => false,
                'message' => 'Tarif Non Npwp tidak ditemukan!'
            ]); 
        }
        
        // dd($karyawan_tunjangans);
        DB::beginTransaction();
        try {
            $tarifnonnpwp->update(['tarifnonnpwp_active' => 0]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tarif Non Npwp berhasil dihapus',
                'data' => $tarifnonnpwp
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