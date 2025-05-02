<?php
namespace App\Http\Controllers\Karyawan\Dashboard;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PermissionModel;
use App\Model\Master\SubscriptionModel;
use App\Model\Master\WajibPajakUserModel;
use App\Model\MasterRelation\MrSubscriptionPermissionModel;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Beranda',
            'content' => 'karyawan.dashboard.index',
            'karyawan' => KaryawanModel::where([
                'karyawan_id' => session()->get('karyawan_data')['karyawan_id']
            ])->first(),
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('karyawan.index', $data);
        }
    }

    public function data(Request $request)
    {
        $data = null;
        if(session()->get('wajibpajak_current')) {
            $wp_id = session()->get('wajibpajak_current')['wajibpajak_id'];
            $data = KaryawanModel::select(
                // (SELECT json_agg(ptable) FROM (SELECT tr_penerimaan.kode_faktur, penerimaan_total_terbayar, bayarsupplier_nominal FROM tr_penerimaan JOIN tr_bayar_supplier ON tr_bayar_supplier.kode_faktur = tr_penerimaan.kode_faktur AND tr_kasbankkeluar_id = kasbankkeluar_id AND penerimaan_aktif='y') as ptable) as nofaktur_json
                DB::raw("(SELECT COUNT(karyawan_id) FROM ms_karyawan 
                JOIN tr_karyawan_masakerja ON tr_karyawan_masakerja.ms_karyawan_id = karyawan_id AND karyawanmasakerja_active = '1' 
                WHERE karyawan_active='1' AND karyawanmasakerja_contract_end IS NULL AND karyawanmasakerja_status = 'TETAP' AND ms_karyawan.ms_wajibpajak_id = {$wp_id} ) as total_karyawan_tetap")
                , DB::raw("(SELECT COUNT(karyawan_id) FROM ms_karyawan 
                JOIN tr_karyawan_masakerja ON tr_karyawan_masakerja.ms_karyawan_id = karyawan_id AND karyawanmasakerja_active = '1' 
                WHERE karyawan_active='1' AND karyawanmasakerja_contract_end IS NULL AND karyawanmasakerja_status = 'KONTRAK' AND ms_karyawan.ms_wajibpajak_id = {$wp_id} ) as total_karyawan_kontrak")
                , DB::raw("(SELECT COUNT(karyawan_id) FROM ms_karyawan 
                JOIN tr_karyawan_masakerja ON tr_karyawan_masakerja.ms_karyawan_id = karyawan_id AND karyawanmasakerja_active = '1' 
                WHERE karyawan_active='1' AND karyawanmasakerja_contract_end IS NULL AND karyawanmasakerja_status = 'NONKARYAWAN' AND ms_karyawan.ms_wajibpajak_id = {$wp_id} ) as total_karyawan_bukan")
                , DB::raw("(SELECT SUM(karyawankalkulasi_salary) FROM ms_karyawan 
                JOIN tr_karyawan_masakerja ON tr_karyawan_masakerja.ms_karyawan_id = karyawan_id AND karyawanmasakerja_active = '1' 
                JOIN ms_karyawan_kalkulasi ON karyawan_id = ms_karyawan_kalkulasi.ms_karyawan_id 
                WHERE karyawan_active='1' AND karyawanmasakerja_contract_end IS NULL AND LPAD(karyawankalkulasi_month::varchar, 2, '0') = to_char(NOW(), 'MM') AND karyawankalkulasi_year::varchar = to_char(NOW(), 'YYYY') AND ms_karyawan.ms_wajibpajak_id = {$wp_id} 
                AND karyawankalkulasi_lock = '1'
                ) as total_salary_bulan_ini")
                , DB::raw("(SELECT SUM(karyawankalkulasi_pph21) FROM ms_karyawan 
                JOIN tr_karyawan_masakerja ON tr_karyawan_masakerja.ms_karyawan_id = karyawan_id AND karyawanmasakerja_active = '1' 
                JOIN ms_karyawan_kalkulasi ON karyawan_id = ms_karyawan_kalkulasi.ms_karyawan_id 
                WHERE karyawan_active='1' AND karyawanmasakerja_contract_end IS NULL AND LPAD(karyawankalkulasi_month::varchar, 2, '0') = to_char(NOW(), 'MM') AND karyawankalkulasi_year::varchar = to_char(NOW(), 'YYYY') AND ms_karyawan.ms_wajibpajak_id = {$wp_id} 
                AND karyawankalkulasi_lock = '1'
                ) as total_pph21_bulan_ini")
                , DB::raw("(SELECT (SELECT json_agg(ptable) FROM (
                    SELECT to_char(m, 'Month') as mn, (
                                    SELECT SUM(karyawankalkulasi_salary) 
                                    FROM ms_karyawan 
                                    JOIN tr_karyawan_masakerja ON tr_karyawan_masakerja.ms_karyawan_id = karyawan_id AND karyawanmasakerja_active = '1' 
                                    LEFT JOIN ms_karyawan_kalkulasi ON karyawan_id = ms_karyawan_kalkulasi.ms_karyawan_id AND karyawankalkulasi_lock = '1'
                                    WHERE karyawan_active='1' AND karyawanmasakerja_contract_end IS NULL 
                                    AND LPAD(karyawankalkulasi_month::varchar, 2, '0') = to_char(m, 'MM') 
                                    AND karyawankalkulasi_year::varchar = to_char(NOW(), 'YYYY') AND ms_karyawan.ms_wajibpajak_id = {$wp_id} 
                                ) as karyawankalkulasi_salary 
                                from generate_series(
                                    '2023-01-01'::date, '2023-12-31', '1 month'
                                ) s(m) ) 
                                as ptable)) as pengeluaran_tahunberjalan_json")
                , DB::raw("(SELECT (SELECT json_agg(ptable) FROM (
                    SELECT to_char(m, 'Month') as mn, (
                                    SELECT SUM(karyawankalkulasi_pph21) 
                                    FROM ms_karyawan 
                                    JOIN tr_karyawan_masakerja ON tr_karyawan_masakerja.ms_karyawan_id = karyawan_id AND karyawanmasakerja_active = '1' 
                                    LEFT JOIN ms_karyawan_kalkulasi ON karyawan_id = ms_karyawan_kalkulasi.ms_karyawan_id AND karyawankalkulasi_lock = '1'
                                    WHERE karyawan_active='1' AND karyawanmasakerja_contract_end IS NULL 
                                    AND LPAD(karyawankalkulasi_month::varchar, 2, '0') = to_char(m, 'MM')
                                    AND karyawankalkulasi_year::varchar = to_char(NOW(), 'YYYY') AND ms_karyawan.ms_wajibpajak_id = {$wp_id} 
                                ) as karyawan_pph 
                                from generate_series(
                                    '2023-01-01'::date, '2023-12-31', '1 month'
                                ) s(m) ) 
                                as ptable)) as pengeluaran_pphberjalan_json")
            )->first();
        }
        // dd($data);


        return response()->json([
            'success' => true,
            'message' => 'Data Dashboard',
            'data' => $data
        ]);
    }
}