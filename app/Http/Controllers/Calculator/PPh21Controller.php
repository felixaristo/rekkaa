<?php

namespace App\Http\Controllers\Calculator;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Mail\RekkaaMail;
use App\Model\Master\BpjsRateModel;
use App\Model\Master\KepemilikanNpwpModel;
use App\Model\Master\PtkpDetailModel;
use App\Model\Master\PtkpModel;
use App\Model\Master\Tarif21Model;
use App\Model\Master\TarifNonNpwpModel;
use App\Model\Master\TunjanganJabatanModel;
use App\Model\Setting\SettingModel;
use App\Model\Transaction\NotificationModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PPh21Controller extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $type = $request->get('type');
        // dd($request->get('test'));
        $data = [
            'title' => 'Kalkulator PPh 21 Karyawan',
            'tarif21' => Tarif21Model::where(['tarif21_active' => 1])->get(),
            'tarif21_nonnpwp' => TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first(),
            'bpjs_rate' => BpjsRateModel::where(['bpjsrate_active' => 1, 'bpjsrate_taxable' => 1])->get(),
            'tunjangan_jabatan' => TunjanganJabatanModel::where(['tunjanganjabatan_active' => 1])->first(),
            'setting_bpjs' => SettingModel::where('setting_active', 1)->where('setting_key', 'ilike', 'BPJS_%')->get(),
            'kepemilikan_npwp' => KepemilikanNpwpModel::select('kepemilikannpwp_code', 'kepemilikannpwp_name', 'kepemilikannpwp_value')->where(['kepemilikannpwp_active' => 1])->get(),
            'ptkp_detail' => PtkpDetailModel::select('ptkpdet_id','ptkpdet_year','ptkpdet_rate_percentage','ptkpdet_category','ptkpdet_rate_month')->where(['ptkpdet_active' => 1])->orderBy('ptkpdet_rate_percentage', 'ASC')->get(),
            'content' => 'visitor.calculator.pph21',
        ];
        
        foreach($data['setting_bpjs'] as &$stbpjs) {
            if($stbpjs->setting_key == 'BPJS_TK_MAX_AMOUNT') {
                $decode_value = json_decode($stbpjs->setting_value);
                
                usort($decode_value, function($a, $b) {
                    return $b->period <=> $a->period;
                });
                // dd($decode_value);

                $stbpjs->setting_value = $decode_value;
            }
        }
        if(session()->get('wajibpajak_current')) {
            if($request->ajax()) {
                return view($data['content'], $data)->render();
            } else {
                return view('user.index', $data);
            }
        } else {
            return view('index', $data);
        }
    }

     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexNonKaryawan(Request $request)
    {
        $type = $request->get('type');

        $data = [
            'title' => 'Kalkulator PPh 21 Non Karyawan',
            'kepemilikan_npwp' => KepemilikanNpwpModel::select('kepemilikannpwp_code', 'kepemilikannpwp_name', 'kepemilikannpwp_value')->where(['kepemilikannpwp_active' => 1])->get(),
            'tarif21' => Tarif21Model::where(['tarif21_active' => 1])->get(),
            'tarif21_nonnpwp' => TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first(),
            'ptkp_detail' => PtkpDetailModel::select('ptkpdet_id','ptkpdet_year','ptkpdet_rate_percentage','ptkpdet_category','ptkpdet_rate_month')->where(['ptkpdet_active' => 1])->orderBy('ptkpdet_rate_percentage', 'ASC')->get(),
            'content' => 'visitor.calculator.pph21-non',
        ];

        if(session()->get('wajibpajak_current')) {
            if($request->ajax()) {
                return view($data['content'], $data)->render();
            } else {
                return view('user.index', $data);
            }
        } else {
            return view('index', $data);
        }
    }

    /**
     * Display a print of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pph21Cetak(Request $request, $params="")
    {
        $params = $request->get('params');
        // dd($render_type);
        // echo $params;
        // echo "<br>";
        try {
            $decode_params = base64_decode($params);
            $explode_params = explode('&',$decode_params);

            $kepemilikan_npwp = '';
            $metode = '';
            $ptkp = '';
            $menggunakan_ter = '';
            $ptkpdetratepercentage = 0;
            $ptkpdetratepermonth = 0;
            $penghasilan_gaji_pokok = '';
            $penghasilan_tunjangan_lainnya = '';
            $jkkrate = 0;
            $isbpjskes = '0';
            $isbpjstk = '0';
            // dd($explode_params);
            foreach($explode_params as $keyval) {
                $explode_keyval = explode('=', $keyval);
                if($explode_keyval[0] == 'knpwp') {
                    $kepemilikan_npwp = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'metode') {
                    $metode = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'menggunakan_ter') {
                    $menggunakan_ter = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'ptkp') {
                    $ptkp = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'penghasilan_gaji_pokok') {
                    $penghasilan_gaji_pokok = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'penghasilan_tunjangan_lainnya') {
                    $penghasilan_tunjangan_lainnya = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'jkkrate') {
                    $jkkrate = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'isbpjskes') {
                    $isbpjskes = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'isbpjstk') {
                    $isbpjstk = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'ptkpdetratepercentage') {
                    $ptkpdetratepercentage = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'ptkpdetratepermonth') {
                    $ptkpdetratepermonth = trim($explode_keyval[1]);
                }
            }
            if(!$kepemilikan_npwp || !$metode || !$ptkp || $penghasilan_gaji_pokok < 0 || $penghasilan_tunjangan_lainnya < 0) {
                return abort(404);
            }
            $kepemilikannpwp = KepemilikanNpwpModel::where(['kepemilikannpwp_code' => $kepemilikan_npwp])->first();
            $ptkp = PtkpModel::where(['ptkp_id' => $ptkp])->first();

            // dd($kepemilikannpwp);
            if(!$kepemilikannpwp || !$ptkp) {
                return abort(404);
            }
            // echo $dasar_pajak.' '.$kepemilikan_npwp.' '.$jenis_transaksi;
            // die;
            // dd($penghasilan_gaji_pokok);
            // let penghasilanJamKesTotal = penghasilanGajiPokok.getNumber() * parseFloat(bpjsRateJamKes.bpjsrate_rate) / 100;
            $ptkp->ptkp_detail = PtkpDetailModel::select('ptkpdet_id','ptkpdet_year','ptkpdet_rate_percentage','ptkpdet_category','ptkpdet_rate_month')->where(['ptkpdet_active' => 1, 'ptkpdet_category' => $ptkp->ptkp_category])->orderBy('ptkpdet_rate_percentage', 'ASC')->get()->toArray();
            $data = [
                'title' => 'Rekkaa - Kalkulator PPh 21 Karyawan',
                'kepemilikannpwp' => $kepemilikannpwp,
                'ptkp' => $ptkp,
                'ptkpdetratepercentage' => $ptkpdetratepercentage,
                'ptkpdetratepermonth' => $ptkpdetratepermonth,
                'metode' => $metode,
                'menggunakan_ter' => $menggunakan_ter,
                'bpjsrate' => bpjsRate(),
                'jkkrate' => $jkkrate,
                'isbpjs' => [$isbpjskes, $isbpjstk],
                'penghasilan_gaji_pokok' => intval($penghasilan_gaji_pokok),
                'penghasilan_tunjangan_lainnya' => intval($penghasilan_tunjangan_lainnya),
                'address' => getSetting('ADDRESS_CALCULATOR'),
                'tunjangan_jabatan' => TunjanganJabatanModel::where(['tunjanganjabatan_active' => 1])->first(),
            ];

            // return view('visitor.calculator.cetak.pph21-cetak', compact('data'));
            $view = view('visitor.calculator.cetak.pph21-cetak', compact('data'))->render();
            $mpdf = new \Mpdf\Mpdf();
            // $mpdf->SetHTMLFooter('<div class="text-left border-footer"><p>Copyright&copy; Rekkaa. All Right Reserved.</p></div>');
            $mpdf->WriteHTML($view);
            return $mpdf->Output();

        } catch(Error $error) {
            abort(404);
        }
    }

    /**
     * Display a print of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function pph21NonCetak(Request $request, $params="")
    {
        $params = $request->get('params');
        try {
            $decode_params = base64_decode($params);
            $explode_params = explode('&',$decode_params);

            $penghasilan_bruto = 0;
            $metode = '';
            $kepemilikan_npwp = '';
            $menggunakan_ter = '';
            $ptkpdetratepercentage = 0;
            $ptkpdetratemonth = 0;
            $sumberpenghasilan = 2;
            $ptkp = 0;
            foreach($explode_params as $keyval) {
                $explode_keyval = explode('=', $keyval);
                if($explode_keyval[0] == 'knpwp') {
                    $kepemilikan_npwp = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'metode') {
                    $metode = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'penghasilan_bruto') {
                    $penghasilan_bruto = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'menggunakan_ter') {
                    $menggunakan_ter = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'menggunakan_ter') {
                    $menggunakan_ter = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'ptkpdetratepercentage') {
                    $ptkpdetratepercentage = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'ptkpdetratemonth') {
                    $ptkpdetratemonth = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'sumberpenghasilan') {
                    $sumberpenghasilan = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'ptkp') {
                    $ptkp = trim($explode_keyval[1]);
                }
            }
            // dd($menggunakan_ter, $ptkpdetratepercentage, $ptkpdetratemonth, $sumberpenghasilan, $ptkprate);
            $kepemilikannpwp = KepemilikanNpwpModel::where(['kepemilikannpwp_code' => $kepemilikan_npwp])->first();
            $ptkp = PtkpModel::where(['ptkp_id' => $ptkp])->first();
            $tarifnonnpwp = TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first();
            // dd($ptkp);
            if(!$kepemilikannpwp || !$metode || !$penghasilan_bruto || !$ptkp) {
                return abort(404);
            }
            
            $ptkp->ptkp_detail = PtkpDetailModel::select('ptkpdet_id','ptkpdet_year','ptkpdet_rate_percentage','ptkpdet_category','ptkpdet_rate_month')->where(['ptkpdet_active' => 1, 'ptkpdet_category' => $ptkp->ptkp_category])->orderBy('ptkpdet_rate_percentage', 'ASC')->get()->toArray();
            // dd($ptkp);
            // if($sumberpenghasilan == 1) {
            //     // dd($penghasilan_bruto, $ptkp->ptkp_rate);
            //     $penghasilan_bruto = $penghasilan_bruto - ($ptkp->ptkp_rate / 12);
            // }
            // dd($penghasilan_bruto);
            $penghasilan_dpp = 0;
            // dd($ptkp);
            $total_pph = perhitunganPPH21Non($penghasilan_bruto, 0, $metode, $sumberpenghasilan, $ptkp, $menggunakan_ter);
            // dd($total_pph);
            if($kepemilikan_npwp == 'NO-NPWP') {
                $total_pph['total_pph'] = ($total_pph['total_pph'] * $tarifnonnpwp->tarifnonnpwp_rate / 100);
            }
            
            if($metode == 'GROSS') {
                $penghasilan_dpp = intval($penghasilan_bruto) * 50 / 100;
            } else { // GROSS UP / NETT
                $penghasilan_dpp = (intval($penghasilan_bruto) + $total_pph['total_pkp']) * 50 / 100;
            }
            
            $data = [
                'title' => 'Rekkaa - Kalkulator PPh 21 Non Karyawan',
                'address' => getSetting('ADDRESS_CALCULATOR'),
                'metode' => $metode,
                'kepemilikannpwp' => $kepemilikannpwp,
                'ptkp' => $ptkp,
                'penghasilan_bruto' => intval($penghasilan_bruto),
                'penghasilan_dpp' => intval($penghasilan_dpp),
                'menggunakan_ter' => intval($menggunakan_ter),
                'ptkpdetratepercentage' => $ptkpdetratepercentage,
                'ptkpdetratemonth' => intval($ptkpdetratemonth),
                'sumberpenghasilan' => intval($sumberpenghasilan),
                'total_pph' => intval($total_pph['total_pph']),
            ];

            // return view('visitor.calculator.cetak.pph21-cetak', compact('data'));
            $view = view('visitor.calculator.cetak.pph21-non-cetak', compact('data'))->render();
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->SetHTMLFooter('<div class="text-left border-footer"><p>Copyright&copy; Rekkaa. All Right Reserved.</p></div>');
            $mpdf->WriteHTML($view);
            return $mpdf->Output();
        } catch(Error $error) {
            abort(404);
        }
    }

    public function cetakPPh21PDF($params="")
    {
        try {
            $decode_params = base64_decode($params);
            $explode_params = explode('&',$decode_params);

            $kepemilikan_npwp = '';
            $metode = '';
            $ptkp = '';
            $menggunakan_ter = '';
            $ptkpdetratepercentage = 0;
            $ptkpdetratepermonth = 0;
            $penghasilan_gaji_pokok = '';
            $penghasilan_tunjangan_lainnya = '';
            $jkkrate = 0;
            $isbpjskes = '0';
            $isbpjstk = '0';
            // dd($explode_params);
            foreach($explode_params as $keyval) {
                $explode_keyval = explode('=', $keyval);
                if($explode_keyval[0] == 'knpwp') {
                    $kepemilikan_npwp = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'metode') {
                    $metode = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'menggunakan_ter') {
                    $menggunakan_ter = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'ptkp') {
                    $ptkp = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'penghasilan_gaji_pokok') {
                    $penghasilan_gaji_pokok = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'penghasilan_tunjangan_lainnya') {
                    $penghasilan_tunjangan_lainnya = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'jkkrate') {
                    $jkkrate = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'isbpjskes') {
                    $isbpjskes = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'isbpjstk') {
                    $isbpjstk = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'ptkpdetratepercentage') {
                    $ptkpdetratepercentage = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'ptkpdetratepermonth') {
                    $ptkpdetratepermonth = trim($explode_keyval[1]);
                }
            }
            if(!$kepemilikan_npwp || !$metode || !$ptkp || $penghasilan_gaji_pokok < 0 || $penghasilan_tunjangan_lainnya < 0) {
                return abort(404);
            }
            $kepemilikannpwp = KepemilikanNpwpModel::where(['kepemilikannpwp_code' => $kepemilikan_npwp])->first();
            $ptkp = PtkpModel::where(['ptkp_id' => $ptkp])->first();

            // dd($kepemilikannpwp);
            if(!$kepemilikannpwp || !$ptkp) {
                return abort(404);
            }
            
            $ptkp->ptkp_detail = PtkpDetailModel::select('ptkpdet_id','ptkpdet_year','ptkpdet_rate_percentage','ptkpdet_category','ptkpdet_rate_month')->where(['ptkpdet_active' => 1, 'ptkpdet_category' => $ptkp->ptkp_category])->orderBy('ptkpdet_rate_percentage', 'ASC')->get()->toArray();
            $data = [
                'title' => 'Rekkaa - Kalkulator PPh 21 Karyawan',
                'kepemilikannpwp' => $kepemilikannpwp,
                'ptkp' => $ptkp,
                'ptkpdetratepercentage' => $ptkpdetratepercentage,
                'ptkpdetratepermonth' => $ptkpdetratepermonth,
                'metode' => $metode,
                'menggunakan_ter' => $menggunakan_ter,
                'bpjsrate' => bpjsRate(),
                'jkkrate' => $jkkrate,
                'isbpjs' => [$isbpjskes, $isbpjstk],
                'penghasilan_gaji_pokok' => intval($penghasilan_gaji_pokok),
                'penghasilan_tunjangan_lainnya' => intval($penghasilan_tunjangan_lainnya),
                'address' => getSetting('ADDRESS_CALCULATOR'),
                'tunjangan_jabatan' => TunjanganJabatanModel::where(['tunjanganjabatan_active' => 1])->first(),
            ];

            $view = view('visitor.calculator.cetak.pph21-cetak', compact('data'))->render();
            $mpdf = new \Mpdf\Mpdf();
            // $mpdf->SetHTMLFooter('<div class="text-left border-footer"><p>Copyright&copy; Rekkaa. All Right Reserved.</p></div>');
            $mpdf->WriteHTML($view);
            return $mpdf->Output('', 'S');

        } catch(Error $error) {
            abort(404);
        }
    }

    public function cetakPPh21NonPDF($params="")
    {
        try {
            $decode_params = base64_decode($params);
            $explode_params = explode('&',$decode_params);

            $penghasilan_bruto = 0;
            $metode = '';
            $kepemilikan_npwp = '';
            $menggunakan_ter = '';
            $ptkpdetratepercentage = 0;
            $ptkpdetratemonth = 0;
            $sumberpenghasilan = 2;
            $ptkp = 0;
            foreach($explode_params as $keyval) {
                $explode_keyval = explode('=', $keyval);
                if($explode_keyval[0] == 'knpwp') {
                    $kepemilikan_npwp = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'metode') {
                    $metode = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'penghasilan_bruto') {
                    $penghasilan_bruto = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'menggunakan_ter') {
                    $menggunakan_ter = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'menggunakan_ter') {
                    $menggunakan_ter = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'ptkpdetratepercentage') {
                    $ptkpdetratepercentage = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'ptkpdetratemonth') {
                    $ptkpdetratemonth = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'sumberpenghasilan') {
                    $sumberpenghasilan = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'ptkp') {
                    $ptkp = trim($explode_keyval[1]);
                }
            }
            // dd($menggunakan_ter, $ptkpdetratepercentage, $ptkpdetratemonth, $sumberpenghasilan, $ptkprate);
            $kepemilikannpwp = KepemilikanNpwpModel::where(['kepemilikannpwp_code' => $kepemilikan_npwp])->first();
            $ptkp = PtkpModel::where(['ptkp_id' => $ptkp])->first();
            $tarifnonnpwp = TarifNonNpwpModel::where(['tarifnonnpwp_active' => 1, 'tarifnonnpwp_taxtype' => 'PPh21'])->first();
            if(!$kepemilikannpwp || !$metode || !$penghasilan_bruto || !$ptkp) {
                return abort(404);
            }
            
            $ptkp->ptkp_detail = PtkpDetailModel::select('ptkpdet_id','ptkpdet_year','ptkpdet_rate_percentage','ptkpdet_category','ptkpdet_rate_month')->where(['ptkpdet_active' => 1, 'ptkpdet_category' => $ptkp->ptkp_category])->orderBy('ptkpdet_rate_percentage', 'ASC')->get()->toArray();
            // dd($ptkp);
            // if($sumberpenghasilan == 1) {
            //     // dd($penghasilan_bruto, $ptkp->ptkp_rate);
            //     $penghasilan_bruto = $penghasilan_bruto - ($ptkp->ptkp_rate / 12);
            // }
            // dd($penghasilan_bruto);
            $penghasilan_dpp = 0;
            // dd($ptkp);
            $total_pph = perhitunganPPH21Non($penghasilan_bruto, 0, $metode, $sumberpenghasilan, $ptkp, $menggunakan_ter);
            // dd($total_pph);
            if($kepemilikan_npwp == 'NO-NPWP') {
                $total_pph['total_pph'] = ($total_pph['total_pph'] * $tarifnonnpwp->tarifnonnpwp_rate / 100);
            }
            
            if($metode == 'GROSS') {
                $penghasilan_dpp = intval($penghasilan_bruto) * 50 / 100;
            } else { // GROSS UP / NETT
                $penghasilan_dpp = (intval($penghasilan_bruto) + $total_pph) * 50 / 100;
            }
            $data = [
                'title' => 'Rekkaa - Kalkulator PPh 21 Non Karyawan',
                'address' => getSetting('ADDRESS_CALCULATOR'),
                'metode' => $metode,
                'kepemilikannpwp' => $kepemilikannpwp,
                'ptkp' => $ptkp,
                'penghasilan_bruto' => intval($penghasilan_bruto),
                'penghasilan_dpp' => intval($penghasilan_dpp),
                'menggunakan_ter' => intval($menggunakan_ter),
                'ptkpdetratepercentage' => intval($ptkpdetratepercentage),
                'ptkpdetratemonth' => intval($ptkpdetratemonth),
                'sumberpenghasilan' => intval($sumberpenghasilan),
                'total_pph' => intval($total_pph['total_pph']),
            ];

            // return view('visitor.calculator.cetak.pph21-cetak', compact('data'));
            $view = view('visitor.calculator.cetak.pph21-non-cetak', compact('data'))->render();
            $mpdf = new \Mpdf\Mpdf();
            $mpdf->SetHTMLFooter('<div class="text-left border-footer"><p>Copyright&copy; Rekkaa. All Right Reserved.</p></div>');
            $mpdf->WriteHTML($view);
            return $mpdf->Output('', 'S');

        } catch(Error $error) {
            abort(404);
        }
    }
    
    public function sendEmail(Request $request)
    {
        // dd(request()->get('useraccess_permissions'));
        if(!session()->get('wajibpajak_current')) {
            abort(404);
        }

        $email = $request->post('email');
        $params = $request->post('params');
        $tipekalkulator = $request->post('tipekalkulator');
        $permission_code = ($tipekalkulator == 'nonkaryawan') ? 'CALCULATOR_PPH21_NONKARYAWAN_SEND_EMAIL' : 'CALCULATOR_PPH21_KARYAWAN_SEND_EMAIL';
        // // check max per day
        // $notificationTotal = NotificationModel::where([
        //     'ms_permission_code' => $permission_code,
        //     'ms_user_id' => session()->get('user_data')['user_id'],
        //     'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        // ])->where(DB::raw('DATE(notification_created_at)'), '=', date('Y-m-d'))->count();

        // melebihi batas
        // if($notificationTotal >= request()->get('useraccess_permissions')->Q_SEND_EMAIL) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Akses ditolak! Batas pengiriman maksimal <b>'.request()->get('useraccess_permissions')->Q_SEND_EMAIL.'</b> email perhari.'
        //     ]);
        // }
        // End Check Subscription Permission

        // dd($this->pph4a2CetakPDF($params));
        // dd($params);
        if($tipekalkulator == 'nonkaryawan') {
            $options = [
                'from' => env("MAIL_FROM_ADDRESS"),
                'subject' => 'Rekkaa - Kalkulator PPh 21 Non Karyawan',
                'attachment' => [
                    // 'file' => $this->cetakPPh21NonPDF($params),
                    'file' => 'App\Http\Controllers\Calculator\PPh21Controller',
                    'method' => 'cetakPPh21NonPDF',
                    'title' => 'Rekkaa - Kalkulator PPh 21 Non Karyawan.pdf'
                ],
                'data' => [
                    'tipe' => 'PPh 21 Non Karyawan'
                ],
                'view' => 'email.email-kalkulator'
            ];
        } else {
            $options = [
                'from' => env("MAIL_FROM_ADDRESS"),
                'subject' => 'Rekkaa - Kalkulator PPh 21',
                'attachment' => [
                    // 'file' => $this->cetakPPh21PDF($params),
                    'file' => 'App\Http\Controllers\Calculator\PPh21Controller',
                    'method' => 'cetakPPh21PDF',
                    'title' => 'Rekkaa - Kalkulator PPh 21.pdf'
                ],
                'data' => [
                    'tipe' => 'PPh 21 Karyawan'
                ],
                'view' => 'email.email-kalkulator'
            ];
        }
        // print_r($options);
        try {
            // insert tr_notification
            $notification = NotificationModel::create([
                'notification_title' => $options['subject'],
                'notification_type' => 'EMAIL',
                'notification_from' => $options['from'],
                'notification_to' => $email,
                'ms_user_id' => session()->get('user_data')['user_id'],
                'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                'notification_view' => 'email.email-kalkulator',
                'notification_attachment' => json_encode([
                    'fnlocation' => $options['attachment']['file'],
                    'fnmethod' => $options['attachment']['method'],
                    'params' => $params,
                    'title' => $options['attachment']['title']
                ]),
                'notification_data' => json_encode([
                    'tipe' => $options['data']['tipe']
                ]),
                'ms_permission_code' => $permission_code
            ]);
            // Mail::to($email)->send(new RekkaaMail($options));
            dispatch(new SendMailJob($notification->notification_id));
            return response()->json([
                'success' => true,
                'message' => $options['subject']." berhasil dikirim!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
