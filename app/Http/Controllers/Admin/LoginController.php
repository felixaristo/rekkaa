<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Master\AdminModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // die(Hash::make('Rekkaa@app123'));
        return view('admin.login', [
            'title' => 'Admin Login',
            'content' => 'admin.login',
        ]);
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
        $admin = AdminModel::where(['admin_email' => $input['email'], 'admin_active' => 1])->first();
        if(!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan!',
            ]);
        }

        if(!Hash::check($input['password'], $admin->admin_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal! Silahkan gunakan email atau password lain.',
            ]);
        }

        $session_data = [
            'admin_id' => $admin->admin_id,
            'admin_email' => $admin->admin_email,
            'admin_name' => $admin->admin_name,
            'admin_type' => $admin->admin_type,
        ];

        session()->put('admin_data', $session_data);
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'data' => $session_data
        ]);
    }

    public function logout () {
        session()->flush();
        // redirect to homepage
        return redirect('/admin/login');
    }
}