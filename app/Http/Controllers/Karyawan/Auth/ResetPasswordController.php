<?php

namespace App\Http\Controllers\Karyawan\Auth;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\User;
use Error;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/karyawan';

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
        $forgot_token = $request->get('token');
        // dd($forgot_token);
        if(!$forgot_token) {
            abort(404);
        }
        $karyawan = KaryawanModel::where([
            'karyawan_forgot_password' => $forgot_token,
            'karyawan_isuser' => 1,
            'karyawan_active' => 1,
        ])->first();
        if(!$karyawan){
            abort(404);
        }
        
        $data = [
            'title' => 'Rekkaa - Halaman Reset Password'
        ];
        return view('karyawan/auth/reset-password', $data);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function reset(Request $request)
    {   
        $forgot_token = $request->get('token');
        if(!$forgot_token) {
            abort(404);
        }

        $this->validate($request, [
            'password' => 'required|required_with:konf_password|same:konf_password',
            'konf_password' => 'required',
        ], [
            'password.required' => 'Password wajib diisi!',
            'konf_password.required' => 'Konfirmasi Password wajib diisi!',
            'konf_password.same' => 'Konfirmasi Password tidak sama!',
        ]);

        try {
            $karyawan = KaryawanModel::where([
                'karyawan_forgot_password' => $forgot_token,
                'karyawan_isuser' => 1,
                'karyawan_active' => 1,
            ])->first();
            if(!$karyawan){
                return response()->json([
                    'success' => false,
                    'message' => 'Reset password gagal. Token tidak valid!',
                ]);
            }
            // dd($user);
            // $karyawan->update([
            //     'karyawan_password' => Hash::make($request->input('password')),
            //     'karyawan_forgot_password' => null,
            // ]);
            // update all password relate to this email
            KaryawanModel::where([
                'karyawan_email' => $karyawan->karyawan_email,
                'karyawan_isuser' => 1,
                'karyawan_active' => 1,
            ])->update([
                'karyawan_password' => Hash::make($request->input('password')),
                'karyawan_forgot_password' => null,
            ]);

            // Mail::to($user->user_email)->send(new RekkaaMail($options));
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan kata sandi berhasil! Silahkan login ke akun anda.',
            ]);

        } catch(Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
          
    }
}
