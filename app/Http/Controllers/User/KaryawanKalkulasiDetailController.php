<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanKalkulasiModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PtkpModel;
use App\Model\Master\Tarif21Model;
use App\Model\Master\TarifNonNpwpModel;
use App\Model\Master\TunjanganJabatanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaryawanKalkulasiDetailController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($karyawanmasakerjaId, $kalkulasiId, Request $request)
    {
        $kalkulasi = KaryawanKalkulasiModel::select("ms_karyawan_kalkulasi.*"
        , 'karyawan_nik', 'karyawan_npwp', 'karyawan_name', 'karyawan_email', 'karyawanmasakerja_status', 'karyawanmasakerja_contract_begin', 'karyawanmasakerja_contract_end','karyawanmasakerja_calculation_method','karyawanmasakerja_salary', 'objekpajak_id', 'tr_karyawan_masakerja.ms_ptkp_id', 'ms_karyawan.ms_wajibpajak_id', 'karyawanmasakerja_tunjangan', 'karyawanmasakerja_bpjs', 'karyawanmasakerja_end_reason', 'karyawanmasakerja_end_type'
        , 'ptkp_description')
        ->join('tr_karyawan_masakerja', 'karyawanmasakerja_id', '=', 'ms_karyawan_kalkulasi.ms_karyawanmasakerja_id')
        ->join('ms_karyawan', 'karyawan_id', '=', 'tr_karyawan_masakerja.ms_karyawan_id')
        ->join('ms_ptkp', 'ptkp_id', '=', 'tr_karyawan_masakerja.ms_ptkp_id')
        ->leftJoin('ms_objek_pajak', 'objekpajak_code', '=', 'tr_karyawan_masakerja.ms_objekpajak_code')
        ->where(['karyawankalkulasi_id' => $kalkulasiId, 'karyawanmasakerja_id' => $karyawanmasakerjaId, 'ms_karyawan.ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],])->first();
        // dd($kalkulasi);
        if(!$kalkulasi) {
            abort(404);
        }
        
        $tunjangan_jabatan = TunjanganJabatanModel::where(['tunjanganjabatan_active' => 1])->first();
        $ptkp = PtkpModel::where(['ptkp_id' => $kalkulasi->ms_ptkp_id])->first();
        $data = [
            'title' => 'Detail Kalkulasi Pajak',
            'content' => 'user.master.karyawan.kalkulasi-detail.index',
            'kalkulasi' => $kalkulasi,
            'ptkp' => $ptkp,
            'bpjsrate' => bpjsRate(),
            'tarif21' => Tarif21Model::where(['tarif21_active' => 1])->get(),
            'tarif21_nonnpwp' => TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first(),
            'tunjangan_jabatan' => $tunjangan_jabatan,
        ];
        // dd($data['karyawan']);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function update($karyawanmasakerjaId, $kalkulasiId, Request $request)
    {
        $salary = $request->input('karyawankalkulasi_salary');
        $tunjangan = $request->input('tunjangan');

        $this->validate($request, [
            'karyawankalkulasi_salary' => 'required',
            'tunjangan' => 'required',
        ]);

        // echo(json_encode($tunjangan));
        // exit;
        $kalkulasi = KaryawanKalkulasiModel::select("ms_karyawan_kalkulasi.*")
        ->join('ms_karyawan', 'karyawan_id', '=', 'ms_karyawan_id')
        ->where([
            'karyawankalkulasi_id' => $kalkulasiId, 
            'ms_karyawanmasakerja_id' => $karyawanmasakerjaId,
            'ms_karyawan.ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
            'karyawankalkulasi_active' => 1
        ])->first();

        if(!$kalkulasi) {
            abort(404);
        }
        KaryawanKalkulasiModel::where(['karyawankalkulasi_id' => $kalkulasiId])->update([
            'karyawankalkulasi_salary' => $salary,
            'karyawankalkulasi_tunjangan' => json_encode($tunjangan),
        ]);
        // dd($kalkulasi);
        $data_generate_pph21 = generatePPH2Karyawan($karyawanmasakerjaId, $kalkulasiId);
        // dd($data_generate_pph21);
        // exit;
        DB::beginTransaction();
        try {

            $data_generate_pph21 = generatePPH2Karyawan($karyawanmasakerjaId, $kalkulasiId);
            KaryawanKalkulasiModel::where(['karyawankalkulasi_id' => $kalkulasiId])
            ->update($data_generate_pph21);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Update detail kalkulasi berhasil',
            ];

        } catch(Error $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}