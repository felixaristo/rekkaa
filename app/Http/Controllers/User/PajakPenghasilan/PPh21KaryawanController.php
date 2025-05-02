<?php
namespace App\Http\Controllers\User\PajakPenghasilan;

use App\Http\Controllers\Controller;
use App\Jobs\ImportJob;
use App\Libraries\AppPayslipLibrary;
use App\Libraries\AppPPh21Library;
use App\Model\Master\BpjsRateModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\TunjanganJabatanModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Mview\VwKaryawanPayrollModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingPajakPph21Model;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use App\Model\Transaction\HistoryImportModel;
use App\Model\Transaction\PayrollModel;
use App\Model\Transaction\PPh21Model;
use App\User;
use Carbon\Carbon;
use DOMDocument;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class PPh21KaryawanController extends Controller
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
            'title' => 'Pajak Penghasilan PPh 21 Karyawan',
            'content' => 'user.pajak-penghasilan.pph21.pph21-karyawan',
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
        $order_columns = ['ms_karyawan_id','payroll_karyawan_name','payroll_karyawan_npwp','payroll_karyawanjabatan_name','payroll_method','payroll_total_netto','payroll_deduction_pph21'];
        $order_col = $order_columns[0];
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $periode = ($request->get('periode')) ? $request->get('periode') : date('m-Y');
        // dd($periode);
        $karyawan_ids = $request->get('karyawan_ids');

        $where = [
            'ms_wajibpajak_id' => $wajibpajak_id,
            // 'payroll_status' => 1,
            // 'payroll_lock' => 1,
        ];

        $list = PayrollModel::with(['karyawan', 'pph21'])
        // with(['currentperiod_payroll'])
        ->when($karyawan_ids, function($q, $karyawan_ids) {
            return $q->whereIn('ms_karyawan_id', $karyawan_ids);
        })
        ->where($where)
        ->where('payroll_status', '>=', 1)
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$periode])
        ->take($limit)->skip($offset)->orderBy($order_col, $order_type)->get();
        
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = PayrollModel::
        when($karyawan_ids, function($q, $karyawan_ids) {
            return $q->whereIn('ms_karyawan_id', $karyawan_ids);
        })
        ->where($where)
        ->where('payroll_status', '>=', 1)
        ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
        ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$periode])->count();
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
        $periodepotong = ($request->get('periodepotong')) ? $request->get('periodepotong') : date('d-m-Y');
        if (formatDate($periodepotong, '/^\d{2}-\d{2}-\d{4}$/') != true) {
            // valid date
            echo "<script>alert('Format tanggal bukti potong tidak valid!');window.close();</script>";
            die;
        }
        $periodepotong = Carbon::createFromFormat('d-m-Y', $periodepotong)->format('d-m-Y');
        $periodeyear = Carbon::createFromFormat('!m-Y', $periode)->format('Y');
        $periodemonth = Carbon::createFromFormat('!m-Y', $periode)->format('n');
        $periodebeginmonth = '01-'.$periodeyear;
        // $periodelbl = Carbon::parse($periode)->format('F Y');
        $periodelbl = Carbon::createFromFormat('!m-Y',$periode)->translatedFormat('F Y');
        // dd($periodelbl);
        $karyawan_ids = $request->get('karyawan_ids');
        $pembetulan = $request->get('pembetulan');
        $type = $request->get('type');
        $t = $request->get('t');

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

            if($type == 'espt' || $type == 'rekap') { // espt and rekap
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
                $data = PayrollModel::with(['karyawan', 'pph21'])
                // with(['currentperiod_payroll'])
                ->when($karyawan_ids, function($q, $karyawan_ids) {
                    return $q->whereIn('ms_karyawan_id', $karyawan_ids);
                })
                ->where($where)
                ->where('payroll_status', '>=', 1)
                ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
                ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$periode])->orderBy('ms_karyawan_id', 'desc')->get();
            } elseif($type == 'ebupot-excel21') {
                if(!in_array('EXPORT_BUKTIPOTONG', request()->get('permission_codes'))) {
                    die('access forbidden');
                }
                $data = PayrollModel::with(['karyawan', 'pph21'])
                ->when($karyawan_ids, function($q, $karyawan_ids) {
                    return $q->whereIn('ms_karyawan_id', $karyawan_ids);
                })
                ->where($where)
                ->where('payroll_status', '>=', 1)
                ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
                ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$periode])->orderBy('ms_karyawan_id', 'desc')->get();
                // dd($periodebeginmonth, $data);
                // dd($periodebeginmonth, $periode, $data);
            } elseif($type == 'viii') {
                if(!in_array('EXPORT_BUKTIPOTONG', request()->get('permission_codes'))) {
                    die('access forbidden');
                }
                $data = PayrollModel::with(['karyawan', 'pph21'])
                ->when($karyawan_ids, function($q, $karyawan_ids) {
                    return $q->whereIn('ms_karyawan_id', $karyawan_ids);
                })
                ->where($where)
                ->where('payroll_status', '>=', 1)
                ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
                ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$periode])
                ->orderBy('ms_karyawan_id', 'desc')->get();
                // dd($periodebeginmonth, $data);
                // dd($periodebeginmonth, $periode, $data);
            }  else { // a1
                if(!in_array('EXPORT_BUKTIPOTONG', request()->get('permission_codes'))) {
                    die('access forbidden');
                }
                $data = PayrollModel::with(['karyawan', 'pph21'])
                // with(['currentperiod_payroll'])
                ->when($karyawan_ids, function($q, $karyawan_ids) {
                    return $q->whereIn('ms_karyawan_id', $karyawan_ids);
                })
                ->where($where)
                ->where('payroll_status', '>=', 1)
                ->whereNotIn('payroll_karyawan_status', ['NONKARYAWAN'])
                ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') >= ? AND TO_CHAR(payroll_period, 'MM-YYYY') <= ?)", [$periodebeginmonth, $periode])
                ->orderBy('ms_karyawan_id', 'desc')->orderBy('payroll_period', 'asc')->get();
                // dd($periodebeginmonth, $data);
                // dd($periodebeginmonth, $periode, $data);
            }
            // dd($data);
            if(count($data) < 1) {
                // abort(404);
                echo "<script>alert('Data tidak ditemukan!');window.close();</script>";
                die;
            }

            // dd($data);
            if($type == 'espt') { // espt
                $title = 'e-SPT PPh21 '.$periode;
                $htmlString = view('user.pajak-penghasilan.export.espt-karyawan', ['title' => $title, 'data' => $data, 'periodelbl' => $periodelbl])->render();
                // return view('user.pajak-penghasilan.export.espt-karyawan', ['title' => $title, 'data' => $data]);
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
                    // $stpajakpph21 = ($pph21->pph21_pajakpph21_data) ? json_decode($pph21->pph21_pajakpph21_data) : null;
                    // $stpajakpph21 = ($stpajakpph21) ? $stpajakpph21 : null;
                    $stpajakpph21 = json_decode($pph21->pph21_pajakpph21_data);
                    // dd($stpenggajian);
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
                    $sheet21->setCellValue('M'.$icstart, 'Tidak');
                    $sheet21->setCellValue('N'.$icstart, ($pph21->pph21_method == 'GROSS') ? 'Ya' : 'Tidak');
                    $sheet21->setCellValue('O'.$icstart, $pph21->pph21_bruto_month);
                    $sheet21->setCellValue('P'.$icstart, '');
                    $sheet21->setCellValue('Q'.$icstart, '');
                    $sheet21->setCellValue('R'.$icstart, 'N');

                    $ic++;
                    $icstart++;
                }
            } else if($type == 'viii') { // excel 1721-A1
                $title = '1721-VIII';
                
                return view('user.pajak-penghasilan.export.1721-viii-bulan-karyawan', ['title' => $title, 'data' => $data, 'periodepotong' => $periodepotong]);
            } else if($type == 'a1') { // excel 1721-A1
                $title = '1721-A1';
                if($t == 'pdf') {
                    return view('user.pajak-penghasilan.export.1721-a1-karyawan', ['title' => $title, 'data' => $data, 'periodepotong' => $periodepotong]);
                } elseif( $t == 'xls') {
                    $title = 'FORMAT_UPLOAD_EBUPOT21_A1';
                    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("../public/assets/import/Template_ebupot_1721_a1.xlsx");

                    // dd($spreadsheet);

                    $karyawans = [];
                    $ji=0;
                    foreach($data as $dt) {
                        // $dt->pph21 = $dt->pph21;
                        // $dt->karyawan = $dt->karyawan;
                        $karyawans[$dt->ms_karyawan_id][] = $dt;
                        // if($ji==0) {
                        //     if($dt->pph21->pph21_pajakpph21_data) {
                        
                        //     }
                        // }
                        // $ji++;
                    }
                    // $stpajakpph21 = json_decode($data[count($data)-1]->pph21->pph21_pajakpph21_data);

                    
                    //change it
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setCellValue('G2', $periodeyear);
                    $sheet->setCellValue('G3', count($karyawans));
                    $sheet21 = $spreadsheet->getSheetByName('A1');
                    
                    $ic = 1;
                    $icstart = 3;
                    // dd($karyawans);
                    foreach($karyawans as $dt) {
                        // dd($dt[0]->karyawan->country->country_name);
                        // dd($dt);
                        $lastdtidx = count($dt) - 1;
                        $payroll_period = Carbon::parse($dt[0]->payroll_paid_at)->format('d-m-Y');
                        $karyawan_npwp = $dt[$lastdtidx]->payroll_karyawan_npwp;
                        $karyawan_nik = $dt[$lastdtidx]->payroll_karyawan_nik;
                        $beginmonth = Carbon::parse($dt[0]->pph21->pph21_period)->format('m');
                        $filtermonth = Carbon::createFromFormat('!m-Y', $periode)->format('m');
                        // $filtermonth = Carbon::parse($dt[count($dt)-1]->pph21->pph21_period)->format('m');
                        // $nobuktipotong = $dt[0]->pph21->pph21_nobuktipotong;
                        // $nik = $dt[0]->payroll_karyawan_nik;
                        $karyawan_name = $dt[$lastdtidx]->payroll_karyawan_name;
                        $karyawan_address = $dt[$lastdtidx]->payroll_karyawan_address;
                        $karyawan_gender = $dt[$lastdtidx]->payroll_karyawan_gender;
                        $karyawanjabatan_name = $dt[$lastdtidx]->payroll_karyawanjabatan_name;
                        $karyawan_citizenship = $dt[$lastdtidx]->karyawan->karyawan_citizenship;
                        $country_code = ($dt[0]->karyawan->country && $karyawan_citizenship == 'WNA') ? $dt[0]->karyawan->country->country_iso : '';
                        // $payroll_method = $dt[0]->payroll_method;
                        // $ptkp_description = $dt[0]->pph21->pph21_ptkp_description;
                        // $name = "[hi] helloz [hello] (hi) {jhihi}";
                        $ptkp = json_decode($dt[$lastdtidx]->pph21->pph21_ptkp_data);
                        $ptkp_code = (isset($ptkp->ptkp_code)) ? $ptkp->ptkp_code : $dt[$lastdtidx]->karyawan->ptkp->ptkp_code;
                        $ptkp_code_explode = explode('/',$ptkp_code); 
                        $objekpajak_code = $dt[$lastdtidx]->karyawan->karyawan_code_objekpajak;
                        // $stpajakpph21 = ($dt[$lastdtidx]->pph21->pph21_pajakpph21_data) ? json_decode($dt[$lastdtidx]->pph21->pph21_pajakpph21_data) : null;
                        // $stpajakpph21 = ($stpajakpph21) ? $stpajakpph21 : null;
                        $stpajakpph21 = json_decode($dt[$lastdtidx]->pph21->pph21_pajakpph21_data);

                        $total_prorate_salary = 0;
                        $total_allowance_pph21 = 0;
                        $total_tunjangan = 0;
                        $total_tunjangan_tahunan = 0;
                        $total_allowance_bpjstkkes = 0;
                        $total_biaya_jabatan = 0;
                        $total_biaya_bpjstkjht = 0;
                        $total_biaya = 0;
                        $total_netto = 0;
                        $total_netto_year = 0;
                        $total_ptkp = 0;
                        $total_pkp = 0;
                        $total_pph21_year = 0;
                        $total_pph21 = 0;
                        $total_pph21_sebelumbulanakhir = 0;
                        $total_netto_sebelumnya = 0;
                        $total_pph21_sebelumnya = 0;
                        $idtd = 0;
                        foreach($dt as $tdt) {
                            $total_prorate_salary += $tdt->pph21->pph21_prorate_salary;
                            $total_allowance_pph21 += $tdt->payroll_allowance_pph21;
                            $total_allowance_bpjstkkes += $tdt->payroll_allowance_bpjstk + $tdt->payroll_allowance_bpjskes;
                            $total_biaya_jabatan += $tdt->pph21->pph21_deduction_position;
                            $total_biaya_bpjstkjht += $tdt->pph21->pph21_deduction_jht_payslip + $tdt->pph21->pph21_deduction_jp;
                            $total_netto_sebelumnya += $tdt->pph21->pph21_prevcp_netto;
                            $total_pph21_sebelumnya += $tdt->pph21->pph21_prevcp_pph21_total;
                            $total_netto += $tdt->pph21->pph21_netto_month;
                            $total_netto_year += $tdt->pph21->pph21_netto_year;
                            $total_ptkp += $tdt->pph21->pph21_ptkp;
                            $total_pkp += $tdt->pph21->pph21_pkp;
                            $total_biaya += $total_biaya_jabatan + $total_biaya_bpjstkjht;
                            $total_pph21 += $tdt->pph21->pph21_total_month;
                            $total_pph21_year += $tdt->pph21->pph21_total_month * 12;

                            $nextidtd = $idtd+1;
                            if(isset($dt[$nextidtd])) {
                                $total_pph21_sebelumbulanakhir += $tdt->pph21->pph21_total_month;
                            }
                            $tunjangan = json_decode($tdt->payroll_allowance_setting);
                            if($tunjangan) {
                                foreach($tunjangan as $tj) {
                                    if($tj->sttunjangankaryawan_period == 'TAHUN') {
                                        $total_tunjangan_tahunan += $tj->sttunjangankaryawan_accumulate_value;
                                    } else {
                                        $total_tunjangan += $tj->sttunjangankaryawan_accumulate_value;
                                    }
                                }
                            }
                            $customtunjangan = json_decode($tdt->payroll_allowance_addition);
                            if($customtunjangan) {
                                foreach($customtunjangan as $ctj) {
                                $total_tunjangan += $ctj->nominal;
                                }
                            }
                            $idtd++;
                        }
                        
                        $total_bruto = $total_prorate_salary + $total_allowance_pph21 + $total_tunjangan + $total_allowance_bpjstkkes;
                    
                        $sheet21->setCellValue('A'.$icstart, $ic);
                        $sheet21->setCellValue('B'.$icstart, $periodepotong);
                        $sheet21->setCellValue('C'.$icstart, $karyawan_npwp != '00.000.000.0-000.000' ? 'NPWP' : 'NIK');
                        if($karyawan_npwp != '00.000.000.0-000.000') {
                            $sheet21->setCellValueExplicit('D'.$icstart, strval(str_replace('-','', str_replace('.','', $karyawan_npwp))), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2);
                        }
                        $sheet21->setCellValueExplicit('E'.$icstart, strval($karyawan_nik), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2);
                        $sheet21->setCellValue('F'.$icstart, $karyawan_name);
                        $sheet21->setCellValue('G'.$icstart, $karyawan_address);
                        $sheet21->setCellValue('H'.$icstart, $karyawan_gender);
                        $sheet21->setCellValue('I'.$icstart, $ptkp_code_explode[0]);
                        $sheet21->setCellValue('J'.$icstart, $ptkp_code_explode[1]);
                        $sheet21->setCellValue('K'.$icstart, $karyawanjabatan_name);
                        $sheet21->setCellValue('L'.$icstart, ($karyawan_citizenship == 'WNA') ? 'Ya' : 'Tidak');
                        $sheet21->setCellValue('M'.$icstart, $country_code);
                        $sheet21->setCellValue('N'.$icstart, $objekpajak_code);
                        $sheet21->setCellValue('O'.$icstart, $beginmonth);
                        $sheet21->setCellValue('P'.$icstart, $filtermonth);
                        $sheet21->setCellValue('Q'.$icstart, 'NPWP');
                        $sheet21->setCellValueExplicit('R'.$icstart, strval(str_replace('-','', str_replace('.','', $stpajakpph21->stpajakpph21_npwp))), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING2);
                        $sheet21->setCellValue('T'.$icstart, $total_bruto);
                        $sheet21->setCellValue('U'.$icstart, $total_prorate_salary);
                        $sheet21->setCellValue('V'.$icstart, $total_allowance_pph21);
                        $sheet21->setCellValue('W'.$icstart, $total_tunjangan);
                        $sheet21->setCellValue('X'.$icstart, 0);
                        $sheet21->setCellValue('Y'.$icstart, $total_allowance_bpjstkkes);
                        $sheet21->setCellValue('Z'.$icstart, 0);
                        $sheet21->setCellValue('AA'.$icstart, $total_tunjangan_tahunan);
                        $sheet21->setCellValue('AB'.$icstart, $total_biaya_jabatan);
                        $sheet21->setCellValue('AC'.$icstart, $total_biaya_bpjstkjht);
                        $sheet21->setCellValue('AD'.$icstart, 0);
                        $sheet21->setCellValue('AE'.$icstart, $total_netto_sebelumnya);
                        $sheet21->setCellValue('AF'.$icstart, $total_netto_year);
                        $sheet21->setCellValue('AG'.$icstart, $ptkp_code);
                        $sheet21->setCellValue('AH'.$icstart, $total_pph21_sebelumnya);
                        $sheet21->setCellValue('AI'.$icstart, 0);
                        $sheet21->setCellValue('AJ'.$icstart, $total_pph21_sebelumbulanakhir);
                        $sheet21->setCellValue('AK'.$icstart, 0);
                        $sheet21->setCellValue('AL'.$icstart, 'N');

                        $ic++;
                        $icstart++;
                    }
                }
            } else { // excel rekap
                // dd($data);
                // Rekap Perhitungan PPh21 10-2023
                $title = 'Rekap Perhitungan PPh21 '.$periode;
                $htmlString = view('user.pajak-penghasilan.export.rekap-karyawan', ['title' => $title, 'data' => $data, 'periodelbl' => $periodelbl])->render();
                // return view('user.pajak-penghasilan.export.rekap-karyawan', ['title' => $title, 'data' => $data]);
                    // echo $htmlString;
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
                $spreadsheet = $reader->loadFromString($htmlString);
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);

                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('A2:E3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('DEB887');
                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('A2:E3')->getFont()
                    ->setBold(true);

                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('F2:L3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('87CEFA');
                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('F2:L3')->getFont()
                    ->setBold(true);

                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('M2:Q3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('F08080');
                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('M2:Q3')->getFont()
                    ->setBold(true);

                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('R2:AA3')
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('98FB98');
                $spreadsheet
                    ->getActiveSheet()
                    ->getStyle('R2:AA3')->getFont()
                    ->setBold(true);
            }

                
            // $writer = new Xls($spreadsheet);
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
            // $writer->save("05featuredemo.xlsx");

            $filename = $title.'.xlsx';
            $location = public_path('assets/export/');
            
            // dd($location, $filename);
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

    public function importTemplate(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $pembetulan = $request->post('pembetulan') + 1;
        $pph12_period = $request->post('periode') ? $request->post('periode') : date('m-Y');
        try {
            
            $karyawan = KaryawanModel::where('ms_wajibpajak_id', $wajibpajak_id)
                ->where('karyawan_active', 1)
                ->where('karyawan_status', '!=', 'NONKARYAWAN')
                ->orderBy('karyawan_enid', 'ASC')
                ->get();
            if(!$karyawan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Karyawan tidak ditemukan!'
                ]);
            }
            // get rate bpjs from tr_pph21
            $bpjsrate = WajibPajakModel::select(
                    DB::raw("(SELECT MAX(pph21_allowance_jamkes_rate) FROM tr_pph21 
                    WHERE pph21_active = '1' AND ms_wajibpajak_id = {$wajibpajak_id} AND (TO_CHAR(pph21_period, 'MM-YYYY') = '{$pph12_period}')) as jamkes_rate")
                    , DB::raw("(SELECT MAX(pph21_allowance_jkk_rate) FROM tr_pph21 
                    WHERE pph21_active = '1' AND ms_wajibpajak_id = {$wajibpajak_id} AND (TO_CHAR(pph21_period, 'MM-YYYY') = '{$pph12_period}')) as jkk_rate")
                    , DB::raw("(SELECT MAX(pph21_allowance_jkm_rate) FROM tr_pph21 
                    WHERE pph21_active = '1' AND ms_wajibpajak_id = {$wajibpajak_id} AND (TO_CHAR(pph21_period, 'MM-YYYY') = '{$pph12_period}')) as jkm_rate")
                )
                ->where(['wajibpajak_id' => $wajibpajak_id])->first();
            // get tunjangan
            $sttunjangankaryawan = SettingTunjanganKaryawanModel::with(['stgrouptunjangan'])->where(['ms_wajibpajak_id' => $wajibpajak_id, 'sttunjangankaryawan_active' => 1])->get();

            // Get the file path from the 'public' directory
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("../public/assets/import/Templat_Import_Pembetulan_Pajak.xlsx");
            $sheetKaryawan = $spreadsheet->getSheetByName('Referensi Data Karyawan');
            $ic = 1;
            $icstart = 2;
            foreach($karyawan as $kary) {
                $sheetKaryawan->setCellValue('A'.$icstart, $kary->karyawan_enid);
                $sheetKaryawan->setCellValue('B'.$icstart, $kary->karyawan_name);
                $icstart++;
            }

            $lastalpha = 'B';
            $sheetPembetulan = $spreadsheet->getSheetByName('Impor Pembetulan');
            
            $lastalpha++;
            $sheetPembetulan->setCellValue($lastalpha.$ic, 'Periode*');
            $sheetPembetulan->getColumnDimension($lastalpha)->setAutoSize(TRUE);

            $lastalpha++;
            $sheetPembetulan->setCellValue($lastalpha.$ic, 'Pembetulan Ke*');
            $sheetPembetulan->getColumnDimension($lastalpha)->setAutoSize(TRUE);

            if($bpjsrate) {
                if($bpjsrate->jamkes_rate) {
                    $lastalpha++;
                    $sheetPembetulan->setCellValue($lastalpha.$ic, 'Jaminan Kesehatan ('.$bpjsrate->jamkes_rate.' %)');

                    $sheetPembetulan->getColumnDimension($lastalpha)->setAutoSize(TRUE);
                }
                if($bpjsrate->jkk_rate) {
                    $lastalpha++;
                    $sheetPembetulan->setCellValue($lastalpha.$ic, 'Jaminan Kecelakaan Kerja ('.$bpjsrate->jkk_rate.' %)');
                    $sheetPembetulan->getColumnDimension($lastalpha)->setAutoSize(TRUE);
                }
                if($bpjsrate->jkm_rate) {
                    $lastalpha++;
                    $sheetPembetulan->setCellValue($lastalpha.$ic, 'Jaminan Kematian ('.$bpjsrate->jkm_rate.' %)');
                    $sheetPembetulan->getColumnDimension($lastalpha)->setAutoSize(TRUE);
                }
            }
            
            $grouptunjanganids = [];
            foreach($sttunjangankaryawan as $tjkaryawan) {
                $lastalpha++;
                array_push($grouptunjanganids, $tjkaryawan->stgrouptunjangan->stgrouptunjangankaryawan_id);
                $sheetPembetulan->setCellValue($lastalpha.$ic, $tjkaryawan->stgrouptunjangan->stgrouptunjangankaryawan_name.' - RK'.$tjkaryawan->stgrouptunjangan->stgrouptunjangankaryawan_id);
                $sheetPembetulan->getColumnDimension($lastalpha)->setAutoSize(TRUE);
            }
            
            $boldFontStyle = [
                'font' => ['bold' => true],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];
            // dd($lastalpha);
            $sheetPembetulan->getStyle('A'.$ic)->applyFromArray($boldFontStyle);
            $sheetPembetulan->getStyle($lastalpha.$ic)->applyFromArray($boldFontStyle);
            
            $trpayroll = PayrollModel::with(['pph21','karyawan'])->where(['ms_wajibpajak_id' => $wajibpajak_id, 'payroll_active' => 1])
            ->where('payroll_status', '>=', 1)
            ->whereRaw("(TO_CHAR(payroll_period, 'MM-YYYY') = ?)", [$pph12_period])->get();
            // dd($trpayroll);
            if(count($trpayroll) <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pajak periode '.$pph12_period. ' tidak ditemukan'
                ]);
            }

            foreach($trpayroll as $py) {
                $pph21 = $py->pph21;
                $ic++;
                $column = 'A';
                $sheetPembetulan->setCellValue($column.$ic, $pph21->karyawan->karyawan_enid);
                $column++;
                $sheetPembetulan->setCellValue($column.$ic, $pph21->pph21_prorate_salary);
                $sheetPembetulan->getStyle($column.$ic)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $column++;
                $sheetPembetulan->setCellValue($column.$ic, $pph12_period);
                
                $column++;
                $sheetPembetulan->setCellValue($column.$ic, $pembetulan);
                $column++;
                $sheetPembetulan->setCellValue($column.$ic, $pph21->pph21_allowance_jamkes);
                $sheetPembetulan->getStyle($column.$ic)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $column++;
                $sheetPembetulan->setCellValue($column.$ic, $pph21->pph21_allowance_jkk);
                $sheetPembetulan->getStyle($column.$ic)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $column++;
                $sheetPembetulan->setCellValue($column.$ic, $pph21->pph21_allowance_jkm);
                $sheetPembetulan->getStyle($column.$ic)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                $payroll = $pph21->payroll;
                // dd($pph21);
                if($payroll) {
                    $tunjangandata = ($payroll->payroll_allowance_setting) ? json_decode($payroll->payroll_allowance_setting) : null;
                    if($tunjangandata) {
                        foreach($grouptunjanganids as $gptjid) {
                            foreach($tunjangandata as $tj) {
                                if($tj->st_grouptunjangankaryawan_id == $gptjid) {
                                    $column++;
                                    $sheetPembetulan->setCellValue($column.$ic, $tj->sttunjangankaryawan_accumulate_value);
                                    $sheetPembetulan->getStyle($column.$ic)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                                    break;
                                }           
                            }
                        }
                    }
                }
            }
            
            $writer = new Xls($spreadsheet);
            $unixtime = time();
            $filename = 'Templat_Impor_Pembetulan_' . $unixtime . '.xls';
            $location = public_path('assets/export/');
            ob_start();
            $writer->save($location.$filename);
            ob_end_clean();

            return response()->json([
                'success' => true,
                'message' => 'Sukses',
                'data' => [
                    'url' => route('user.download-locale', ['file' => $filename]),
                ]
            ]);
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importHistory(Request $request)
    {
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
        
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        
        // Get the start_date and end_date inputs
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        
        $dataHistory = HistoryImportModel::where('historyimport_type', 'TAXREVISE')
            ->where('tr_history_import.ms_wajibpajak_id', $wajibpajak_id)
            ->orderBy('historyimport_date', 'DESC')
            ->leftjoin('mr_user_wajib_pajak', 'tr_history_import.ms_user_id', '=', 'mr_user_wajib_pajak.ms_user_id')
            ->select('tr_history_import.historyimport_date', 'tr_history_import.ms_user_id', 'tr_history_import.historyimport_status'
            , 'tr_history_import.historyimport_id', 'tr_history_import.historyimport_file_name', 'tr_history_import.historyimport_detail_import'
            , 'tr_history_import.historyimport_originfile_name', 'mr_user_wajib_pajak.userwajibpajak_name');
        
        // Apply date filtering if start_date and end_date are provided
        if ($start_date && $end_date) {
            $dataHistory->whereBetween('tr_history_import.historyimport_date', [$start_date, $end_date]);
        }
        
        $dataHistory = $dataHistory->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data Setting Kehadiran.',
            'data' => $dataHistory->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $dataHistory->total(),
            'recordsTotal' => $dataHistory->total(),
        ], 200);
    }

    public function import(Request $request)
    {
        if (!$request->hasfile('file')) {
            $res["success"] = false;
            $res["message"] = 'Data excel wajib diisi!';
            return response()->json($res, 500);
        }
        $quota = 100;//$request->get('quota');
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $user_id = session()->get('user_data')['user_id'];

        // Get the uploaded file
        $file = $request->file('file');

        if(!in_array($file->extension(), ['xls','xlsx','csv'])) {
            $res["success"] = false;
            $res["message"] = 'Format harus xls, xlsx atau csv!';
            return response()->json($res, 500);
        }

        $filename = rand().'.xlsx';
        $file->move(public_path('uploads/'), $filename);
        DB::beginTransaction();
        try {
            $totalComplete = 0;
            $totalFail = 0;
            $createHistory = HistoryImportModel::create([
                'ms_wajibpajak_id' => $wajibpajak_id,
                'ms_user_id' => $user_id,
                'historyimport_type' => "TAXREVISE",
                'historyimport_date' => date('Y-m-d H:i:s'),
                'historyimport_file_name' => $filename,
                'historyimport_originfile_name' => $file->getClientOriginalName(),
                'historyimport_status' => 'PROCESSED'
            ]);
            
            // dispatch(new ImportJob($createHistory->historyimport_id, $createHistory->historyimport_type, ['quota' => $quota]));
            $importpph21 = $this->importPPh21($createHistory->historyimport_id, ['quota' => $quota]);
            // dd($importpph21);
            // ->delay(Carbon::now()->addSeconds(60));
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil import pembetulan.',
            ], 200);
        } catch (\Exception $e) {
            unlink(public_path('uploads/'). $filename); // remove file
            Log::error('Error occurred: ' . $e->getMessage(). ' Line: '. $e->getLine());
            DB::rollback();
            // something went wrong
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(). ' Line: '. $e->getLine()
            ]);
        }
    }

    public function importPPh21($historyid, $otherdata)
    {
        $getHistory = HistoryImportModel::where(['historyimport_id' => $historyid])->first();

        $wajibpajak_id = $getHistory->ms_wajibpajak_id;
        $wajibpajak = $getHistory->wajibpajak;
        $user_id = $getHistory->ms_user_id;
        $user = $getHistory->user;
        $filename = $getHistory->historyimport_file_name;
        // dd($getHistory);
        // $quota = $request->get('quota');

        $path = public_path('uploads/'.$filename);
        $inputFileType = IOFactory::identify($path);
        $reader = IOFactory::createReader($inputFileType);
        $worksheetData = $reader->listWorksheetInfo($path);

        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
    
        // Get the Sheet D
        $sheet = $spreadsheet->getSheetByName('Impor Pembetulan');
        $maxRows = $sheet->getHighestRow();
        $quota = $otherdata['quota'];

        $chunkFilter = new ChunkReadFilter();
        $reader->setReadFilter($chunkFilter);

        $chunkSize = 100; // read as chunk
        $startRow = 2; // mulai baris ke 2;

        $result = [];

        for ($startRow; $startRow <= $maxRows; $startRow += $chunkSize) {
            // if($quota && $startRow > $quota['maxquota']) { // exceded quota
            //     break;
            // }
            $chunkFilter = new ChunkReadFilter($startRow, $chunkSize);
            // // Tell the Read Filter, the limits on which rows we want to read this iteration
            $chunkFilter->setRows($startRow, $chunkSize);
            // Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
            $spreadsheet_chunk = $reader->load($path);
            $nb = $startRow;
            $max_chunk = $startRow + $chunkSize;

            $appPph21KaryawanLib = new AppPPh21Library();
            for($nb; $nb<=$max_chunk; $nb++) {
                $nbi = $nb + 1;
                $karyawan_enid = $sheet->getCell("A" . $nbi)->getValue(); // Kode Karyawan
                $prorate_salary = $sheet->getCell("B" . $nbi)->getValue(); // Gaji Pokok
                // dd($sheet->getCell("C" . $nbi)->getValue());
                $periode = convertExcelDate($sheet->getCell("C" . $nbi)); // Periode
                $periode_dtparse = Carbon::parse($periode); // Periode
                $periode_m = $periode_dtparse->format('m'); // Periode
                $periode_my = $periode_dtparse->format('m-Y'); // Periode
                $pembetulanx = $sheet->getCell("D" . $nbi)->getValue(); // Pembetulan Ke x
                $jamkes_total = $sheet->getCell("E" . $nbi)->getValue(); // Jamkes x%
                $jkk_total = $sheet->getCell("F" . $nbi)->getValue(); // JKK x%
                $jkm_total = $sheet->getCell("G" . $nbi)->getValue(); // JKM x%
                // $columnH = $sheet->getCell("H" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnI = $sheet->getCell("I" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnJ = $sheet->getCell("J" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnK = $sheet->getCell("K" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnL = $sheet->getCell("L" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnM = $sheet->getCell("M" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnN = convertExcelDate($sheet->getCell("N" . $nbi)); // Use $sheet instead of $worksheetData
                // $columnO = convertExcelDate($sheet->getCell("O" . $nbi)); // Use $sheet instead of $worksheetData
                // $columnP = $sheet->getCell("P" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnQ = $sheet->getCell("Q" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnR = $sheet->getCell("R" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnS = $sheet->getCell("S" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnT = $sheet->getCell("T" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnU = $sheet->getCell("U" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnV = $sheet->getCell("V" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnW = $sheet->getCell("W" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnX = convertExcelDate($sheet->getCell("X" . $nbi)); // Use $sheet instead of $worksheetData
                // $columnY = $sheet->getCell("Y" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                // $columnZ = convertExcelDate($sheet->getCell("Z" . $nbi)); // Use $sheet instead of $worksheetData
                $karyawan = KaryawanModel::where(['karyawan_enid' => $karyawan_enid, 'ms_wajibpajak_id' => $wajibpajak_id])->first();
                if(!$karyawan) {
                    continue;
                }
                $pph21 = PPh21Model::with(['payroll'])->where([
                                'ms_karyawan_id' => $karyawan->karyawan_id, 
                                'ms_wajibpajak_id' => $wajibpajak_id,
                                'pph21_active' => 1
                            ])
                            ->whereRaw("(TO_CHAR(pph21_period, 'YYYY-MM') = ?)", [$periode_dtparse->format('Y-m')])
                            ->orderBy('pph21_id', 'asc')->first();
                            
                // dd($pph21);
                $payroll = $pph21->payroll;
                // $appPayslipLib->ratenonnpwp = 100;
                // // $appPayslipLib->custom_allowance_data = $custom_allowance_data;
                // // $appPayslipLib->custom_deduction_data = $custom_deduction_data;
                // $appPayslipLib->payroll_period = $periode;//$payroll_period_ymd;
                // $appPayslipLib->karyawan = $karyawan;
                // $appPayslipLib->stpenggajian_karyawan = ($payroll && $payroll->payroll_penggajiansetting) ? json_decode($payroll->payroll_penggajiansetting) : null;
                // $stattendance = $payroll->payroll_attendancesetting;
                // $attendance_days = ($stattendance) ? json_decode($stattendance->attendance_working_day) : [];
                // $appPayslipLib->attendance_days = $attendance_days;
                
                // $appPph21KaryawanLib = new AppPPh21Library();
                $bpjskes_accumulate_total = $jamkes_total;
                $bpjstk_accumulate_total = $jkk_total + $jkm_total;
                $bpjstk_jp_accumulate_total = $pph21->pph21_allowance_jp;
                $tunjangan_nominal_pph21 = 0;
                $potongan_nominal_pph21 = 0;
                // $tunjangan_jabatan = $pph21->pph21_deduction_position;
                $tunjangan_jabatan = TunjanganJabatanModel::where(['tunjanganjabatan_active' => '1'])->first();
                $jkkrate = $pph21->pph21_allowance_jkk_rate;
                $bpjskes = ($pph21->pph21_allowance_jamkes_rate) ? 1 : 0;
                $bpjstk = ($pph21->pph21_allowance_jkk_rate) ? 1 : 0;
                $isbpjs = [$bpjskes, $bpjstk];
                $ratenonnpwp = 100;
                $bpjsrate = BpjsRateModel::where(['bpjsrate_active' => '1'])->get();
                $appPph21KaryawanLib->karyawan = $karyawan;
                $appPph21KaryawanLib->salary = $prorate_salary; // need check from prorate
                $appPph21KaryawanLib->method = $payroll->payroll_method;
                $appPph21KaryawanLib->bpjsrate = $bpjsrate;
                $appPph21KaryawanLib->bpjs_accumulate_total = [$bpjskes_accumulate_total, $bpjstk_accumulate_total, $bpjstk_jp_accumulate_total];
                $appPph21KaryawanLib->tunjangan = ['tunjangan_nominal' => $tunjangan_nominal_pph21, 'potongan_nominal' => $potongan_nominal_pph21, 'tunjangan_jabatan' => $tunjangan_jabatan];
                $appPph21KaryawanLib->jkkrate = $jkkrate;
                $appPph21KaryawanLib->isbpjs = $isbpjs;
                $appPph21KaryawanLib->ratenonnpwp = $ratenonnpwp;
                $appPph21KaryawanLib->periode_month = $periode_m;
                $appPph21KaryawanLib->periode_my = $periode_my;
                $appPph21KaryawanLib->pph21_prevcp_netto = 0;//$this->pph21_prevcp_netto;
                $appPph21KaryawanLib->pph21_prevcp_pph21_total = 0;//$this->pph21_prevcp_pph21_total;

                $calculate_pph21 = $appPph21KaryawanLib->calculateTotal();
                $ptkp = $calculate_pph21['ptkp'];
                // dd($calculate_pph21);
                $ptkp_detail = $calculate_pph21['ptkp_detail'];
                $result[] = [
                    'karyawan_enid' => $karyawan_enid,
                    'periode' => $periode,
                    'tr_payroll_uuid' => $pph21->tr_payroll_uuid,
                    'ms_user_id' => $pph21->ms_user_id,
                    'ms_wajibpajak_id' => $pph21->ms_wajibpajak_id,
                    'ms_karyawan_id' => $pph21->ms_karyawan_id,
                    'ms_ptkp_id' => $pph21->ms_ptkp_id,
                    'pph21_ptkp_description' => $ptkp['ptkp_description'],
                    'pph21_ptkp_rate' => $ptkp['ptkp_rate'],
                    'pph21_ptkp_category' => $ptkp['ptkp_category'],
                    'ms_ptkpdet_id' => ($ptkp_detail) ? $ptkp_detail['ptkpdet_id'] : 0,
                    'pph21_ptkpdet_rate_percentage' => ($ptkp_detail) ? floatval($ptkp_detail['ptkpdet_rate_percentage']) : null,
                    'pph21_ptkpdet_rate_nominal' => ($ptkp_detail) ? intval($ptkp_detail['ptkpdet_rate_month']) : null,
                    'pph21_method' => $pph21->pph21_method,
                    'pph21_period' => $periode,
                    'pph21_basic_salary' => $prorate_salary,
                    'pph21_prorate_salary' => $prorate_salary,
        
                    'pph21_deduction_other' => $calculate_pph21['nominal_potongan_lain'],
                    'pph21_deduction_position' => $calculate_pph21['total_biaya_jabatan'],
                    'pph21_deduction_jamkes' => $calculate_pph21['biaya_jamkes'],
                    'pph21_deduction_jamkes_payslip' => 0,//$calculate_pph21['biaya_jamkes_payslip'],
                    'pph21_deduction_jht' => $calculate_pph21['biaya_jht'],
                    'pph21_deduction_jp' => $calculate_pph21['biaya_jp'],
        
                    'pph21_deduction_jamkes_rate' => $calculate_pph21['biaya_jamkes_rate'],
                    'pph21_deduction_jamkes_payslip_rate' => 0,//$calculate_pph21['biaya_jamkes_payslip_rate'],
                    'pph21_deduction_jht_rate' => $calculate_pph21['biaya_jht_rate'],
                    'pph21_deduction_jp_rate' => $calculate_pph21['biaya_jp_rate'],
        
                    'pph21_allowance_other' => $calculate_pph21['nominal_tunjangan_lain'],
                    'pph21_allowance_jamkes' => $calculate_pph21['penghasilan_jamkes'],
                    'pph21_allowance_jkk' => $calculate_pph21['penghasilan_jkk'],
                    'pph21_allowance_jkm' => $calculate_pph21['penghasilan_jkm'],
                    'pph21_allowance_jht' => $calculate_pph21['penghasilan_jht'],
                    'pph21_allowance_jht_payslip' => 0,//$calculate_pph21['penghasilan_jht_payslip'],
                    'pph21_allowance_jp' => $calculate_pph21['penghasilan_jp'],
                    'pph21_allowance_jp_payslip' => 0,//$calculate_pph21['penghasilan_jp_payslip'],
                    
                    'pph21_allowance_jamkes_rate' => $calculate_pph21['penghasilan_jamkes_rate'],
                    'pph21_allowance_jkk_rate' => $calculate_pph21['penghasilan_jkk_rate'],
                    'pph21_allowance_jkm_rate' => $calculate_pph21['penghasilan_jkm_rate'],
                    'pph21_allowance_jht_rate' => $calculate_pph21['penghasilan_jht_rate'],
                    'pph21_allowance_jht_payslip_rate' => 0,//$calculate_pph21['penghasilan_jht_payslip_rate'],
                    'pph21_allowance_jp_rate' => $calculate_pph21['penghasilan_jp_rate'],
                    'pph21_allowance_jp_payslip_rate' => 0,//$calculate_pph21['penghasilan_jp_payslip_rate'],
                    // 'pph21_allowance_overtime' => $temp_data_payroll['payroll_allowance_overtime'],
                    'pph21_pkp' => $calculate_pph21['total_pkp_pertahun'],
                    'pph21_ptkp' => $calculate_pph21['total_ptkp'],
                    
                    'pph21_prevcp_netto' => $calculate_pph21['pph21_prevcp_netto'],
                    'pph21_prevcp_pph21_total' => $calculate_pph21['pph21_prevcp_pph21_total'],

                    'pph21_bruto_month' => $calculate_pph21['total_bruto_perbulan'],
                    'pph21_bruto_year' => $calculate_pph21['total_bruto_pertahun'],
                    'pph21_netto_month' => $calculate_pph21['total_neto_perbulan'],
                    'pph21_netto_year' => $calculate_pph21['total_neto_pertahun'],
                    'pph21_total_month' => $calculate_pph21['total_pph_terutang_perbulan'],
                    'pph21_total_year' => $calculate_pph21['total_pph_terutang_pertahun'],
        
                    'pph21_ptkp_data' => json_encode($ptkp),
                    'pph21_ptkpdet_data' => json_encode($ptkp_detail),
                    'pph21_tarif21_data' => ($calculate_pph21['tarif21']) ? json_encode($calculate_pph21['tarif21']) : null,
                    'pph21_pajakpph21_data' => $pph21->pph21_pajakpph21_data,
                    'pph21_pembetulan' => $pembetulanx,
                    "row" => $nb + 1
                ];
                // dd($result);
            }

            $spreadsheet_chunk->__destruct();
            $spreadsheet_chunk = null;
            unset($spreadsheet_chunk);
        }
        // then release the memory
        $spreadsheet->__destruct();
        $spreadsheet = null;
        unset($spreadsheet);
        $reader = null;
        unset($reader);
        // dd($result);
        DB::beginTransaction();
        try {
            $totalComplete = 0;
            $totalFail = 0;

            if(count($result) > 0) {
            //     $request = new \Illuminate\Http\Request();
            //     $request->setMethod('POST');

            //     $karyawanCreated = [];
            //     $karyawanCreatedForSetting = [];
            //     // dd(count($result));
                for($x = 0; $x < count($result); $x++) {
                    
                    if ($result[$x]['karyawan_enid'] === null || $result[$x]['karyawan_enid'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Karyawan Id';
                        continue;
                    }

                    if ($result[$x]['periode'] === null || $result[$x]['periode'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Periode';
                        continue;
                    }

                    if ($result[$x]['pph21_pembetulan'] === null || $result[$x]['pph21_pembetulan'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Pembetulan';
                        continue;
                    }

                    PPh21Model::create([
                        'tr_payroll_uuid' => $result[$x]['tr_payroll_uuid'],
                        'ms_user_id' => $result[$x]['ms_user_id'],
                        'ms_wajibpajak_id' => $result[$x]['ms_wajibpajak_id'],
                        'ms_karyawan_id' => $result[$x]['ms_karyawan_id'],
                        'ms_ptkp_id' => $result[$x]['ms_ptkp_id'],
                        'pph21_ptkp_description' => $result[$x]['pph21_ptkp_description'],
                        'pph21_ptkp_rate' => $result[$x]['pph21_ptkp_rate'],
                        'pph21_ptkp_category' => $result[$x]['pph21_ptkp_category'],
                        'ms_ptkpdet_id' => $result[$x]['ms_ptkpdet_id'],
                        'pph21_ptkpdet_rate_percentage' => $result[$x]['pph21_ptkpdet_rate_percentage'],
                        'pph21_ptkpdet_rate_nominal' => $result[$x]['pph21_ptkpdet_rate_nominal'],
                        'pph21_method' => $result[$x]['pph21_method'],
                        'pph21_period' => $result[$x]['pph21_period'],
                        'pph21_basic_salary' => $result[$x]['pph21_basic_salary'],
                        'pph21_prorate_salary' => $result[$x]['pph21_prorate_salary'],
            
                        'pph21_deduction_other' => $result[$x]['pph21_deduction_other'],
                        'pph21_deduction_position' => $result[$x]['pph21_deduction_position'],
                        'pph21_deduction_jamkes' => $result[$x]['pph21_deduction_jamkes'],
                        'pph21_deduction_jamkes_payslip' => $result[$x]['pph21_deduction_jamkes_payslip'],
                        'pph21_deduction_jht' => $result[$x]['pph21_deduction_jht'],
                        'pph21_deduction_jp' => $result[$x]['pph21_deduction_jp'],
            
                        'pph21_deduction_jamkes_rate' => $result[$x]['pph21_deduction_jamkes_rate'],
                        'pph21_deduction_jamkes_payslip_rate' => $result[$x]['pph21_deduction_jamkes_payslip_rate'],
                        'pph21_deduction_jht_rate' => $result[$x]['pph21_deduction_jht_rate'],
                        'pph21_deduction_jp_rate' => $result[$x]['pph21_deduction_jp_rate'],
            
                        'pph21_allowance_other' => $result[$x]['pph21_allowance_other'],
                        'pph21_allowance_jamkes' => $result[$x]['pph21_allowance_jamkes'],
                        'pph21_allowance_jkk' => $result[$x]['pph21_allowance_jkk'],
                        'pph21_allowance_jkm' => $result[$x]['pph21_allowance_jkm'],
                        'pph21_allowance_jht' => $result[$x]['pph21_allowance_jht'],
                        'pph21_allowance_jht_payslip' => $result[$x]['pph21_allowance_jht_payslip'],
                        'pph21_allowance_jp' => $result[$x]['pph21_allowance_jp'],
                        'pph21_allowance_jp_payslip' => $result[$x]['pph21_allowance_jp_payslip'],
                        
                        'pph21_allowance_jamkes_rate' => $result[$x]['pph21_allowance_jamkes_rate'],
                        'pph21_allowance_jkk_rate' => $result[$x]['pph21_allowance_jkk_rate'],
                        'pph21_allowance_jkm_rate' => $result[$x]['pph21_allowance_jkm_rate'],
                        'pph21_allowance_jht_rate' => $result[$x]['pph21_allowance_jht_rate'],
                        'pph21_allowance_jht_payslip_rate' => $result[$x]['pph21_allowance_jht_payslip_rate'],
                        'pph21_allowance_jp_rate' => $result[$x]['pph21_allowance_jp_rate'],
                        'pph21_allowance_jp_payslip_rate' => $result[$x]['pph21_allowance_jp_payslip_rate'],
                        // 'pph21_allowance_overtime' => $temp_data_payroll['payroll_allowance_overtime'],
                        'pph21_pkp' => $result[$x]['pph21_pkp'],
                        'pph21_ptkp' => $result[$x]['pph21_ptkp'],
                        
                        'pph21_prevcp_netto' => $result[$x]['pph21_prevcp_netto'],
                        'pph21_prevcp_pph21_total' => $result[$x]['pph21_prevcp_pph21_total'],

                        'pph21_bruto_month' => $result[$x]['pph21_bruto_month'],
                        'pph21_bruto_year' => $result[$x]['pph21_bruto_year'],
                        'pph21_netto_month' => $result[$x]['pph21_netto_month'],
                        'pph21_netto_year' => $result[$x]['pph21_netto_year'],
                        'pph21_total_month' => $result[$x]['pph21_total_month'],
                        'pph21_total_year' => $result[$x]['pph21_total_year'],
            
                        'pph21_ptkp_data' => $result[$x]['pph21_ptkp_data'],
                        'pph21_ptkpdet_data' => $result[$x]['pph21_ptkpdet_data'],
                        'pph21_tarif21_data' => $result[$x]['pph21_tarif21_data'],
                        'pph21_pajakpph21_data' => $result[$x]['pph21_pajakpph21_data'],
                        'pph21_pembetulan' => $result[$x]['pph21_pembetulan'],
                    ]);
    
                    $totalComplete = $totalComplete + 1;
                    $result[$x]['status'] = 'Sukses';
                    $result[$x]['remark'] = '-';
                }
            }


            // end
            HistoryImportModel::where(['historyimport_id' => $getHistory->historyimport_id])
            ->update([
                'historyimport_status' => 'COMPLETED', 
                'historyimport_detail_import' => $totalComplete . ' Sukses, ' . $totalFail . ' Gagal', 
                'historyimport_content' => json_encode($result),
            ]);
            
            DB::commit();
            unlink(public_path('uploads/'). $filename); // remove file
            return response()->json([
                'success' => true,
                'message' => $totalComplete . ' Sukses, ' . $totalFail . ' Gagal',
                'data' => ['result', $result]
            ]);
        } catch (\Exception $e) {
            Log::error('Error occurred: ' . $e->getMessage(). ' Line: '. $e->getLine());
            DB::rollback();
            // something went wrong
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(). ' Line: '. $e->getLine()
            ]);
        }
    }
}

class ChunkReadFilter implements IReadFilter
{
    private $startRow = 0;

    private $endRow = 0;

    /**
     * Set the list of rows that we want to read.
     *
     * @param mixed $startRow
     * @param mixed $chunkSize
     */
    public function setRows($startRow, $chunkSize)
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        //  Only read the heading row, and the rows that are configured in            $this->_startRow and $this->_endRow
        if (($row == 1) || ($row >= $this->startRow && $row <   $this->endRow)) {
            return true;
        }

        return false;
    }
}