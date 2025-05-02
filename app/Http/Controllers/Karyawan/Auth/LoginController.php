<?php

namespace App\Http\Controllers\Karyawan\Auth;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    public function authenticated(Request $request, $user)
    {
        // if ($user->hasRole('admin')) {
        //     return redirect()->route('admin.page');
        // }

        return redirect()->route('karyawan.page');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function login(Request $request)
    {   
        $input = $request->all();
  
        // dd($input);
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
  
        // $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL);
        $karyawan = KaryawanModel::select("karyawan_id", "karyawan_email", "karyawan_name"
        , "karyawan_password", "ms_user_id", "ms_wajibpajak_id")
        ->with(['wajibpajak'])->where([
            'karyawan_email' => $input['email'],
            'karyawan_isuser' => 1,
            'karyawan_active' => 1,
        ])->get();
        // dd($karyawan);
        if(count($karyawan) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan! Silahkan hubungi admin terlebih dahulu.',
            ]);
        }
        // dd($karyawan);
        
        $karyawan_password = $karyawan[0]->karyawan_password;
        if(!Hash::check($input['password'], $karyawan_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal! Silahkan gunakan email atau password lain.',
            ]);
        }
        // dd($karyawan);

        $karyawan_data = [];
        foreach($karyawan as $kr) {
            array_push($karyawan_data, [
                'karyawan_id' => $kr->karyawan_id,
                'karyawan_name' => $kr->karyawan_name,
                'karyawan_email' => $kr->karyawan_email,
                'wajibpajak_name' => $kr->wajibpajak->wajibpajak_name,
                'wajibpajak_npwp' => $kr->wajibpajak->wajibpajak_npwp,
            ]);
        }
            
        if(count($karyawan) > 1) { // multiple account
            session()->put('karyawan_multipleaccount', $karyawan_data);
        } else { // single account
            $karyawan = $karyawan[0];
            // user session
            $session_data = [
                'wajibpajak_id' => $karyawan->ms_wajibpajak_id,
                'user_id' => $karyawan->ms_user_id,
                'karyawan_id' => $karyawan->karyawan_id,
                'karyawan_name' => $karyawan->karyawan_name,
            ];
            session()->put('karyawan_data', $session_data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'data' => [
                'karyawan' => $karyawan_data
            ]
        ]);
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function getAccess(Request $request)
    {   
        $karyawan_id = $request->input('id');
        // dd($input);
        $this->validate($request, [
            'id' => 'required',
        ]);

        $multipleaccount_session = session()->get('karyawan_multipleaccount');
        if(!$multipleaccount_session) {
            abort(404);
        }

        // dd($multipleaccount_session);
        $isaccount_exist = false;
        foreach($multipleaccount_session as $account) {
            if($account['karyawan_id'] == $karyawan_id) {
                $isaccount_exist = true;
                break;
            }
        }
        if($isaccount_exist == false) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan! Silahkan hubungi admin terlebih dahulu.',
            ]);
        }

        $karyawan = KaryawanModel::select("karyawan_id", "karyawan_email", "karyawan_name"
        , "karyawan_password", "ms_user_id", "ms_wajibpajak_id")->where([
            'karyawan_id' => $karyawan_id,
            'karyawan_isuser' => 1,
            'karyawan_active' => 1,
        ])->first();

        if(!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak ditemukan! Silahkan hubungi admin terlebih dahulu.',
            ]);
        }

        // user session
        $session_data = [
            'wajibpajak_id' => $karyawan->ms_wajibpajak_id,
            'user_id' => $karyawan->ms_user_id,
            'karyawan_id' => $karyawan->karyawan_id,
            'karyawan_name' => $karyawan->karyawan_name,
        ];
        session()->put('karyawan_data', $session_data);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
        ]);
    }

    public function logout () {
        session()->forget('karyawan_data');
        
        // redirect to homepage
        return redirect('/karyawan/login');
    }
}
