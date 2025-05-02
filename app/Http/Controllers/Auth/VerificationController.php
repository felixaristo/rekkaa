<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
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
        // $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function index(Request $request)
    {   
        $verified_token = $request->get('token');
        // dd($verified_token);
        if(!$verified_token) {
            abort(404);
        }
        $user = User::where([
            'user_verified_token' => $verified_token,
            'user_email_verified_at' => null,
            'user_active' => 1,
        ])->first();
        if(!$user){
            abort(404);
        }

        $user->user_email_verified_at = date('Y-m-d H:i:s');
        $user->save();
        
        $data = [
            'title' => 'Rekkaa - Verifikasi Akun Berhasil!'
        ];
        return view('auth/verifikasi-akun', $data);
    }
}
