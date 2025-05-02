<?php
namespace App\Http\Controllers\User\Master;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanInfoFieldModel;
use App\Model\Master\KaryawanKalkulasiModel;
use App\Model\Master\KaryawanMasakerjaModel;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingLeaveModel;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use App\Model\Setting\SettingPotonganKaryawanDetailModel;
use App\Model\Setting\SettingPotonganKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanDetailModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class NonKaryawanNonaktifController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Non Karyawan Non Aktif',
            'content' => 'user.master.non-karyawan.nonaktif.index',
        ];

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }
    
    public function profile($karyawanId, Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $karyawan = KaryawanModel::
        select("*"
        , DB::raw("(SELECT json_agg(ttable) 
        FROM (
            SELECT std.st_tunjangankaryawan_id, stk.sttunjangankaryawan_name
            FROM st_tunjangan_karyawan_detail as std
            JOIN st_tunjangan_karyawan as stk ON std.st_tunjangankaryawan_id = stk.sttunjangankaryawan_id
            WHERE std.ms_karyawan_id = ms_karyawan.karyawan_id
                AND std.sttunjangankaryawandet_active = '1'
                AND stk.sttunjangankaryawan_active = '1'
        ) as ttable) as tunjangan_json")
        , DB::raw("(SELECT json_agg(ttable) 
        FROM (
            SELECT spd.st_potongankaryawan_id, spk.stpotongankaryawan_name
            FROM st_potongan_karyawan_detail as spd
            JOIN st_potongan_karyawan as spk ON spd.st_potongankaryawan_id = spk.stpotongankaryawan_id
            WHERE spd.ms_karyawan_id = ms_karyawan.karyawan_id
                AND spd.stpotongankaryawandet_active = '1'
                AND spk.stpotongankaryawan_active = '1'
        ) as ttable) as potongan_json"))
        ->with(['bank', 'penggajian', 'ptkp', 'manager', 'divisi', 'jabatan', 'attendance', 'country'])
        ->where([
            'karyawan_id' => $karyawanId, 
            'karyawan_active' => 0,
            'karyawan_status' => 'NONKARYAWAN',
            // 'karyawan_contract_end' => null,
            'ms_wajibpajak_id' => $wajibpajak_id,
            // 'ms_user_id' => $user_id,
        ])->first();
        if(!$karyawan) {
            abort(404);
        }

        $karyawan_infofield = KaryawanInfoFieldModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_user_id' => $user_id])->first();
        // $stbpjskaryawan_data = ($setting[0]->setting_bpjspegawai_json) ? json_decode($setting[0]->setting_bpjspegawai_json) : null;
        $data = [
            'title' => 'Profile Karyawan',
            'content' => 'user.master.non-karyawan.profile',
            'karyawan' => $karyawan,
            'karyawan_infofield' => $karyawan_infofield,
            // 'sttunjangankaryawan_data' => ($setting[0]->setting_tunjangankaryawan_json) ? json_decode($setting[0]->setting_tunjangankaryawan_json) : null,
            // 'stbpjskaryawan_data' => ($stbpjskaryawan_data) ? json_decode($stbpjskaryawan_data->stbpjskaryawan_value) : null,
        ];
        // return response()->json($data);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
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
        $search = (isset($request->input('search')['value']) && $request->input('search')['value']) ? $request->input('search')['value'] : null;
        
        $where = [
            'karyawan_active' => 0,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];
        $list = KaryawanModel::select('karyawan_id', 'karyawan_name', 'karyawan_nik', 'karyawan_npwp', 'karyawan_address', 'karyawan_phone', 'karyawan_status')
        ->where($where)
        ->whereNotNull('karyawan_contract_end')
        ->whereIn('karyawan_status', ['NONKARYAWAN'])
        ->where(function ($q) use ($search){
            return $q->where(DB::raw('LOWER(karyawan_nik)'), 'like', '%'.strtolower($search).'%')
            ->orWhere(DB::raw('LOWER(karyawan_npwp)'), 'like', '%'.strtolower($search).'%')
            ->orWhere(DB::raw('LOWER(karyawan_name)'), 'like', '%'.strtolower($search).'%');
        })
        ->take($limit)->skip($offset)->orderBy('karyawan_id', 'desc')->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = KaryawanModel::where($where)
        ->whereNotNull('karyawan_contract_end')
        ->where(function ($q) use ($search){
            return $q->where(DB::raw('LOWER(karyawan_nik)'), 'like', '%'.strtolower($search).'%')
            ->orWhere(DB::raw('LOWER(karyawan_npwp)'), 'like', '%'.strtolower($search).'%')
            ->orWhere(DB::raw('LOWER(karyawan_name)'), 'like', '%'.strtolower($search).'%');
        })->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }
}