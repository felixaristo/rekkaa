<?php

namespace App\Http\Controllers\Calculator;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Mail\RekkaaMail;
use App\Model\Master\KepemilikanNpwpModel;
use App\Model\Master\ObjekPajakModel;
use App\Model\Master\Tarif21Model;
use App\Model\Transaction\NotificationModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Pph4a2Controller extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd(request()->get('useraccess_permissions')->Q_SEND_EMAIL);
        $data = [
            'title' => 'Kalkulator PPh pasal 4 ayat 2',
            'content' => 'visitor.calculator.pph4a2',
            'kepemilikan_npwp' => KepemilikanNpwpModel::select('kepemilikannpwp_code', 'kepemilikannpwp_name', 'kepemilikannpwp_value')->where(['kepemilikannpwp_active' => 1])->get()
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
    public function cetak(Request $request, $params="")
    {
        $params = $request->get('params');
        // dd($render_type);
        // echo $params;
        // echo "<br>";
        try {
            $decode_params = base64_decode($params);
            $explode_params = explode('&',$decode_params);
            // $kepemilikan_npwp = $decode_params('knpwp');
            // $jenis_transaksi = $request->get('jt');
            // $dasar_pajak = $request->get('dp');

            $kepemilikan_npwp = '';
            $jenis_transaksi = '';
            $dasar_pajak = '';
            foreach($explode_params as $keyval) {
                $explode_keyval = explode('=', $keyval);
                if($explode_keyval[0] == 'knpwp') {
                    $kepemilikan_npwp = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'jt') {
                    $jenis_transaksi = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'dp') {
                    $dasar_pajak = trim($explode_keyval[1]);
                }

                if($explode_keyval[0] == 'kt') {
                    $kategori = trim($explode_keyval[1]);
                }
            }

            if(!$kepemilikan_npwp || !$jenis_transaksi || !$dasar_pajak) {
                return abort(404);
            }
            $kepemilikannpwp = KepemilikanNpwpModel::where(['kepemilikannpwp_code' => $kepemilikan_npwp])->first();
            $jenistransaksi = ObjekPajakModel::where(['objekpajak_code' => $jenis_transaksi])->first();

            // dd($kepemilikan_npwp);
            if(!$kepemilikannpwp || !$jenistransaksi) {
                return abort(404);
            }
            // echo $dasar_pajak.' '.$kepemilikan_npwp.' '.$jenis_transaksi;
            // die;
            $data = [
                'title' => 'Rekkaa - Kalkulator PPh Pasal 4 ayat 2',
                'address' => getSetting('ADDRESS_CALCULATOR'),
                'kepemilikannpwp' => $kepemilikannpwp,
                'jenistransaksi' => $jenistransaksi,
                'dasar_pajak' => $dasar_pajak
            ];

            $view = view('visitor.calculator.cetak.pph4a2-cetak', compact('data'))->render();
            $mpdf = new \Mpdf\Mpdf();
            // $mpdf->SetHTMLFooter('<div class="text-left border-footer"><p>Copyright&copy; Rekkaa. All Right Reserved.</p></div>');
            $mpdf->WriteHTML($view);
            return $mpdf->Output();

            

        } catch(Error $error) {
            abort(404);
        }
    }

    public function cetakPDF($params="")
    {
        // dd($render_type);
        // echo $params;
        // echo "<br>";
        try {
            $decode_params = base64_decode($params);
            $explode_params = explode('&',$decode_params);
            // $kepemilikan_npwp = $decode_params('knpwp');
            // $jenis_transaksi = $request->get('jt');
            // $dasar_pajak = $request->get('dp');

            $kepemilikan_npwp = '';
            $jenis_transaksi = '';
            $dasar_pajak = '';
            foreach($explode_params as $keyval) {
                $explode_keyval = explode('=', $keyval);
                if($explode_keyval[0] == 'knpwp') {
                    $kepemilikan_npwp = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'jt') {
                    $jenis_transaksi = trim($explode_keyval[1]);
                }
                if($explode_keyval[0] == 'dp') {
                    $dasar_pajak = trim($explode_keyval[1]);
                }
            }

            if(!$kepemilikan_npwp || !$jenis_transaksi || !$dasar_pajak) {
                return abort(404);
            }
            $kepemilikannpwp = KepemilikanNpwpModel::where(['kepemilikannpwp_code' => $kepemilikan_npwp])->first();
            $jenistransaksi = ObjekPajakModel::where(['objekpajak_code' => $jenis_transaksi])->first();

            // dd($kepemilikan_npwp);
            if(!$kepemilikannpwp || !$jenistransaksi) {
                return abort(404);
            }
            // echo $dasar_pajak.' '.$kepemilikan_npwp.' '.$jenis_transaksi;
            // die;
            $data = [
                'title' => 'Rekkaa - Kalkulator PPh Pasal 4 ayat 2',
                'address' => getSetting('ADDRESS_CALCULATOR'),
                'kepemilikannpwp' => $kepemilikannpwp,
                'jenistransaksi' => $jenistransaksi,
                'dasar_pajak' => $dasar_pajak
            ];

            // if($render_type === 'system') {
                $view = view('visitor.calculator.cetak.pph4a2-cetak', compact('data'))->render();
                $mpdf = new \Mpdf\Mpdf();
                // $mpdf->showImageErrors = true;
                $mpdf->SetHTMLFooter('<div class="text-left border-footer"><p>Copyright&copy; Rekkaa. All Right Reserved.</p></div>');
                $mpdf->WriteHTML($view);
                return $mpdf->Output('', 'S');
                // return $view;
            // } else {
            //     return view('visitor.calculator.cetak.pph4a2-cetak', compact('data'));
            // }

            

        } catch(Error $error) {
            abort(404);
        }
    }
    
    public function sendEmail(Request $request)
    {
        if(!session()->get('wajibpajak_current')) {
            abort(404);
        }

        $email = $request->post('email');
        $params = $request->post('params');
        // dd($this->pph4a2CetakPDF($params));
        // dd($params);
        // $options = [
        //     'from' => env("MAIL_FROM_ADDRESS"),
        //     'subject' => 'Rekkaa - Kalkulator PPh Pasal 4 ayat 2',
        //     'attachment' => [
        //         'file' => $this->cetakPDF($params),
        //         'title' => 'Rekkaa - Kalkulator PPh Pasal 4 ayat 2.pdf'
        //     ],
        //     'data' => [
        //         'tipe' => 'PPh 4 ayat 2'
        //     ],
        //     'view' => 'email.email-kalkulator'
        // ];
        // print_r($options);
        try {
            // insert tr_notification
            $notification = NotificationModel::create([
                'notification_title' => 'Rekkaa - Kalkulator PPh Pasal 4 ayat 2',
                'notification_type' => 'EMAIL',
                'notification_from' => env("MAIL_FROM_ADDRESS"),
                'notification_to' => $email,
                'ms_user_id' => session()->get('user_data')['user_id'],
                'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
                'notification_view' => 'email.email-kalkulator',
                'notification_attachment' => json_encode([
                    'fnlocation' => 'App\Http\Controllers\Calculator\Pph4a2Controller',
                    'fnmethod' => 'cetakPDF',
                    'params' => $params,
                    'title' => 'Rekkaa - Kalkulator PPh Pasal 4 ayat 2.pdf'
                ]),
                'notification_data' => json_encode([
                    'tipe' => 'PPh 4 ayat 2'
                ]),
                'ms_permission_code' => 'CALCULATOR_PPH4a2_SEND_EMAIL'
            ]);
            // Mail::to($email)->send(new RekkaaMail($options));
            dispatch(new SendMailJob($notification->notification_id));
            return response()->json([
                'success' => true,
                'message' => "Rekkaa - Kalkulator PPh Pasal 4 ayat 2 berhasil dikirim!"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
