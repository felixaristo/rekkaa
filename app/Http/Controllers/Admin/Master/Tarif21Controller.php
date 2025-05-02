<?php
namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\Tarif21Model;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Tarif21Controller extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Tarif PPh 21',
            'content' => 'admin.master.tarif-pph21.index',
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
            'tarif21_active' => 1,
        ];
        $list = Tarif21Model::select('ms_tarif21.*')
        ->where($where)
        ->take($limit)->skip($offset)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = Tarif21Model::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {   
        // dd($request->all());
        $this->validate($request, [
            'tarif21_year' => 'required|numeric',
            'tarif21_description' => 'required',
            'tarif21_rate' => 'required|numeric',
            'tarif21_startincome' => 'required|numeric',
            'tarif21_endincome' => 'required|numeric',
        ], [
            'tarif21_year.required' => 'Tahun wajib diisi!',
            'tarif21_description.required' => 'Deskripsi wajib diisi!',
            'tarif21_rate.numeric' => 'Rate harus angka!',
            'tarif21_startincome.numeric' => 'Batas awal pendapatan harus angka!',
            'tarif21_endincome.numeric' => 'Batas akhir pendapatan harus angka!',
        ]);

        $save_data = [
            'tarif21_year' => $request->input('tarif21_year'),
            'tarif21_description' => $request->input('tarif21_description'),
            'tarif21_rate' => $request->input('tarif21_rate'),
            'tarif21_startincome' => $request->input('tarif21_startincome'),
            'tarif21_endincome' => $request->input('tarif21_endincome'),
        ];

        DB::beginTransaction();
        try {
            $tarif21 = Tarif21Model::create($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tarif PPh 21 berhasil disimpan',
                'data' => $tarif21
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($tarif21Id, Request $request)
    {
        $this->validate($request, [
            'tarif21_year' => 'required|numeric',
            'tarif21_description' => 'required',
            'tarif21_rate' => 'required|numeric',
            'tarif21_startincome' => 'required|numeric',
            'tarif21_endincome' => 'required|numeric',
        ], [
            'tarif21_year.required' => 'Tahun wajib diisi!',
            'tarif21_description.required' => 'Deskripsi wajib diisi!',
            'tarif21_rate.numeric' => 'Rate harus angka!',
            'tarif21_startincome.numeric' => 'Batas awal pendapatan harus angka!',
            'tarif21_endincome.numeric' => 'Batas akhir pendapatan harus angka!',
        ]);

        $tarif21 = Tarif21Model::where([
            'tarif21_id' => $tarif21Id, 
            'tarif21_active' => 1,
        ])->first();
        if(!$tarif21) {
            return response()->json([
                'success' => false,
                'message' => 'Tarif PPh 21 tidak ditemukan!'
            ]);
        }

        $save_data = [
            'tarif21_year' => $request->input('tarif21_year'),
            'tarif21_description' => $request->input('tarif21_description'),
            'tarif21_rate' => $request->input('tarif21_rate'),
            'tarif21_startincome' => $request->input('tarif21_startincome'),
            'tarif21_endincome' => $request->input('tarif21_endincome'),
        ];

        DB::beginTransaction();
        try {
            
            $tarif21->update($save_data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tarif PPh 21 berhasil disimpan',
                'data' => $tarif21
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($tarif21Id, Request $request)
    {
        $tarif21 = Tarif21Model::where([
            'tarif21_id' => $tarif21Id, 
            'tarif21_active' => 1,
        ])->first();
        if(!$tarif21) {
            return response()->json([
                'success' => false,
                'message' => 'Tarif PPh 21 tidak ditemukan!'
            ]); 
        }
        
        // dd($karyawan_tunjangans);
        DB::beginTransaction();
        try {
            $tarif21->update(['tarif21_active' => 0]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tarif PPh 21 berhasil dihapus',
                'data' => $tarif21
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