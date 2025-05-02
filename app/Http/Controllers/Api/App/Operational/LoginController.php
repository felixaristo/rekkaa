<?php

namespace App\Http\Controllers\Api\App\Operational;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Master\AdminModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Facades\JWTFactory;
use Tymon\JWTAuth\JWT;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        //set validation
        $validator = Validator::make($request->all(), [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        //if validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data invalid',
                'data' => $validator->errors()
            ], 422);
        }

        $email = $request->get('email');
        $password = $request->get('password');
        $admin = AdminModel::where(['admin_email' => $email, 'admin_active' => '1'])->first();
        if(!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
            ], 400);
        }
        if(!Hash::check($password, $admin->admin_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal! Silahkan gunakan email atau password lain.',
            ], 400);
        }
        //get credentials from request
        // $credentials = $request->only('email', 'password');

        $credentials = [
            'data' => [
                'id' => $admin->admin_id,
                'email' => $admin->admin_email,
                'type' => $admin->admin_type,
            ]
        ];
        // $credentials = request(['admin_email', 'admin_password']);
        $factory = JWTFactory::customClaims($credentials);
        $payload = $factory->make();
        $token = JWTAuth::encode($payload);
        // dd(JWTAuth::parseToken()->authenticate());
        // return response()->json(['error' => 'Unauthorized', 'payload' => $payload, 'token' => $token->get(), 'decode' => $decode], 401);
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'token' => $token->get(),
        ]);
    }

    public function test(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diambil.',
            'data' => $request->get('data')
        ]);
    }

    // /**
    //  * Create a new AuthController instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     $this->middleware('auth:admin-api', ['except' => ['login']]);
    // }

    // /**
    //  * Get a JWT via given credentials.
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function login(Request $request)
    // {
    //     $credentials = [
    //         'admin_email' => $request->get('email'),
    //         'admin_password' => $request->get('password'),
    //     ];
    //     // $credentials = request(['admin_email', 'admin_password']);
    //     $factory = JWTFactory::customClaims($credentials);
    //     // dd($factory);
    //     $payload = $factory->make();
    //     // dd($payload);
    //     $token = JWTAuth::encode($payload);
    //     $decode = JWTAuth::decode($token);
    //     // dd($decode);
    //     // dd(auth()->guard('admin-api')->attempt($credentials));
    //     // if (! $token = auth()->guard('admin-api')->attempt($credentials)) {
    //         return response()->json(['error' => 'Unauthorized', 'payload' => $payload, 'token' => $token->get(), 'decode' => $decode], 401);
    //     // }

    //     return $this->respondWithToken($token);
    // }

    // /**
    //  * Get the authenticated User.
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function me()
    // {
    //     return response()->json(auth()->user());
    // }

    // /**
    //  * Log the user out (Invalidate the token).
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function logout()
    // {
    //     auth()->logout();

    //     return response()->json(['message' => 'Successfully logged out']);
    // }

    // /**
    //  * Refresh a token.
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // // public function refresh()
    // // {
    //     // return $this->respondWithToken(auth()->refresh());
    // // }

    // /**
    //  * Get the token array structure.
    //  *
    //  * @param  string $token
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // protected function respondWithToken($token)
    // {
    //     return response()->json([
    //         'access_token' => $token,
    //         'token_type' => 'bearer',
    //         // 'expires_in' => auth()->fa()->getTTL() * 60
    //     ]);
    // }
}