<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanKalkulasiModel;
use App\Model\Master\KaryawanMasakerjaModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PtkpModel;
use App\Model\Master\Tarif21Model;
use App\Model\Master\TunjanganJabatanModel;
use Carbon\Carbon;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KaryawanKalkulasiController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($karyawanmasakerjaId, Request $request)
    {
        $karyawanmasakerja = KaryawanMasakerjaModel::select('tr_karyawan_masakerja.*', 'ms_karyawan.*', 'ptkp_description', 'objekpajak_code', 'objekpajak_description', 'karyawanmasakerja_end_reason', 'karyawanmasakerja_end_type')
        ->where(['karyawanmasakerja_id' => $karyawanmasakerjaId, 'ms_karyawan.ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],])
        ->join('ms_karyawan', 'karyawan_id', '=', 'ms_karyawan_id')
        ->join('ms_ptkp', 'ptkp_id', '=', 'ms_ptkp_id')
        ->leftJoin('ms_objek_pajak', 'objekpajak_code', '=', 'ms_objekpajak_code')
        ->first();
        if(!$karyawanmasakerja) {
            abort(404);
        }
        // dd($karyawanmasakerja->karyawanmasakerja_contract_end);

        $content = 'user.master.karyawan.kalkulasi.index';
        if($karyawanmasakerja->karyawanmasakerja_status == 'NONKARYAWAN') {
            $content = 'user.master.karyawan.kalkulasi.indexnonkaryawan';
        }
        $data = [
            'title' => 'Kalkulasi Pajak Karyawan',
            'tarif21' => Tarif21Model::where(['tarif21_active' => 1])->get(),
            'content' => $content,
            'karyawanmasakerja' => $karyawanmasakerja
        ];
        // dd($data['karyawan']);
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
    public function datatable($karyawanmasakerjaId, Request $request)
    {
        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
        $tahun = intval($request->input('tahun'));
        // $l = $this->generate($karyawanmasakerjaId, $request);
        // dd($tahun);

        $where = [
            'ms_karyawanmasakerja_id' => $karyawanmasakerjaId,
            'ms_karyawan.ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
            'karyawankalkulasi_year' => $tahun,
            // 'karyawankalkulasi_lock' => false,
            'karyawanmasakerja_active' => 1,
        ];

        // generate if not exist
        $check_kalkulasi_exist = KaryawanKalkulasiModel::
        select("ms_karyawan_kalkulasi.*", "tr_karyawan_masakerja.karyawanmasakerja_calculation_method"
        , "tr_karyawan_masakerja.karyawanmasakerja_active", "tr_karyawan_masakerja.karyawanmasakerja_contract_begin"
        , "tr_karyawan_masakerja.karyawanmasakerja_bpjs", "tr_karyawan_masakerja.karyawanmasakerja_tunjangan"
        , DB::raw("(SELECT json_agg(ptable) 
                FROM (
                    SELECT a.*
                    FROM ms_karyawan_kalkulasi as a
                    WHERE a.ms_karyawanmasakerja_id = tr_karyawan_masakerja.karyawanmasakerja_id
                    AND a.karyawankalkulasi_lock = true
                    ORDER BY a.karyawankalkulasi_month ASC
                )
            as ptable) as kalkulasi_lock_json
        ")
        )
        ->join("tr_karyawan_masakerja", "tr_karyawan_masakerja.karyawanmasakerja_id", "=", "ms_karyawan_kalkulasi.ms_karyawanmasakerja_id")
        ->join("ms_karyawan", "karyawan_id", "=", "ms_karyawan_kalkulasi.ms_karyawan_id")
        ->where($where)->first();
        // dd($check_kalkulasi_exist);
        if(!$check_kalkulasi_exist) {
            // if($check_kalkulasi_exist->karyawanmasakerja_active == '1') {
            $data_generate_pph21 = generatePPH2Karyawan($karyawanmasakerjaId, 0, 0);
            KaryawanKalkulasiModel::insert($data_generate_pph21);
            // dd($data_generate_pph21);
        } else {
            $periode_masuk = Carbon::parse($check_kalkulasi_exist->karyawanmasakerja_contract_begin)->format('mY');
            // dd($check_kalkulasi_exist->karyawanmasakerja_calculation_method !== $check_kalkulasi_exist->karyawankalkulasi_method
            // || $check_kalkulasi_exist->karyawankalkulasi_bpjs !== $check_kalkulasi_exist->karyawanmasakerja_bpjs
            // || json_encode($check_kalkulasi_exist->karyawankalkulasi_tunjangan) !== json_encode($check_kalkulasi_exist->karyawanmasakerja_tunjangan)
            // || $check_kalkulasi_exist->karyawankalkulasi_salary !== $check_kalkulasi_exist->karyawanmasakerja_salary);
            // print_r($check_kalkulasi_exist->karyawanmasakerja_tunjangan);
            if($check_kalkulasi_exist->karyawanmasakerja_calculation_method !== $check_kalkulasi_exist->karyawankalkulasi_method
            || $check_kalkulasi_exist->karyawankalkulasi_bpjs !== $check_kalkulasi_exist->karyawanmasakerja_bpjs
            || json_encode($check_kalkulasi_exist->karyawankalkulasi_tunjangan) !== json_encode($check_kalkulasi_exist->karyawanmasakerja_tunjangan)
            || $check_kalkulasi_exist->karyawankalkulasi_salary !== $check_kalkulasi_exist->karyawanmasakerja_salary
            ) {
                // dd($check_kalkulasi_exist);
                if($check_kalkulasi_exist->karyawanmasakerja_active == '1') {
                    $kalkulasi_lock_decode = ($check_kalkulasi_exist->kalkulasi_lock_json) ? json_decode($check_kalkulasi_exist->kalkulasi_lock_json) : null;
                    $kalkulasi_ids_arr = [];
                    $bulan_tahun_arr = [];
                    if($kalkulasi_lock_decode) {
                        foreach($kalkulasi_lock_decode as $kallock) {
                            array_push($kalkulasi_ids_arr, $kallock->karyawankalkulasi_id);
                            array_push($bulan_tahun_arr, $kallock->karyawankalkulasi_month.$kallock->karyawankalkulasi_year);
                        }
                    }
                    $data_generate_pph21 = generatePPH2Karyawan($karyawanmasakerjaId, 0, 0);
                    // dd($data_generate_pph21);
                    $new_data_generate_pph21 = [];
                    // temukan data yang ada tidak di lock
                    foreach($data_generate_pph21 as $genpph21) {
                        $periodekal = $genpph21['karyawankalkulasi_month'].$genpph21['karyawankalkulasi_year'];
                        // jika tidak di lock
                        if(!in_array($periodekal, $bulan_tahun_arr)) {
                            // jika periode lebih dari sama dengan tgl masuk
                            if($periodekal >= $periode_masuk) {
                                array_push($new_data_generate_pph21, $genpph21);
                            }
                        }
                    }
                    // dd($new_data_generate_pph21);
                    if($new_data_generate_pph21) {
                        // update kalkulasi yang tidak dilock
                        KaryawanKalkulasiModel::whereNotIn('karyawankalkulasi_id', $kalkulasi_ids_arr)
                        ->where([
                            'ms_karyawanmasakerja_id' => $karyawanmasakerjaId,
                        ])
                        ->where('karyawankalkulasi_month', '>=', Carbon::parse($check_kalkulasi_exist->karyawanmasakerja_contract_begin)->format('m'))
                        ->where('karyawankalkulasi_year', '>=', Carbon::parse($check_kalkulasi_exist->karyawanmasakerja_contract_begin)->format('Y'))
                        ->update([
                            'karyawankalkulasi_salary' => $new_data_generate_pph21[0]['karyawankalkulasi_salary'],
                            'karyawankalkulasi_pph21' => $new_data_generate_pph21[0]['karyawankalkulasi_pph21'],
                            'karyawankalkulasi_bruto' => $new_data_generate_pph21[0]['karyawankalkulasi_bruto'],
                            'karyawankalkulasi_method' => $new_data_generate_pph21[0]['karyawankalkulasi_method'],
                            // 'karyawankalkulasi_status' => $new_data_generate_pph21['karyawankalkulasi_status'],
                            'karyawankalkulasi_position' => $new_data_generate_pph21[0]['karyawankalkulasi_position'],
                            'karyawankalkulasi_bpjs' => $new_data_generate_pph21[0]['karyawankalkulasi_bpjs'],
                            'karyawankalkulasi_tunjangan' => $new_data_generate_pph21[0]['karyawankalkulasi_tunjangan'],
                            'karyawankalkulasi_tunjanganjabatan' => $new_data_generate_pph21[0]['karyawankalkulasi_tunjanganjabatan'],
                            'ms_objekpajak_code' => $new_data_generate_pph21[0]['ms_objekpajak_code'],
                            'ms_ptkp_id' => $new_data_generate_pph21[0]['ms_ptkp_id'],
                            'karyawankalkulasi_data' => $new_data_generate_pph21[0]['karyawankalkulasi_data'],
                        ]);
                    }
                }
            }
        }
        // dd('aaa');
        
        $list = KaryawanKalkulasiModel::select('ms_karyawan_kalkulasi.*')
        ->join("tr_karyawan_masakerja", "tr_karyawan_masakerja.karyawanmasakerja_id", "=", "ms_karyawan_kalkulasi.ms_karyawanmasakerja_id")
        ->join("ms_karyawan", "karyawan_id", "=", "tr_karyawan_masakerja.ms_karyawan_id")
        ->where($where)
        // ->when($search, function($q, $search) {
        //     return $q->where(DB::raw('LOWER(karyawan_nik)'), 'like', '%'.strtolower($search).'%')
        //     ->orWhere(DB::raw('LOWER(karyawan_npwp)'), 'like', '%'.strtolower($search).'%')
        //     ->orWhere(DB::raw('LOWER(karyawan_name)'), 'like', '%'.strtolower($search).'%');
        // })
        ->take($limit)->skip($offset)->orderBy('karyawankalkulasi_month', 'asc')->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = KaryawanKalkulasiModel::join("tr_karyawan_masakerja", "tr_karyawan_masakerja.karyawanmasakerja_id", "=", "ms_karyawan_kalkulasi.ms_karyawanmasakerja_id")
        ->join("ms_karyawan", "karyawan_id", "=", "tr_karyawan_masakerja.ms_karyawan_id")
        ->where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;

        return response()->json($data);
    }

    public function lock($karyawanmasakerjaId, Request $request)
    {
        $kalkulasi_id = $request->input('karyawankalkulasi_id');
        $kalkulasi = KaryawanKalkulasiModel::select('ms_karyawan_kalkulasi.*'
        , 'karyawanmasakerja_status', 'karyawanmasakerja_calculation_method'
        , 'karyawanmasakerja_salary', 'karyawanmasakerja_bpjs', 'karyawanmasakerja_tunjangan', 'tr_karyawan_masakerja.ms_objekpajak_code as ms_objekpajak_code_masakerja', 'tr_karyawan_masakerja.ms_objekpajak_code as ms_objekpajak_code_masakerja', 'tr_karyawan_masakerja.ms_ptkp_id as ms_ptkp_id_masakerja')
        ->join("tr_karyawan_masakerja", 'karyawanmasakerja_id', '=', 'ms_karyawan_kalkulasi.ms_karyawanmasakerja_id')
        ->join("ms_karyawan", 'karyawan_id', '=', 'tr_karyawan_masakerja.ms_karyawan_id')
        ->where([
            'karyawanmasakerja_id' => $karyawanmasakerjaId,
            'ms_karyawan.ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
            'karyawankalkulasi_id' => $kalkulasi_id,
            'karyawankalkulasi_active' => 1,
        ])->first();
        if(!$kalkulasi) {
            return response()->json([
                'success' => false,
                'message' => 'Kalkulasi tidak ditemukan!'
            ]);
        }

        // dd($kalkulasi);
        if($kalkulasi->karyawankalkulasi_lock) {
            // unlock
            $data = [
                'karyawankalkulasi_lock' => false,
                'karyawankalkulasi_lock_at' => null,
                'ms_ptkp_id' => $kalkulasi->ms_ptkp_id_masakerja,
                'ms_objekpajak_code' => $kalkulasi->ms_objekpajak_code_masakerja,
                'karyawankalkulasi_status' => $kalkulasi->karyawanmasakerja_status,
                'karyawankalkulasi_method' => $kalkulasi->karyawanmasakerja_calculation_method,
                'karyawankalkulasi_salary' => $kalkulasi->karyawanmasakerja_salary,
                'karyawankalkulasi_bpjs' => json_encode($kalkulasi->karyawanmasakerja_bpjs),
                'karyawankalkulasi_tunjangan' => json_encode($kalkulasi->karyawanmasakerja_tunjangan),
            ];
        } else {
            $data = [
                'karyawankalkulasi_lock' => true,
                'karyawankalkulasi_lock_at' => date('Y-m-d H:i:s')
            ];
        }

        DB::beginTransaction();
        try {

            KaryawanKalkulasiModel::where(['karyawankalkulasi_id' => $kalkulasi_id])->update($data);
            
            // unlock
            if($kalkulasi->karyawankalkulasi_lock) {
                $data_generate_pph21 = generatePPH2Karyawan($karyawanmasakerjaId, $kalkulasi_id);
                KaryawanKalkulasiModel::where(['karyawankalkulasi_id' => $kalkulasi_id])
                ->update($data_generate_pph21);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => ($kalkulasi->karyawankalkulasi_lock) ? 'Unlock kalkulasi berhasil' : 'Lock kalkulasi berhasil',
            ];

        } catch(Error $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Non Karyawan
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable_nonkaryawan($karyawanmasakerjaId, Request $request)
    {
        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;

        $where = [
            'ms_karyawanmasakerja_id' => $karyawanmasakerjaId,
            'ms_karyawan.ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id']
        ];
        
        $list = KaryawanKalkulasiModel::select('ms_karyawan_kalkulasi.*'
        ,DB::raw("
        (SELECT row_to_json(ptable) 
        FROM
        (SELECT 
            SUM(karyawankalkulasi_bruto) as karyawankalkulasi_bruto_total
            FROM ms_karyawan_kalkulasi
            WHERE tr_karyawan_masakerja.karyawanmasakerja_id = ms_karyawan_kalkulasi.ms_karyawanmasakerja_id 
            AND karyawankalkulasi_active = '1'
        )
        as ptable
        ) as karyawankalkulasi_akumulasi"))
        ->join("tr_karyawan_masakerja", "tr_karyawan_masakerja.karyawanmasakerja_id", "=", "ms_karyawan_kalkulasi.ms_karyawanmasakerja_id")
        ->join("ms_karyawan", "karyawan_id", "=", "tr_karyawan_masakerja.ms_karyawan_id")
        ->where($where)
        // ->when($search, function($q, $search) {
        //     return $q->where(DB::raw('LOWER(karyawan_nik)'), 'like', '%'.strtolower($search).'%')
        //     ->orWhere(DB::raw('LOWER(karyawan_npwp)'), 'like', '%'.strtolower($search).'%')
        //     ->orWhere(DB::raw('LOWER(karyawan_name)'), 'like', '%'.strtolower($search).'%');
        // })
        ->take($limit)->skip($offset)->orderBy('karyawankalkulasi_id', 'asc')->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = KaryawanKalkulasiModel::join("tr_karyawan_masakerja", "tr_karyawan_masakerja.karyawanmasakerja_id", "=", "ms_karyawan_kalkulasi.ms_karyawanmasakerja_id")
        ->join("ms_karyawan", "karyawan_id", "=", "tr_karyawan_masakerja.ms_karyawan_id")
        ->where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;

        return response()->json($data);
    }

    function locknonkaryawan($karyawanmasakerjaId, Request $request)
    {
        $this->validate($request, [
            'nonkaryawan_tgltrx' => 'required',
            'nonkaryawan_pendapatankotor' => 'required|integer',
        ], [
            'nonkaryawan_tgltrx.required' => 'Tanggal wajib diisi!',
            'nonkaryawan_pendapatankotor.required' => 'Pendapatan Kotor wajib diisi!',
        ]);

        $karyawanmasakerja = KaryawanMasakerjaModel::select('tr_karyawan_masakerja.*', 'ms_karyawan.*', 'ptkp_description', 'objekpajak_code', 'objekpajak_description'
        , DB::raw("(SELECT SUM(karyawankalkulasi_bruto) FROM ms_karyawan_kalkulasi WHERE tr_karyawan_masakerja.karyawanmasakerja_id = ms_karyawan_kalkulasi.ms_karyawanmasakerja_id AND karyawankalkulasi_active = '1') as karyawankalkulasi_bruto_total"))
        ->where([
            'karyawanmasakerja_id' => $karyawanmasakerjaId,
            'karyawanmasakerja_active' => 1,
            'ms_karyawan.ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])
        ->leftJoin('ms_karyawan_kalkulasi', 'ms_karyawanmasakerja_id', '=', 'karyawanmasakerja_id')
        ->join('ms_karyawan', 'karyawan_id', '=', 'tr_karyawan_masakerja.ms_karyawan_id')
        ->join('ms_ptkp', 'ptkp_id', '=', 'tr_karyawan_masakerja.ms_ptkp_id')
        ->leftJoin('ms_objek_pajak', 'objekpajak_code', '=', 'tr_karyawan_masakerja.ms_objekpajak_code')
        ->first();
        // dd($karyawanmasakerja);
        if(!$karyawanmasakerja) {
            abort(404);
        }

        $nonkaryawan_tgltrx = \Carbon\Carbon::parse($request->input('nonkaryawan_tgltrx'));
        $nonkaryawan_tgltrx_dt = $nonkaryawan_tgltrx->translatedFormat('Y-m-d H:i:s');
        $nonkaryawan_pendapatankotor = $request->input('nonkaryawan_pendapatankotor');
        $karyawankalkulasi_bruto_total = ($karyawanmasakerja->karyawankalkulasi_bruto_total) ? $karyawanmasakerja->karyawankalkulasi_bruto_total : 0;

        // dd($nonkaryawan_pendapatankotor .' '. $karyawankalkulasi_bruto_total);
        $perhitunganpph21non = perhitunganPPH21Non($nonkaryawan_pendapatankotor, $karyawankalkulasi_bruto_total);
        // var_dump ($perhitunganpph21non); exit;

        $bulan = Carbon::parse($nonkaryawan_tgltrx_dt)->format('m');
        $tahun = Carbon::parse($nonkaryawan_tgltrx_dt)->format('Y');

        DB::beginTransaction();
        try {
            // check objek pajak
            $objekpajak_code = $karyawanmasakerja->ms_objekpajak_code;
            if($karyawanmasakerja->karyawankalkulasi_bruto_total > 0 && $karyawanmasakerja->ms_objekpajak_code == '21-100-09') {
                $objekpajak_code = '21-100-08';
                KaryawanMasakerjaModel::where(['karyawanmasakerja_id' => $karyawanmasakerja->karyawanmasakerja_id])
                ->update([
                    'ms_objekpajak_code' => $objekpajak_code
                ]);
            }

            // insert kalkulasi
            KaryawanKalkulasiModel::create([
                'ms_karyawanmasakerja_id' => $karyawanmasakerja->karyawanmasakerja_id,
                'ms_karyawan_id' => $karyawanmasakerja->karyawan_id,
                'karyawankalkulasi_month' => $bulan,
                'karyawankalkulasi_year' => $tahun,
                'karyawankalkulasi_salary' => $karyawankalkulasi_bruto_total,
                'karyawankalkulasi_pph21' => $perhitunganpph21non,
                'karyawankalkulasi_bruto' => $nonkaryawan_pendapatankotor,
                'karyawankalkulasi_method' => $karyawanmasakerja->karyawanmasakerja_calculation_method,
                'karyawankalkulasi_status' => $karyawanmasakerja->karyawanmasakerja_status,
                'karyawankalkulasi_position' => $karyawanmasakerja->karyawanmasakerja_position,
                'karyawankalkulasi_bpjs' => null,
                'karyawankalkulasi_tunjangan' => null,
                'karyawankalkulasi_tunjanganjabatan' => null,
                'ms_objekpajak_code' => $objekpajak_code,
                'ms_ptkp_id' => $karyawanmasakerja->ms_ptkp_id,
                'karyawankalkulasi_lock' => true,
                'karyawankalkulasi_lock_at' => $nonkaryawan_tgltrx_dt,
                'karyawankalkulasi_data' => json_encode([
                    'karyawankalkulasi_salary' => $karyawankalkulasi_bruto_total,
                    'karyawankalkulasi_netto' => $nonkaryawan_pendapatankotor / 2,
                    'nonkaryawan_pendapatankotor' => $nonkaryawan_pendapatankotor,
                    'nonkaryawan_pendapatankotor_sebelumnya' => $karyawankalkulasi_bruto_total,
                    'karyawankalkulasi_pph21' => $perhitunganpph21non,
                ]),
            ]);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kalkulasi berhasil disimpan',
                'data' => $karyawanmasakerja
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function locknonkaryawanupdate($karyawanmasakerjaId, $kalkulasiId, Request $request)
    {
        $this->validate($request, [
            'nonkaryawan_tgltrx' => 'required',
            'nonkaryawan_pendapatankotor' => 'required|integer',
        ], [
            'nonkaryawan_tgltrx.required' => 'Tanggal wajib diisi!',
            'nonkaryawan_pendapatankotor.required' => 'Pendapatan Kotor wajib diisi!',
        ]);

        $karyawankalkulasi = KaryawanKalkulasiModel::select('ms_karyawan_kalkulasi.*', 'tr_karyawan_masakerja.*', 'ms_karyawan.*', 'ptkp_description', 'objekpajak_code', 'objekpajak_description')
        ->where([
            'karyawanmasakerja_id' => $karyawanmasakerjaId,
            'karyawankalkulasi_id' => $kalkulasiId,
            'karyawanmasakerja_active' => 1,
            'ms_karyawan.ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])
        ->join('tr_karyawan_masakerja', 'tr_karyawan_masakerja.ms_karyawan_id', '=', 'ms_karyawan_kalkulasi.ms_karyawan_id')
        ->join('ms_karyawan', 'karyawan_id', '=', 'tr_karyawan_masakerja.ms_karyawan_id')
        ->leftJoin('ms_ptkp', 'ptkp_id', '=', 'tr_karyawan_masakerja.ms_ptkp_id')
        ->leftJoin('ms_objek_pajak', 'objekpajak_code', '=', 'tr_karyawan_masakerja.ms_objekpajak_code')
        ->first();
        // dd($karyawankalkulasi);
        if(!$karyawankalkulasi) {
            abort(404);
        }

        $nonkaryawan_tgltrx = \Carbon\Carbon::parse($request->input('nonkaryawan_tgltrx'));
        $nonkaryawan_tgltrx_dt = $nonkaryawan_tgltrx->translatedFormat('Y-m-d H:i:s');
        $nonkaryawan_pendapatankotor = $request->input('nonkaryawan_pendapatankotor');

        $perhitunganpph21non = perhitunganPPH21Non($nonkaryawan_pendapatankotor);
        

        $bulan = Carbon::parse($nonkaryawan_tgltrx_dt)->format('m');
        $tahun = Carbon::parse($nonkaryawan_tgltrx_dt)->format('Y');

        DB::beginTransaction();
        try {
            // insert kalkulasi
            KaryawanKalkulasiModel::
            where([
                'karyawankalkulasi_id' => $kalkulasiId
            ])
            ->update([
                'karyawankalkulasi_month' => $bulan,
                'karyawankalkulasi_year' => $tahun,
                'karyawankalkulasi_salary' => $nonkaryawan_pendapatankotor,
                'karyawankalkulasi_pph21' => $perhitunganpph21non,
                'karyawankalkulasi_bruto' => $nonkaryawan_pendapatankotor,
                'karyawankalkulasi_lock_at' => $nonkaryawan_tgltrx_dt,
                'karyawankalkulasi_data' => json_encode([
                    'karyawankalkulasi_salary' => $nonkaryawan_pendapatankotor,
                    'nonkaryawan_pendapatankotor' => $nonkaryawan_pendapatankotor,
                    'karyawankalkulasi_pph21' => $perhitunganpph21non,
                ]),
            ]);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kalkulasi berhasil disimpan',
                'data' => $karyawankalkulasi
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    function locknonkaryawandelete($karyawanmasakerjaId, $kalkulasiId, Request $request)
    {
        $karyawankalkulasi = KaryawanKalkulasiModel::select('ms_karyawan_kalkulasi.*', 'tr_karyawan_masakerja.*', 'ms_karyawan.*', 'ptkp_description', 'objekpajak_code', 'objekpajak_description')
        ->where([
            'karyawanmasakerja_id' => $karyawanmasakerjaId,
            'karyawankalkulasi_id' => $kalkulasiId,
            'karyawanmasakerja_active' => 1,
            'ms_karyawan.ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])
        ->join('tr_karyawan_masakerja', 'tr_karyawan_masakerja.ms_karyawan_id', '=', 'ms_karyawan_kalkulasi.ms_karyawan_id')
        ->join('ms_karyawan', 'karyawan_id', '=', 'tr_karyawan_masakerja.ms_karyawan_id')
        ->leftJoin('ms_ptkp', 'ptkp_id', '=', 'tr_karyawan_masakerja.ms_ptkp_id')
        ->leftJoin('ms_objek_pajak', 'objekpajak_code', '=', 'tr_karyawan_masakerja.ms_objekpajak_code')
        ->first();
        // dd($karyawankalkulasi);
        if(!$karyawankalkulasi) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            // insert kalkulasi
            KaryawanKalkulasiModel::
            where([
                'karyawankalkulasi_id' => $kalkulasiId
            ])
            ->delete();
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kalkulasi berhasil dihapus',
                'data' => $karyawankalkulasi
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