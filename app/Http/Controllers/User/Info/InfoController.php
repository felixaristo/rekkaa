<?php
namespace App\Http\Controllers\User\Info;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use App\Model\Transaction\NotificationModel;
use App\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InfoController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getwajibpajakgroup(Request $request)
    {
        return [
            'success' => true,
            'data' => getWajibPajakGroup()
        ];
    }

    /**
     * Activate of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function wajibpajakgroupactivate(Request $request)
    {
        $wajibpajak_id = intval($request->get('wp_id'));
        if(!$wajibpajak_id) {
            return redirect()->back();
        }
        $wajibpajak = getWajibPajakGroup($wajibpajak_id);
        if(!$wajibpajak) {
            return redirect()->back();
        };
        // dd($wajibpajak);
        $session_user = session()->get('user_data');
        $session_user['user_name'] = $wajibpajak->userwajibpajak_name;
        session()->put('user_data', $session_user);
        session()->put('wajibpajak_current', [
            'wajibpajak_id' => $wajibpajak->wajibpajak_id,
            'wajibpajak_name' => $wajibpajak->wajibpajak_name,
            'wajibpajak_npwp' => $wajibpajak->wajibpajak_npwp,
            'wajibpajak_type' => $wajibpajak->wajibpajak_type,
        ]);
       
        return redirect('/user/beranda');
    }

    /**
     * Display a user of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getkaryawanbyemail(Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawan_id = $request->input('karyawan_id');

        $this->validate($request, [
            'karyawan_email' => ['required','email',
                Rule::unique('ms_karyawan')->where(function ($query) use($user_id, $wajibpajak_id, $karyawan_id) {
                    if($karyawan_id) {
                        return $query->where([
                            'ms_user_id' => $user_id,
                            'ms_wajibpajak_id' => $wajibpajak_id
                        ])->where('karyawan_id', '<>', $karyawan_id);
                    } else {
                        return $query->where([
                            'ms_user_id' => $user_id,
                            'ms_wajibpajak_id' => $wajibpajak_id
                        ]);   
                    }
                }),
            ],
        ], 
        [
            'karyawan_email.required' => 'Email wajib diisi!',
            'karyawan_email.email' => 'Format email tidak sesuai!',
            'karyawan_email.unique' => 'Email sudah digunakan. Silahkan gunakan email lainnya!',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Email available!'
        ]);
    }

    /**
     * Display a user of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getkaryawanbykode(Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawan_id = $request->input('karyawan_id');

        $this->validate($request, [
            'karyawan_enid' => [
                'required',
                Rule::unique('ms_karyawan')->where(function ($query) use($user_id, $wajibpajak_id, $karyawan_id) {
                    if($karyawan_id) {
                        return $query->where([
                            'ms_user_id' => $user_id,
                            'ms_wajibpajak_id' => $wajibpajak_id
                        ])->where('karyawan_id', '<>', $karyawan_id);
                    } else {
                        return $query->where([
                            'ms_user_id' => $user_id,
                            'ms_wajibpajak_id' => $wajibpajak_id
                        ]);   
                    }
                }),
            ],
        ], 
        [
            'karyawan_enid.required' => 'ID Karyawan wajib diisi!',
            'karyawan_enid.unique' => 'ID Karyawan sudah digunakan. Silahkan gunakan ID Karyawan lainnya!',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'ID available!'
        ]);
    }

    /**
     * Display a user of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getkaryawanbynik(Request $request, $user_id = null, $wajibpajak_id = null)
    {
        $user_id = ($user_id) ? $user_id : session()->get('user_data')['user_id'];
        $wajibpajak_id = ($wajibpajak_id) ? $wajibpajak_id : session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawan_id = $request->input('karyawan_id');

        $validator = Validator::make( $request->all(), [
            'karyawan_nik' => [
                'required',
                Rule::unique('ms_karyawan')->where(function ($query) use($user_id, $wajibpajak_id, $karyawan_id) {
                    if($karyawan_id) {
                        return $query->where([
                            'ms_user_id' => $user_id,
                            'ms_wajibpajak_id' => $wajibpajak_id
                        ])->where('karyawan_id', '<>', $karyawan_id);
                    } else {
                        return $query->where([
                            'ms_user_id' => $user_id,
                            'ms_wajibpajak_id' => $wajibpajak_id
                        ]);   
                    }
                }),
            ],
        ], 
        [
            'karyawan_nik.required' => 'NIK Karyawan wajib diisi!',
            'karyawan_nik.unique' => 'NIK Karyawan sudah digunakan. Silahkan gunakan NIK lainnya!',
        ]);
        // $validator = Validator::make( $request->all(), $rules, $messages );

        if ( $validator->fails() ) 
        {
            return response()->json([
                'success' => false, 
                'message' => $validator->errors()->first()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'NIK available!'
        ]);
    }

    /**
     * Display a user of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getkaryawanbynpwp(Request $request, $user_id = null, $wajibpajak_id = null)
    {
        $user_id = ($user_id) ? $user_id : session()->get('user_data')['user_id'];
        $wajibpajak_id = ($wajibpajak_id) ? $wajibpajak_id : session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawan_id = $request->input('karyawan_id');
        
        $validator = Validator::make( $request->all(), [
            'karyawan_npwp' => [
                'required',
                Rule::unique('ms_karyawan')->where(function ($query) use($user_id, $wajibpajak_id, $karyawan_id) {
                    if($karyawan_id) {
                        return $query->where([
                            'ms_user_id' => $user_id,
                            'ms_wajibpajak_id' => $wajibpajak_id,
                        ])->where('karyawan_id', '<>', $karyawan_id)->where('karyawan_npwp', '<>', '00.000.000.0-000.000');
                    } else {
                        return $query->where([
                            'ms_user_id' => $user_id,
                            'ms_wajibpajak_id' => $wajibpajak_id
                        ])->where('karyawan_npwp', '<>', '00.000.000.0-000.000');   
                    }
                }),
            ],
        ], 
        [
            'karyawan_npwp.required' => 'NPWP Karyawan wajib diisi!',
            'karyawan_npwp.unique' => 'NPWP Karyawan sudah digunakan. Silahkan gunakan NPWP lainnya!',
        ]);

        if ( $validator->fails() ) 
        {
            return response()->json([
                'success' => false, 
                'message' => $validator->errors()->first()
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'NPWP available!'
        ]);
    }

    /**
     * Send an email verification to user of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendemailverification(Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $current_date = date('Y-m-d');

        // check if user verified
        $user_verified = User::where(['user_id' => $user_id])->first();
        if($user_verified->user_email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'User sudah terverifikasi!'
            ]);
        }
        // check notification
        $notification = NotificationModel::where(['ms_user_id' => $user_id, 'notification_text' => 'EMAIL_VERIFICATION'])
                        ->whereRaw("(TO_CHAR(notification_created_at, 'YYYY-MM-DD') = ?)", [$current_date])->first();

        if($notification) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan email verifikasi hanya bisa 1 kali sehari!'
            ]);
        }

        $generate_token = Hash::make('VRA'.$request->input('user_email').date('Y-m-dhms'));
        User::where(['user_id' => $user_id])->update(['user_verified_token' => $generate_token,]);
                
        // insert tr_notification verification
        $notification = NotificationModel::create([
            'notification_title' => 'Verifikasi Akun',
            'notification_type' => 'EMAIL',
            'notification_text' => 'EMAIL_VERIFICATION',
            'notification_from' => env("MAIL_FROM_ADDRESS"),
            'notification_to' => $user_verified->user_email,
            'ms_user_id' => $user_verified->user_id,
            'notification_view' => 'email.email-verifikasi',
        ]);
        dispatch(new SendMailJob($notification->notification_id));

        return response()->json([
            'success' => true,
            'message' => 'Email verifikasi berhasil dikirim!'
        ]);
    }
}