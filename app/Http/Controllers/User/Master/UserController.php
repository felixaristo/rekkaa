<?php
namespace App\Http\Controllers\User\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanKalkulasiModel;
use App\Model\Master\KaryawanMasakerjaModel;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use App\User;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
     /**
     * Display a select of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        $q = $request->get('q');
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $where = [
            'user_active' => 1,
            // 'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];

        $data = User::select('user_id', 'user_name', 'user_email')
        // ->leftJoin('ms_karyawan', 'ms_karyawan.ms_user_id', '=', 'user_id')
        ->when($q, function ($query, $q) {
            return $query->where('user_email', 'ilike', '%'.$q.'%');
        })
        ->where($where)
        ->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    public function lockKalkulasi(Request $request)
    {
        // dd($request->all());
        $lock_karyawan = $request->input('lock_karyawan_id');
        $lock_metode = $request->input('lock_metode_id');
        $lock_status = $request->input('lock_status_id');
        $lock_periode = $request->input('lock_periode_format');

        $bulan = null;
        $tahun = null;
        if($lock_periode) {
            $bulan = Carbon::createFromFormat('d-m-Y', '01-'.$lock_periode)->format('n');
            $tahun = Carbon::createFromFormat('d-m-Y', '01-'.$lock_periode)->format('Y');
        }
        // dd($bulan);

        $where = [
            'karyawankalkulasi_active' => 1,
            'karyawankalkulasi_lock' => false,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];
        // find kalkulasi
        $kalkulasi = KaryawanKalkulasiModel::select('ms_karyawan_kalkulasi.*', 'karyawan_name')
        ->join('ms_karyawan', 'ms_karyawan_id', '=', 'karyawan_id')
        ->when($bulan, function($q, $bulan) {
            return $q->where('karyawankalkulasi_month', '=', $bulan);
        })
        ->when($tahun, function($q, $tahun) {
            return $q->where('karyawankalkulasi_year', '=', $tahun);
        })
        ->when($lock_karyawan, function($q, $lock_karyawan) {
            // if(is_array($lock_karyawan))
                return $q->whereIn('ms_karyawan_id', $lock_karyawan);
            // return true;
        })
        ->when($lock_metode, function($q, $lock_metode) {
            // if(is_array($lock_metode))
                return $q->whereIn('karyawankalkulasi_method', $lock_metode);
            // return true;
        })
        ->when($lock_status, function($q, $lock_status) {
            // if(is_array($lock_status))
                return $q->whereIn('karyawan_status', $lock_status);
            // return true;
        })
        ->where($where)->get();
        // dd(count($kalkulasi));
        if(count($kalkulasi) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Kalkulasi tidak ditemukan',
            ]);
        }
        $kalkulasi_ids = [];
        foreach($kalkulasi as $kal) {
            array_push($kalkulasi_ids, $kal->karyawankalkulasi_id);
        }
        
        DB::beginTransaction();
        try {
            KaryawanKalkulasiModel::
            whereIn('karyawankalkulasi_id', $kalkulasi_ids)
            ->update([
                'karyawankalkulasi_lock' => true,
                'karyawankalkulasi_lock_at' => date('Y-m-d H:i:s'),
                // 'karyawankalkulasi_method' => $kalkulasi[0]->karyawan_calculation_method
            ]);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kalkulasi berhasil di lock',
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