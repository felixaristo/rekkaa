<?php

use App\Model\Master\KaryawanMasakerjaModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function generatePPH2Karyawan($karyawanmasakerjaId, $kalkulasiId = 0, $tahun = 0)
{
    // dd($karyawanmasakerjaId);
    $where = [
        'karyawanmasakerja_id' => $karyawanmasakerjaId,
        'ms_karyawan.ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
    ];
    if($kalkulasiId) {
        $where['karyawankalkulasi_id'] = $kalkulasiId;
    }
    // find kalkulasi
    $kalkulasi = KaryawanMasakerjaModel::select('tr_karyawan_masakerja.*', 'tr_karyawan_masakerja.ms_karyawan_id as ms_karyawan_id_masakerja', 'tr_karyawan_masakerja.ms_objekpajak_code as ms_objekpajak_code_masakerja', 'tr_karyawan_masakerja.ms_ptkp_id as ms_ptkp_id_masakerja'
    , 'ms_karyawan_kalkulasi.*', 'ms_karyawan_kalkulasi.ms_ptkp_id as ms_ptkp_id_kalkulasi', 'ms_karyawan_kalkulasi.ms_objekpajak_code as ms_objekpajak_code_kalkulasi'
    , DB::raw("(SELECT row_to_json(tjtable) FROM 
        (
            SELECT * FROM ms_tunjangan_jabatan WHERE tunjanganjabatan_active = '1'
        ) tjtable
    ) as tunjanganjabatan_json")
    , DB::raw("(SELECT row_to_json(pttable) FROM 
        (
            SELECT * FROM ms_ptkp WHERE ptkp_id = tr_karyawan_masakerja.ms_ptkp_id
        ) pttable
    ) as ptkp_json"))
    ->join('ms_karyawan', 'tr_karyawan_masakerja.ms_karyawan_id', '=', 'karyawan_id')
    ->leftJoin("ms_karyawan_kalkulasi", function($join)  use ($tahun)
    {
        $join->on('ms_karyawanmasakerja_id', '=', 'tr_karyawan_masakerja.karyawanmasakerja_id');
        $join->on('karyawan_id', '=', 'ms_karyawan_kalkulasi.ms_karyawan_id');
        if($tahun) {
            $join->on('karyawankalkulasi_year', '=', DB::raw("'".intval($tahun)."'"));
        }
    })
    ->where($where)->get();
    if(!$kalkulasi) {
        return null;
    }
    // dd($kalkulasi);

    // bpjs rate
    $bpjsrate = bpjsRate();

    if($kalkulasiId) {
        if($kalkulasi[0]->karyawankalkulasi_status == 'NONKARYAWAN') {
            return null;
        }

        // ptkp
        $ptkp = ($kalkulasi[0]->ptkp_json) ? json_decode($kalkulasi[0]->ptkp_json) : null;
        // tunjangan jabatan
        $tunjangan_jabatan = ($kalkulasi[0]->karyawankalkulasi_tunjanganjabatan) ? json_decode($kalkulasi[0]->karyawankalkulasi_tunjanganjabatan) : null;

        // perhitungan penghasilan dari setting bpjs
        $karyawan_bpjs = bpjsKaryawan($kalkulasi[0]->karyawankalkulasi_bpjs, $kalkulasi[0]->karyawankalkulasi_salary);
        // perhitungan penghasilan dari setting tunjangan
        $tunjangan_lain_decode = $kalkulasi[0]->karyawankalkulasi_tunjangan;
        $nominal_tunjangan_lain = 0;
        if($tunjangan_lain_decode) {
            foreach($tunjangan_lain_decode as $key => $val) {
                $nominal_tunjangan_lain += $val;
            }
        }

        $perhitungan = perhitunganTotalPPH21($bpjsrate, $ptkp, $karyawan_bpjs, $kalkulasi[0]->karyawankalkulasi_salary, 
        [
            'tunjangan_jabatan' => $tunjangan_jabatan,
            'tunjangan_nominal' => $nominal_tunjangan_lain
        ]
        , $kalkulasi[0]->karyawankalkulasi_method);

        $bruto = $perhitungan['total_bruto_perbulan'];
        $pph21 = $perhitungan['total_pph_terutang_perbulan'];
        $data_pph21 = json_encode($perhitungan);

        $karyawanId = $kalkulasi[0]->ms_karyawan_id_masakerja;
        $temp_data = [
            'ms_karyawanmasakerja_id' => $kalkulasi[0]->karyawanmasakerja_id,
            'ms_karyawan_id' => $karyawanId,
            'karyawankalkulasi_month' => $kalkulasi[0]->karyawankalkulasi_month,
            'karyawankalkulasi_year' => $kalkulasi[0]->karyawankalkulasi_year,
            'karyawankalkulasi_salary' => $kalkulasi[0]->karyawankalkulasi_salary,
            'karyawankalkulasi_pph21' => $pph21,
            'karyawankalkulasi_bruto' => $bruto,
            'karyawankalkulasi_method' => $kalkulasi[0]->karyawankalkulasi_method,
            'karyawankalkulasi_status' => $kalkulasi[0]->karyawankalkulasi_status,
            'karyawankalkulasi_position' => $kalkulasi[0]->karyawankalkulasi_position,
            'karyawankalkulasi_bpjs' => json_encode($kalkulasi[0]->karyawankalkulasi_bpjs),
            'karyawankalkulasi_tunjangan' => json_encode($kalkulasi[0]->karyawankalkulasi_tunjangan),
            'karyawankalkulasi_tunjanganjabatan' => json_encode($tunjangan_jabatan),
            'ms_objekpajak_code' => $kalkulasi[0]->ms_objekpajak_code_kalkulasi,
            'ms_ptkp_id' => $kalkulasi[0]->ms_ptkp_id_kalkulasi,
            'karyawankalkulasi_data' => $data_pph21,
        ];

    } else {
    
    // multiple generate
        if($kalkulasi[0]->karyawanmasakerja_status == 'NONKARYAWAN') {
            return null;
        }

        $bulan_masuk = Carbon::parse($kalkulasi[0]->karyawanmasakerja_contract_begin)->format('m');
        $tahun_masuk = Carbon::parse($kalkulasi[0]->karyawanmasakerja_contract_begin)->format('Y');
        // return $tahun_masuk.' - '.$tahun;

        // ptkp
        $ptkp = ($kalkulasi[0]->ptkp_json) ? json_decode($kalkulasi[0]->ptkp_json) : null;
        // tunjangan jabatan
        $tunjangan_jabatan = ($kalkulasi[0]->tunjanganjabatan_json) ? json_decode($kalkulasi[0]->tunjanganjabatan_json) : null;

        // perhitungan penghasilan dari setting bpjs
        $karyawan_bpjs = bpjsKaryawan($kalkulasi[0]->karyawanmasakerja_bpjs, $kalkulasi[0]->karyawanmasakerja_salary);
        // dd($karyawan_bpjs);
        // perhitungan penghasilan dari setting tunjangan
        $tunjangan_lain_decode = $kalkulasi[0]->karyawanmasakerja_tunjangan;
        $nominal_tunjangan_lain = 0;
        if($tunjangan_lain_decode) {
            foreach($tunjangan_lain_decode as $key => $val) {
                $nominal_tunjangan_lain += $val;
            }
        }

        $perhitungan = perhitunganTotalPPH21($bpjsrate, $ptkp, $karyawan_bpjs, $kalkulasi[0]->karyawanmasakerja_salary, 
        [
            'tunjangan_jabatan' => $tunjangan_jabatan,
            'tunjangan_nominal' => $nominal_tunjangan_lain
        ]
        , $kalkulasi[0]->karyawanmasakerja_calculation_method);

        $karyawanId = $kalkulasi[0]->ms_karyawan_id_masakerja;
        $temp_data = [];
            
        if($tahun == 0) {
            for($th = $tahun_masuk; $th<=date('Y'); $th++) {
                for($i=1; $i<=12; $i++) {
                    $salary = 0;
                    $pph21 = 0;
                    $bruto = 0;
                    $data_pph21 = null;
                    $bulan = (strlen($i) > 1) ? strval($i) : '0'.$i;
                    // $tahun = $tahun_masuk + $i;
                    $periode_masuk = $tahun_masuk.'-'.$bulan_masuk.'-01';
                    $iperiode = $th.'-'.$bulan.'-01';
                    if($iperiode >= $periode_masuk) {
                        // dd($iperiode.' - '.$periode_masuk);
                        $salary = $kalkulasi[0]->karyawanmasakerja_salary;
                        $bruto = $perhitungan['total_bruto_perbulan'];
                        $pph21 = $perhitungan['total_pph_terutang_perbulan'];
                        $data_pph21 = json_encode($perhitungan);
                        // dd($salary);
                    }
                    array_push($temp_data, [
                        'ms_karyawanmasakerja_id' => $kalkulasi[0]->karyawanmasakerja_id,
                        'ms_karyawan_id' => $karyawanId,
                        'karyawankalkulasi_month' => $bulan,
                        'karyawankalkulasi_year' => $th,
                        'karyawankalkulasi_salary' => $salary,
                        'karyawankalkulasi_pph21' => $pph21,
                        'karyawankalkulasi_bruto' => $bruto,
                        'karyawankalkulasi_method' => $kalkulasi[0]->karyawanmasakerja_calculation_method,
                        'karyawankalkulasi_status' => $kalkulasi[0]->karyawanmasakerja_status,
                        'karyawankalkulasi_position' => $kalkulasi[0]->karyawanmasakerja_position,
                        'karyawankalkulasi_bpjs' => json_encode($kalkulasi[0]->karyawanmasakerja_bpjs),
                        'karyawankalkulasi_tunjangan' => json_encode($kalkulasi[0]->karyawanmasakerja_tunjangan),
                        'karyawankalkulasi_tunjanganjabatan' => json_encode($tunjangan_jabatan),
                        'ms_objekpajak_code' => $kalkulasi[0]->ms_objekpajak_code_masakerja,
                        'ms_ptkp_id' => $kalkulasi[0]->ms_ptkp_id_masakerja,
                        'karyawankalkulasi_data' => $data_pph21,
                    ]);
                }
            }

            // dd($temp_data);
        } else {
            for($i=1; $i<=12; $i++) {
                $salary = 0;
                $pph21 = 0;
                $bruto = 0;
                $data_pph21 = null;
                $bulan = (strlen($i) > 1) ? strval($i) : '0'.$i;
                // $tahun = $tahun_masuk + $i;
                $periode_masuk = $tahun_masuk.'-'.$bulan_masuk.'-01';
                $iperiode = $tahun.$bulan.'-01';
                if($iperiode >= $periode_masuk) {
                    $salary = $kalkulasi[0]->karyawanmasakerja_salary;
                    $bruto = $perhitungan['total_bruto_perbulan'];
                    $pph21 = $perhitungan['total_pph_terutang_perbulan'];
                    $data_pph21 = json_encode($perhitungan);
                }
                array_push($temp_data, [
                    'ms_karyawanmasakerja_id' => $kalkulasi[0]->karyawanmasakerja_id,
                    'ms_karyawan_id' => $karyawanId,
                    'karyawankalkulasi_month' => $bulan,
                    'karyawankalkulasi_year' => $tahun,
                    'karyawankalkulasi_salary' => $salary,
                    'karyawankalkulasi_pph21' => $pph21,
                    'karyawankalkulasi_bruto' => $bruto,
                    'karyawankalkulasi_method' => $kalkulasi[0]->karyawanmasakerja_calculation_method,
                    'karyawankalkulasi_status' => $kalkulasi[0]->karyawanmasakerja_status,
                    'karyawankalkulasi_position' => $kalkulasi[0]->karyawanmasakerja_position,
                    'karyawankalkulasi_bpjs' => json_encode($kalkulasi[0]->karyawanmasakerja_bpjs),
                    'karyawankalkulasi_tunjangan' => json_encode($kalkulasi[0]->karyawanmasakerja_tunjangan),
                    'karyawankalkulasi_tunjanganjabatan' => json_encode($tunjangan_jabatan),
                    'ms_objekpajak_code' => $kalkulasi[0]->ms_objekpajak_code_masakerja,
                    'ms_ptkp_id' => $kalkulasi[0]->ms_ptkp_id_masakerja,
                    'karyawankalkulasi_data' => $data_pph21,
                ]);
            }
        }
    }
    
    // return response()->json($temp_data);
    return $temp_data;
}
