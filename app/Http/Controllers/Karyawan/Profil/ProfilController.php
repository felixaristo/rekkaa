<?php
namespace App\Http\Controllers\Karyawan\Profil;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanInfoFieldModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfilController extends Controller
{
    public function index(Request $request)
    {
        $karyawan = $request->get('karyawan');
        
        $tunjangan_karyawan = null;
        if($karyawan->st_tunjangan_id) {
            $tunjangan_karyawan = SettingTunjanganKaryawanModel::whereIn('sttunjangankaryawan_id', explode(',', $karyawan->st_tunjangan_id))
            ->where(['sttunjangankaryawan_active' => 1])->get();
        }
        
        $setting = DB::select(DB::raw("SELECT (SELECT json_agg(ptable) 
        FROM (
            SELECT st_tunjangan_karyawan.*
            FROM st_tunjangan_karyawan
            WHERE ms_wajibpajak_id = {$karyawan->ms_wajibpajak_id}
            AND sttunjangankaryawan_active = '1'
            ORDER BY sttunjangankaryawan_id ASC
        )
        as ptable) as setting_tunjangankaryawan_json
        , (SELECT row_to_json(ptable) 
        FROM (
            SELECT st_bpjs_karyawan.*
            FROM st_bpjs_karyawan
            WHERE ms_wajibpajak_id = {$karyawan->ms_wajibpajak_id}
            AND stbpjskaryawan_active = '1'
            ORDER BY stbpjskaryawan_id ASC
        )
        as ptable) as setting_bpjspegawai_json
        "));

        $karyawan_infofield = KaryawanInfoFieldModel::where(['ms_wajibpajak_id' => $karyawan->ms_wajibpajak_id, 'ms_user_id' => $karyawan->ms_user_id])->first();
        $stbpjskaryawan_data = ($setting[0]->setting_bpjspegawai_json) ? json_decode($setting[0]->setting_bpjspegawai_json) : null;
        $data = [
            'title' => 'Profil',
            'content' => 'karyawan.profil.index',
            'karyawan' => $karyawan,
            'tunjangan_karyawan' => $tunjangan_karyawan,
            'sttunjangankaryawan_data' => ($setting[0]->setting_tunjangankaryawan_json) ? json_decode($setting[0]->setting_tunjangankaryawan_json) : null,
            'stbpjskaryawan_data' => ($stbpjskaryawan_data) ? json_decode($stbpjskaryawan_data->stbpjskaryawan_value) : null,
            'karyawan_infofield' => $karyawan_infofield,
        ];
        // dd($karyawan->manager);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('karyawan.index', $data);
        }
    }
}