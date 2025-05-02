<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\User;
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
    protected $redirectTo = '/home';

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

        return redirect()->route('user.page');
    }

    // /**
    //  * Create a new controller instance.
    //  *
    //  * @return void
    //  */
    // public function login(Request $request)
    // {   
    //     $input = $request->all();
  
    //     // dd($input);
    //     $this->validate($request, [
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);
  
    //     // $fieldType = filter_var($request->email, FILTER_VALIDATE_EMAIL);
    //     if(auth()->attempt(
    //             array(
    //                 'user_email' => $input['email'], 
    //                 'user_password' => $input['password']
    //             )
    //         )
    //     )
    //     {
    //         dd(auth()->user());
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Login berhasil!',
    //             'data' => [
    //                 'role' => auth()->user()->roles->pluck('name')[0]
    //             ]
    //         ]);
    //     }else{
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Login gagal! Silahkan gunakan email atau password lain.',
    //         ]);
    //     }
    // }

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
        $user = User::with(['userorder'])->where(['user_email' => $input['email']])->first();
        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan! Silahkan daftar terlebih dahulu.',
            ]);
        }

        if(!Hash::check($input['password'], $user->user_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal! Silahkan gunakan email atau password lain.',
            ]);
        }

        if($user->user_active == 0) {
            // dd($user->userorder);
            session()->put('user_id_continue', $user->user_id);
            return response()->json([
                'success' => false,
                'message' => 'Silahkan lanjutkan proses registrasi.',
                'data' => [
                    'status' => 0,
                    'subscription_id' => $user->userorder->ms_subscription_id,
                ],
            ]);
        }
        // insert wajib pajak
        $user_id = $user->user_id;
        $userwajibpajak = MrUserWajibPajakModel::with(['wajibpajak'])->where([
            'userwajibpajak_active' => 1,
            'ms_user_id' => $user_id,
        ])->first();
        $wajibpajak = ($userwajibpajak) ? $userwajibpajak->wajibpajak : null;

        // user session
        $session_data = [
            'user_id' => $user_id,
            'user_email' => $user->user_email,
            'user_name' => ($userwajibpajak) ? $userwajibpajak->userwajibpajak_name : '',
        ];
        session()->put('user_data', $session_data);

        // wajib pajak session
        if($wajibpajak) {
            $wajibpajak_id = $wajibpajak->wajibpajak_id;

            session()->put('wajibpajak_current', [
                'wajibpajak_id' => $wajibpajak_id,
                'wajibpajak_name' => $wajibpajak->wajibpajak_name,
                'wajibpajak_npwp' => $wajibpajak->wajibpajak_npwp,
                'wajibpajak_type' => $wajibpajak->wajibpajak_type,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'data' => $session_data
        ]);
    }

    public function logout () {
        session()->forget('user_data');
        session()->forget('wajibpajak_current');
        // redirect to homepage
        return redirect('/login');
    }
}
