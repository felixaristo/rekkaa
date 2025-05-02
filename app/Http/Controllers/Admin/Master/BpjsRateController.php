<?php
namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\BpjsRateModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BpjsRateController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'BPJS Rate',
            'content' => 'admin.master.bpjsrate.index',
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
            'bpjsrate_active' => 1,
        ];
        $list = BpjsRateModel::select('ms_bpjs_rate.*')
        ->where($where)
        ->take($limit)->skip($offset)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = BpjsRateModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {   
        // dd($request->all());
        $this->validate($request, [
            'bpjsrate_category' => 'required|in:TK,KES,JP',
            'bpjsrate_code' => 'required',
            'bpjsrate_description' => 'required',
            'bpjsrate_calculationtype' => 'required|in:PENAMBAH,PENGURANG',
            'bpjsrate_rate' => 'required|numeric',
        ], [
            'bpjsrate_category.required' => 'Kategori wajib diisi!',
            'bpjsrate_code.required' => 'Kode wajib diisi!',
            'bpjsrate_description.required' => 'Deskripsi wajib diisi!',
            'bpjsrate_calculationtype.required' => 'Tipe wajib diisi!',
            'bpjsrate_rate.numeric' => 'Nilai tunjangan harus angka!',
        ]);

        $save_data = [
            'bpjsrate_category' => $request->input('bpjsrate_category'),
            'bpjsrate_code' => $request->input('bpjsrate_code'),
            'bpjsrate_description' => $request->input('bpjsrate_description'),
            'bpjsrate_calculationtype' => $request->input('bpjsrate_calculationtype'),
            'bpjsrate_rate' => $request->input('bpjsrate_rate'),
        ];

        DB::beginTransaction();
        try {
            $bpjsrate = BpjsRateModel::create($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Rate BPJS berhasil disimpan',
                'data' => $bpjsrate
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($bpjsrateId, Request $request)
    {
        $this->validate($request, [
            // 'bpjsrate_category' => 'required|in:TK,KES,JP',
            // 'bpjsrate_code' => 'required',
            'bpjsrate_description' => 'required',
            'bpjsrate_calculationtype' => 'required|in:PENAMBAH,PENGURANG',
            'bpjsrate_rate' => 'required|numeric',
        ], [
            // 'bpjsrate_category.required' => 'Kategori wajib diisi!',
            // 'bpjsrate_code.required' => 'Kode wajib diisi!',
            'bpjsrate_description.required' => 'Deskripsi wajib diisi!',
            'bpjsrate_calculationtype.required' => 'Tipe wajib diisi!',
            'bpjsrate_rate.numeric' => 'Nilai tunjangan harus angka!',
        ]);
        $bpjsrate = BpjsRateModel::where([
            'bpjsrate_id' => $bpjsrateId, 
            'bpjsrate_active' => 1,
        ])->first();
        if(!$bpjsrate) {
            return response()->json([
                'success' => false,
                'message' => 'Rate BPJS tidak ditemukan!'
            ]);
        }

        $save_data = [
            // 'bpjsrate_category' => $request->input('bpjsrate_category'),
            // 'bpjsrate_code' => $request->input('bpjsrate_code'),
            'bpjsrate_description' => $request->input('bpjsrate_description'),
            'bpjsrate_calculationtype' => $request->input('bpjsrate_calculationtype'),
            'bpjsrate_rate' => $request->input('bpjsrate_rate'),
        ];

        DB::beginTransaction();
        try {
            
            $bpjsrate->update($save_data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Rate BPJS berhasil disimpan',
                'data' => $bpjsrate
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($bpjsrateId, Request $request)
    {
        $bpjsrate = BpjsRateModel::where([
            'bpjsrate_id' => $bpjsrateId, 
            'bpjsrate_active' => 1,
        ])->first();
        if(!$bpjsrate) {
            return response()->json([
                'success' => false,
                'message' => 'Rate BPJS tidak ditemukan!'
            ]); 
        }
        
        // dd($karyawan_tunjangans);
        DB::beginTransaction();
        try {
            $bpjsrate->update(['bpjsrate_active' => 0]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Rate BPJS berhasil dihapus',
                'data' => $bpjsrate
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