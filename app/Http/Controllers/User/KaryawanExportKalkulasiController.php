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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class KaryawanExportKalkulasiController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $karyawan = KaryawanModel::where(['karyawan_id' => $karyawanId, 'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],])->first();
        // if(!$karyawan) {
        //     abort(404);
        // }
        $data = [
            'title' => 'Export Kalkulasi Pajak Karyawan',
            'content' => 'user.master.karyawan.export.kalkulasi.index',
            // 'karyawan' => $karyawan
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
    // public function datatable($karyawanId, Request $request)
    // {
    //     $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
    //     $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
    //     $tahun = $request->input('tahun');
    //     $this->generate($karyawanId, $request);
    //     $where = [
    //         'ms_karyawan_id' => $karyawanId,
    //         'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
    //         'karyawankalkulasi_year' => $tahun,
    //     ];
    //     $list = KaryawanKalkulasiModel::select('ms_karyawan_kalkulasi.*')
    //     ->join("ms_karyawan", "karyawan_id", "=", "ms_karyawan_id")
    //     ->where($where)
    //     // ->when($search, function($q, $search) {
    //     //     return $q->where(DB::raw('LOWER(karyawan_nik)'), 'like', '%'.strtolower($search).'%')
    //     //     ->orWhere(DB::raw('LOWER(karyawan_npwp)'), 'like', '%'.strtolower($search).'%')
    //     //     ->orWhere(DB::raw('LOWER(karyawan_name)'), 'like', '%'.strtolower($search).'%');
    //     // })
    //     ->take($limit)->skip($offset)->orderBy('karyawankalkulasi_month', 'asc')->get();
    //     $data['draw'] = $request->input('draw');
	// 	$data['recordsTotal'] = $data['recordsTotal'];
	// 	$data['recordsFiltered'] = $data['recordsTotal'];
    //     $data['data'] = $list;
        
    //     return response()->json($data);
    // }

    public function export(Request $request)
    {
        $this->validate($request, [
            'export_masapajak' => 'required',
            'export_jenispajak' => 'required|integer',
            'export_pembetulan' => 'required|integer',
        ], [
            'nonkaryawan_tgltrx.required' => 'Tanggal wajib diisi!',
            'nonkaryawan_pendapatankotor.required' => 'Pendapatan Kotor wajib diisi!',
        ]);
        $masa_pajak = $request->input('export_masapajak');
        $pembetulan = $request->input('export_pembetulan');
        $jenispajak = $request->input('export_jenispajak');
        $jenispajak_arr = ($jenispajak == '1') ? ['TETAP', 'KONTRAK'] : ['NONKARYAWAN'];
        $title = 'Pajak_Karyawan_'.$masa_pajak;
        // dd($masa_pajak);
        $tahun = Carbon::createFromFormat('d-m-Y', '01-'.$masa_pajak)->format('Y');
        $bulan = Carbon::createFromFormat('d-m-Y', '01-'.$masa_pajak)->format('m');
        // dd($tahun.$bulan);
        // find kalkulasi
        $kalkulasi = KaryawanKalkulasiModel::select('ms_karyawan_kalkulasi.*'
            ,'karyawan_npwp','karyawan_name', 'ms_karyawan_kalkulasi.ms_objekpajak_code', 'ms_karyawan_kalkulasi.ms_ptkp_id', 'ms_karyawan.ms_wajibpajak_id', 'objekpajak_code'
        )
        ->join("tr_karyawan_masakerja", "ms_karyawanmasakerja_id", "=", "karyawanmasakerja_id")
        ->join("ms_karyawan", "tr_karyawan_masakerja.ms_karyawan_id", "=", "karyawan_id")
        ->leftJoin("ms_objek_pajak", "ms_karyawan_kalkulasi.ms_objekpajak_code", "=", "objekpajak_code")
        ->where([
            'karyawankalkulasi_lock' => true,
            'karyawankalkulasi_active' => 1,
            'karyawankalkulasi_month' => $bulan,
            'karyawankalkulasi_year' => $tahun,
            'karyawan_active' => 1,
            'ms_karyawan.ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])
        ->whereIn('karyawanmasakerja_status', $jenispajak_arr)
        ->get();
        // dd($kalkulasi);
        $masakerja_ids = [];
        if($kalkulasi) {
            $i = 0;
            foreach($kalkulasi as &$kal) {
                $bruto = 0;
                $pph21 = 0;
                foreach($kalkulasi as $k) {
                    if($k->karyawankalkulasi_month.$k->karyawankalkulasi_year === $kal->karyawankalkulasi_month.$kal->karyawankalkulasi_year
                        && $k->ms_karyawanmasakerja_id === $kal->ms_karyawanmasakerja_id
                    ) {
                        $bruto += $k->karyawankalkulasi_bruto;
                        $pph21 += $k->karyawankalkulasi_pph21;
                    }
                }
                
                $kal->karyawankalkulasi_bruto = $bruto;
                $kal->karyawankalkulasi_pph21 = $pph21;
                if(!in_array($kal->ms_karyawanmasakerja_id, $masakerja_ids)) {
                    array_push($masakerja_ids, $kal->ms_karyawanmasakerja_id);
                } else {
                    unset($kalkulasi[$i]);
                }
                $i++;
            }
        }
        // dd($kalkulasi);

        $htmlString = view('user.master.karyawan.export.kalkulasi.export', ['title' => $title,'kalkulasi' => $kalkulasi, 'pembetulan' => $pembetulan])->render();
            // echo $htmlString;
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($htmlString);
        
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
        $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);
        // ob_start();
        $writer = new Xls($spreadsheet);

        // $nik = ($kalkulasi[0]->karyawan_npwp)
        $filename = $title.'.xls';
        $location = public_path('assets/export/');
        ob_start();
        $writer->save($location.$filename);
        // $xlsData = ob_get_contents();
        ob_end_clean();

        return url('/assets/export/'.$filename);
    }
}