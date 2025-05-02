<?php

namespace App\Http\Controllers\Karyawan\Auth;

use App\Http\Controllers\Controller;
use App\Mail\RekkaaMail;
use App\Model\Master\KaryawanModel;
use App\Model\Transaction\NotificationModel;
use App\User;
use Error;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function index(Request $request)
    {   
        $data = [
            'title' => 'Rekkaa - Halaman Lupa Password'
        ];
        return view('karyawan/auth/lupa-password', $data);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function forgot(Request $request)
    {   
        // dd(env("MAIL_FROM_ADDRESS"));
        $this->validate($request, [
            'user_email' => 'required',
        ], [
            'user_email.required' => 'Email wajib diisi!',
        ]);

        try {
            $karyawan = KaryawanModel::where([
                'karyawan_email' => $request->input('user_email'),
                'karyawan_isuser' => 1,
                'karyawan_active' => 1,
            ])->first();
            if(!$karyawan){
                return response()->json([
                    'success' => false,
                    'message' => 'Reset password gagal. Email tidak ditemukan!',
                ]);
            }
            // dd($user);
            $generate_token = Hash::make('FGP'.$karyawan->karyawan_id.uniqid());
            $karyawan->update([
                'karyawan_forgot_password' => $generate_token,
            ]);

            // insert tr_notification
            NotificationModel::create([
                'notification_title' => 'Rekkaa - Lupa Password',
                'notification_type' => 'EMAIL',
                'notification_from' => env("MAIL_FROM_ADDRESS"),
                'notification_to' => $karyawan->karyawan_email,
                'ms_user_id' => $karyawan->ms_user_id,
                'notification_view' => 'email.email-lupa-password-karyawan',
                'notification_data' => json_encode([
                    'entity' => [
                        'name' => null,
                        'user_name' => $karyawan->karyawan_name,
                        'forgot_token' => $karyawan->karyawan_forgot_password,
                    ]
                ])
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Silahkan cek email anda untuk melakukan reset password!',
            ]);

        } catch(Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
          
    }
}
