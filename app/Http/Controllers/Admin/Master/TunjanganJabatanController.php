<?php
namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\TunjanganJabatanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TunjanganJabatanController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Tunjangan Jabatan',
            'content' => 'admin.master.tunjangan-jabatan.index',
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
            'tunjanganjabatan_active' => 1,
        ];
        $list = TunjanganJabatanModel::select('ms_tunjangan_jabatan.*')
        ->where($where)
        ->take($limit)->skip($offset)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = TunjanganJabatanModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {   
        // dd($request->all());
        $this->validate($request, [
            'tunjanganjabatan_year' => 'required|numeric',
            'tunjanganjabatan_rate' => 'required|numeric',
            'tunjanganjabatan_maximum_allowance' => 'required|numeric',
        ], [
            'tunjanganjabatan_year.required' => 'Tahun wajib diisi!',
            'tunjanganjabatan_rate.numeric' => 'Rate harus angka!',
            'tunjanganjabatan_maximum_allowance.numeric' => 'Maksimal nilai harus angka!',
        ]);

        $save_data = [
            'tunjanganjabatan_year' => $request->input('tunjanganjabatan_year'),
            'tunjanganjabatan_rate' => $request->input('tunjanganjabatan_rate'),
            'tunjanganjabatan_maximum_allowance' => $request->input('tunjanganjabatan_maximum_allowance'),
        ];

        DB::beginTransaction();
        try {
            $tunjanganjabatan = TunjanganJabatanModel::create($save_data);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tunjangan Jabatan berhasil disimpan',
                'data' => $tunjanganjabatan
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($tunjanganjabatanId, Request $request)
    {
        $this->validate($request, [
            'tunjanganjabatan_year' => 'required|numeric',
            'tunjanganjabatan_rate' => 'required|numeric',
            'tunjanganjabatan_maximum_allowance' => 'required|numeric',
        ], [
            'tunjanganjabatan_year.required' => 'Tahun wajib diisi!',
            'tunjanganjabatan_rate.numeric' => 'Rate harus angka!',
            'tunjanganjabatan_maximum_allowance.numeric' => 'Maksimal nilai harus angka!',
        ]);

        $tunjanganjabatan = TunjanganJabatanModel::where([
            'tunjanganjabatan_id' => $tunjanganjabatanId, 
            'tunjanganjabatan_active' => 1,
        ])->first();
        if(!$tunjanganjabatan) {
            return response()->json([
                'success' => false,
                'message' => 'Tunjangan Jabatan tidak ditemukan!'
            ]);
        }

        $save_data = [
            'tunjanganjabatan_year' => $request->input('tunjanganjabatan_year'),
            'tunjanganjabatan_rate' => $request->input('tunjanganjabatan_rate'),
            'tunjanganjabatan_maximum_allowance' => $request->input('tunjanganjabatan_maximum_allowance'),
        ];

        DB::beginTransaction();
        try {
            
            $tunjanganjabatan->update($save_data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tunjangan Jabatan berhasil disimpan',
                'data' => $tunjanganjabatan
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($tunjanganjabatanId, Request $request)
    {
        $tunjanganjabatan = TunjanganJabatanModel::where([
            'tunjanganjabatan_id' => $tunjanganjabatanId, 
            'tunjanganjabatan_active' => 1,
        ])->first();
        if(!$tunjanganjabatan) {
            return response()->json([
                'success' => false,
                'message' => 'Tunjangan Jabatan tidak ditemukan!'
            ]); 
        }
        
        // dd($karyawan_tunjangans);
        DB::beginTransaction();
        try {
            $tunjanganjabatan->update(['tunjanganjabatan_active' => 0]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Tunjangan Jabatan berhasil dihapus',
                'data' => $tunjanganjabatan
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