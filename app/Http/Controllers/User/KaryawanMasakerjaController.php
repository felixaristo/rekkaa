<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanKalkulasiModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PtkpModel;
use App\Model\Master\TunjanganJabatanModel;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaryawanMasakerjaController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($karyawanId, Request $request)
    {
        // $karyawan = KaryawanModel::select('ms_karyawan.*', 'ptkp_description')
        // ->where(['karyawan_id' => $karyawanId, 'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],])
        // ->join('ms_ptkp', 'ptkp_id', '=', 'ms_ptkp_id')
        // ->first();
        // if(!$karyawan) {
        //     abort(404);
        // }
        // $data = [
        //     'title' => 'Kalkulasi Pajak Karyawan',
        //     'content' => 'user.master.karyawan.kalkulasi.index',
        //     'karyawan' => $karyawan
        // ];
        // // dd($data['karyawan']);
        // if($request->ajax()) {
        //     return view($data['content'], $data)->render();
        // } else {
        //     return view('user.index', $data);
        // }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable($karyawanId, Request $request)
    {
        // $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        // $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
        // $tahun = $request->input('tahun');
        // $this->generate($karyawanId, $request);
        // $where = [
        //     'ms_karyawan_id' => $karyawanId,
        //     'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        //     'karyawankalkulasi_year' => $tahun,
        // ];
        // $list = KaryawanKalkulasiModel::select('ms_karyawan_kalkulasi.*')
        // ->join("ms_karyawan", "karyawan_id", "=", "ms_karyawan_id")
        // ->where($where)
        // // ->when($search, function($q, $search) {
        // //     return $q->where(DB::raw('LOWER(karyawan_nik)'), 'like', '%'.strtolower($search).'%')
        // //     ->orWhere(DB::raw('LOWER(karyawan_npwp)'), 'like', '%'.strtolower($search).'%')
        // //     ->orWhere(DB::raw('LOWER(karyawan_name)'), 'like', '%'.strtolower($search).'%');
        // // })
        // ->take($limit)->skip($offset)->orderBy('karyawankalkulasi_month', 'asc')->get();
        // $data['draw'] = $request->input('draw');
		// $data['recordsTotal'] = $data['recordsTotal'];
		// $data['recordsFiltered'] = $data['recordsTotal'];
        // $data['data'] = $list;
        
        // return response()->json($data);
    }
}