<?php
namespace App\Http\Controllers\User\PajakPenghasilan;

use App\Http\Controllers\Controller;
use App\Libraries\AppPPh21Library;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PtkpDetailModel;
use App\Model\Mview\VwKaryawanPayrollModel;
use App\Model\Setting\SettingPajakPph21Model;
use App\Model\Transaction\PayrollModel;
use App\User;
use Carbon\Carbon;
use DOMDocument;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class PPh21NonKaryawanController extends Controller
{
      /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $user_id = session()->get('user_data')['user_id'];
        $periode = date('m-Y');
        $user = User::with(['wajibpajak.wpstpenggajian'])->where([
            'user_id' => $user_id
        ])->first();

        $data = [
            'title' => 'Pajak Penghasilan PPh 21 Non Karyawan',
            'content' => 'user.pajak-penghasilan.pph21.pph21-nonkaryawan',
            'user' => $user
        ];
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
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $limit = ($request->get('length')) ? intval($request->get('length')) : 10;
        $offset = ($request->get('start')) ? intval($request->get('start')) : 0;
        $order_columns = ['karyawan_name','karyawan_npwp','karyawan_nik','karyawan_calculation_method','payroll_total_income','pph21_total_dpp_kumulatif','karyawan_id','pph21_total_netto'];
        $order_col = $order_columns[0];
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $periode = ($request->get('periode')) ? $request->get('periode') : date('m-Y');
        $payroll_periodyearmonth = Carbon::createFromFormat('!m-Y', $periode)->format('Y-m');
        // dd($periode)
        $karyawan_ids = $request->get('karyawan_ids');

        $where = [
            // 'karyawan_active' => 1,
            // 'karyawan_contract_end' => null,
            // 'karyawan_contract_now' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ];
        // if($status != '-1') {
            // $where['payroll_status'] = intval($status);
        // }

        $escape_periode = DB::connection()->getPdo()->quote($payroll_periodyearmonth);
        $list = KaryawanModel::select("ms_karyawan.*"
        , DB::raw("(SELECT CASE WHEN SUM(payroll_total_income) > 0 THEN SUM(payroll_total_income) ELSE 0 END FROM tr_payroll WHERE ms_karyawan_id = karyawan_id AND payroll_status>='1' AND payroll_active='1' AND TO_CHAR(payroll_period, 'YYYY-MM') = {$escape_periode}) as payroll_total_income")
        , DB::raw("(SELECT CASE WHEN SUM(pph21_total_month) > 0 THEN SUM(pph21_total_month) ELSE 0 END FROM tr_pph21 JOIN tr_payroll ON tr_payroll.payroll_uuid = tr_pph21.tr_payroll_uuid AND payroll_status>='1' WHERE tr_pph21.ms_karyawan_id = karyawan_id AND pph21_active='1' AND TO_CHAR(pph21_period, 'YYYY-MM') = {$escape_periode}) as pph21_total_netto")
        , DB::raw("(SELECT CASE WHEN pph21_dpp_kumulatif > 0 THEN pph21_dpp_kumulatif ELSE 0 END FROM tr_pph21 JOIN tr_payroll ON tr_payroll.payroll_uuid = tr_pph21.tr_payroll_uuid AND payroll_status>='1' WHERE tr_pph21.ms_karyawan_id = karyawan_id AND pph21_active='1' AND TO_CHAR(pph21_period, 'YYYY-MM') = {$escape_periode} ORDER BY pph21_trx_at DESC LIMIT 1) as pph21_total_dpp_kumulatif")
        , DB::raw("(SELECT pph21_tarif21_data FROM tr_pph21 JOIN tr_payroll ON tr_payroll.payroll_uuid = tr_pph21.tr_payroll_uuid AND payroll_status>='1' WHERE tr_pph21.ms_karyawan_id = karyawan_id AND pph21_active='1' AND TO_CHAR(pph21_period, 'YYYY-MM') = {$escape_periode} ORDER BY pph21_trx_at DESC LIMIT 1) as pph21_tarif21_data")
        // , DB::raw("(SELECT payroll_status FROM tr_payroll WHERE ms_karyawan_id = karyawan_id AND payroll_active='1' AND TO_CHAR(payroll_period, 'YYYY-MM') = {$escape_periode} LIMIT 1) as payroll_status")
        )
        ->with(['payroll' => function($q) use($payroll_periodyearmonth) {
            $q->with('pph21');
            $q->whereRaw("(payroll_status>='1' AND payroll_active='1' AND TO_CHAR(payroll_period, 'YYYY-MM') = ?)", [$payroll_periodyearmonth]);
        }])
        ->when($payroll_periodyearmonth, function($q, $payroll_periodyearmonth) {
            return $q->whereRaw("(
                CASE WHEN karyawan_contract_end IS NOT NULL
                THEN 
                    TO_CHAR(karyawan_contract_end, 'YYYY-MM') >= ?
                ELSE
                    karyawan_contract_end IS NULL
                END
            )", [$payroll_periodyearmonth]);
        })
        ->when($karyawan_ids, function($q, $karyawan_ids) {
            return $q->whereIn('karyawan_id', $karyawan_ids);
        })
        ->where($where)
        ->whereIn('karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("((SELECT CASE WHEN SUM(payroll_total_income) > 0 THEN SUM(payroll_total_income) ELSE 0 END FROM tr_payroll WHERE ms_karyawan_id = karyawan_id AND payroll_status>='1' AND payroll_active='1' AND TO_CHAR(payroll_period, 'YYYY-MM') = {$escape_periode}) > 0)")
        ->whereRaw("(TO_CHAR(karyawan_contract_begin, 'YYYY-MM') <= ?)", [$payroll_periodyearmonth])
        ->take($limit)->skip($offset)->orderBy($order_col, $order_type)->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = KaryawanModel::
        when($payroll_periodyearmonth, function($q, $payroll_periodyearmonth) {
            return $q->whereRaw("(
                CASE WHEN karyawan_contract_end IS NOT NULL
                THEN 
                    TO_CHAR(karyawan_contract_end, 'YYYY-MM') >= ?
                ELSE
                    karyawan_contract_end IS NULL
                END
            )", [$payroll_periodyearmonth]);
        })
        ->when($karyawan_ids, function($q, $karyawan_ids) {
            return $q->whereIn('karyawan_id', $karyawan_ids);
        })
        ->where($where)
        ->whereIn('karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("((SELECT CASE WHEN SUM(payroll_total_income) > 0 THEN SUM(payroll_total_income) ELSE 0 END FROM tr_payroll WHERE ms_karyawan_id = karyawan_id AND payroll_status>='1' AND payroll_active='1' AND TO_CHAR(payroll_period, 'YYYY-MM') = {$escape_periode}) > 0)")
        ->whereRaw("(TO_CHAR(karyawan_contract_begin, 'YYYY-MM') <= ?)", [$payroll_periodyearmonth])->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function export(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $periode = ($request->get('periode')) ? $request->get('periode') : date('m-Y');
        if (formatDate($periode, '/^\d{2}-\d{4}$/') != true) {
            // valid date
            echo "<script>alert('Format periode tidak valid!');window.close();</script>";
            die;
        }
        // dd($periode);
        // $periodelbl = Carbon::parse($periode)->format('F Y');
        // $periodepotong = ($request->get('periodepotong')) ? $request->get('periodepotong') : date('d-m-Y');
        // if (formatDate($periodepotong, '/^\d{2}-\d{2}-\d{4}$/') != true) {
        //     // valid date
        //     echo "<script>alert('Format tanggal bukti potong tidak valid!');window.close();</script>";
        //     die;
        // }
        // $periodepotong = Carbon::createFromFormat('d-m-Y', $periodepotong)->format('d-m-Y');
        $periodelbl = Carbon::createFromFormat('!m-Y',$periode)->translatedFormat('F Y');
        $periodeyear = Carbon::createFromFormat('!m-Y', $periode)->format('Y');
        $periodemonth = Carbon::createFromFormat('!m-Y', $periode)->format('n');
        // dd($periodemonth);
        $karyawan_ids = $request->get('karyawan_ids');
        $pembetulan = $request->get('pembetulan');
        $type = $request->get('type');

        if($type == 'espt') {
            if(!in_array('EXPORT_EXCEL_ESPT', request()->get('permission_codes'))) {
                die('access forbidden');
            }
        }
        if($type == 'rekap') {
            if(!in_array('EXPORT_EXCEL_REKAP', request()->get('permission_codes'))) {
                die('access forbidden');
            }
        }
        if($type == 'vi') {
            if(!in_array('EXPORT_BUKTIPOTONG', request()->get('permission_codes'))) {
                die('access forbidden');
            }
        }

        if($pembetulan < 0) {
            return response()->json([
                'success' => false,
                'message' => 'Pembetulan wajib diisi!'
            ]);
        }
        try {
            $where = [
                'ms_wajibpajak_id' => $wajibpajak_id,
                // 'payroll_lock' => 1,
                // 'payroll_status' => 3,
                'payroll_active' => 1,
            ];

            $data = PayrollModel::with(['karyawan', 'pph21'])
            // with(['currentperiod_payroll'])
            ->when($karyawan_ids, function($q, $karyawan_ids) {
                $karyawan_ids_exp = [];
                foreach($karyawan_ids as $kid) {
                    $karyawan_ids_exp[] = intval($kid);
                }
                
                return $q->whereIn('ms_karyawan_id', $karyawan_ids_exp);
            })
            ->where($where)
            ->where('payroll_status', '>=', 1)
            ->whereIn('payroll_karyawan_status', ['NONKARYAWAN'])
            ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$periode])
            ->orderBy('ms_karyawan_id', 'desc')
            ->orderBy('payroll_period', 'asc')->get();
            // ->orderBy('payroll_trx_at', 'asc')->get();
            // dd($data);
            if(count($data) < 1) {
                // abort(404);
                echo "<script>alert('Data tidak ditemukan!');window.close();</script>";
                die;
            }

            // dd($data);
            if($type == 'espt') {
                
                $title = 'e-SPT PPh21 '.$periode;
                $htmlString = view('user.pajak-penghasilan.export.espt-nonkaryawan', ['title' => $title, 'data' => $data, 'periodelbl' => $periodelbl])->render();
                // return view('user.pajak-penghasilan.export.espt-nonkaryawan', ['title' => $title, 'data' => $data, 'periodelbl' => $periodelbl]);
                    // echo $htmlString;
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
                $spreadsheet = $reader->loadFromString($htmlString);
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);

                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('A1:I1')
                    ->getFont()
                    ->setBold(true);
            } else if($type == 'ebupot-excel21') { // Ebupot bulanan 21
                $title = 'FORMAT_UPLOAD_EBUPOT_21';
                // return view('user.pajak-penghasilan.export.1721-bulan-karyawan', ['title' => $title, 'data' => $data, 'stpajakpph21' => $stpajakpph21]);
                //load spreadsheet
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("../public/assets/import/Template_ebupot_1721_bulan.xlsx");

                // dd($spreadsheet);
                //change it
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('D2', $periodeyear);
                $sheet->setCellValue('G2', $periodemonth);
                $sheet->setCellValue('G3', count($data));
                $sheet21 = $spreadsheet->getSheetByName('21');
                $ic = 1;
                $icstart = 3;
                
                foreach($data as $dt) {
                    $karyawan = $dt->karyawan;
                    $pph21 = $dt->pph21;
                    $stpenggajian = json_decode($dt->payroll_penggajiansetting);
                    $ptkp = json_decode($pph21->pph21_ptkp_data);
                    $ptkp_code = (isset($ptkp->ptkp_code)) ? $ptkp->ptkp_code : $karyawan->ptkp->ptkp_code;
                    // dd($karyawan->ptkp);
                    $stpajakpph21 = json_decode($pph21->pph21_pajakpph21_data);
                    // dd($stpajakpph21);
                    // $sheet21->getStyle('D'.$icstart)
                    // ->getNumberFormat()
                    // ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

                    $sheet21->setCellValue('A'.$icstart, $ic);
                    $sheet21->setCellValue('B'.$icstart, Carbon::parse($dt->payroll_paid_at)->format('d-m-Y'));
                    $sheet21->setCellValue('C'.$icstart, $dt->payroll_karyawan_name);
                    if($dt->payroll_karyawan_npwp != '00.000.000.0-000.000') {
                        $sheet21->setCellValueExplicit('D'.$icstart, strval(str_replace('-','', str_replace('.','', $dt->payroll_karyawan_npwp))), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2);
                    }
                    $sheet21->setCellValueExplicit('E'.$icstart, strval($dt->payroll_karyawan_nik), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2);
                    $sheet21->setCellValue('F'.$icstart, $dt->payroll_karyawan_name);
                    $sheet21->setCellValue('G'.$icstart, $dt->payroll_karyawan_address);
                    $sheet21->setCellValue('H'.$icstart, $dt->karyawan->karyawan_code_objekpajak);
                    $sheet21->setCellValue('I'.$icstart, 'NPWP');
                    $sheet21->setCellValueExplicit('J'.$icstart, strval(str_replace('-','', str_replace('.','', $stpajakpph21->stpajakpph21_npwp))), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2);
                    // $sheet21->setCellValue('K'.$icstart, "`".$stpajakpph21->stpajakpph21_nik);
                    $sheet21->setCellValue('L'.$icstart, $ptkp_code);
                    $sheet21->setCellValue('M'.$icstart, 'Ya');
                    $sheet21->setCellValue('N'.$icstart, ($pph21->pph21_method == 'GROSS') ? 'Ya' : 'Tidak');
                    $sheet21->setCellValue('O'.$icstart, $pph21->pph21_bruto_month);
                    $sheet21->setCellValue('P'.$icstart, '');
                    $sheet21->setCellValue('Q'.$icstart, '');
                    $sheet21->setCellValue('R'.$icstart, 'N');

                    $ic++;
                    $icstart++;
                }
            } else if($type == 'vi') { // 1721-VI
                $title = '1721-VI';
                // dd($data);
                // $stpajakpph21 = json_decode($pph21->pph21_pajakpph21_data);
                return view('user.pajak-penghasilan.export.1721-vi-nonkaryawan', ['title' => $title, 'data' => $data]);
            } else if($type == 'vi-bulan') { // 1721-VI Bulanan
                $title = '1721-VI';
                // dd($data);
                return view('user.pajak-penghasilan.export.1721-vi-bulan-nonkaryawan', ['title' => $title, 'data' => $data]);
            } else {
                // dd($data);
                // Rekap Perhitungan PPh21 10-2023
                $title = 'Rekap Perhitungan PPh21 '.$periode;
                $htmlString = view('user.pajak-penghasilan.export.rekap-nonkaryawan', ['title' => $title, 'data' => $data, 'periodelbl' => $periodelbl])->render();
                // return view('user.pajak-penghasilan.export.rekap-nonkaryawan', ['title' => $title, 'data' => $data, 'periodelbl' => $periodelbl]);
                    // echo $htmlString;
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
                $spreadsheet = $reader->loadFromString($htmlString);
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);

                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('A2:G3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('DEB887');
                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('A2:G3')->getFont()
                    ->setBold(true);

                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('H2:K3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('98FB98');
                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('H2:L3')->getFont()
                    ->setBold(true);

                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('C')->getNumberFormat()->setFormatCode('#');
                // $spreadsheet->getActiveSheet()->setCellValueExplicit('D'.$icstart, strval(str_replace('-','', str_replace('.','', $dt->payroll_karyawan_npwp))), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2);
            }

                
            // $writer = new Xls($spreadsheet);
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");

            $filename = $title.'.xlsx';
            $location = public_path('assets/export/');
            ob_start();
            $writer->save($location.$filename);
            // $xlsData = ob_get_contents();
            ob_end_clean();
            
            $content = file_get_contents($location.$filename);
            // echo url('/assets/export/'.$filename);
            // return url('/assets/export/'.$filename);
            header("Content-Disposition: attachment; filename=".$filename);

            unlink($location.$filename);
            exit($content);
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}