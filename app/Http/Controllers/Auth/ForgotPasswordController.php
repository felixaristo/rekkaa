<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Mail\RekkaaMail;
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
    function forgot(Request $request)
    {   
        // dd(env("MAIL_FROM_ADDRESS"));
        $this->validate($request, [
            'user_email' => 'required',
        ], [
            'user_email.required' => 'Email wajib diisi!',
        ]);

        try {
            $user = User::where([
                'user_email' => $request->input('user_email'),
                'user_active' => 1,
            ])->first();
            if(!$user){
                return response()->json([
                    'success' => false,
                    'message' => 'Reset password gagal. Email tidak ditemukan!',
                ]);
            }
            // dd($user);
            $generate_token = Hash::make('FGP'.$user->user_id.uniqid());
            $user->update([
                'user_forgot_token' => $generate_token,
            ]);

            // $options = [
            //     'from' => env("MAIL_FROM_ADDRESS"),
            //     'subject' => 'Rekkaa - Lupa Password',
            //     'data' => [
            //         'user' => $user
            //     ],
            //     'view' => 'email.email-lupa-password'
            // ];

            // Mail::to($user->user_email)->send(new RekkaaMail($options));

            // insert tr_notification
            $notification = NotificationModel::create([
                'notification_title' => 'Rekkaa - Lupa Password',
                'notification_type' => 'EMAIL',
                'notification_from' => env("MAIL_FROM_ADDRESS"),
                'notification_to' => $user->user_email,
                'ms_user_id' => $user->user_id,
                'notification_view' => 'email.email-lupa-password',
            ]);

            dispatch(new SendMailJob($notification->notification_id));

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
