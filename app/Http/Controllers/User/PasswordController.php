<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\Model\Master\WajibPajakUserModel;
use App\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Pengaturan',
            'content' => 'user.profil.password',
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
        // dd('ooioi');
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'new_password' => 'required|same:confirm_password',
            'confirm_password' => 'required',
        ], [
            'old_password.required' => 'Password lama diisi!',
            'new_password.required' => 'Password baru diisi!',
            'confirm_password.required' => 'Konfirmasi password wajib diisi!',
            'new_password.same' => 'Password baru tidak sama dengan Konfirmasi password!',
        ]);

        $user_id = session()->get('user_data')['user_id'];
        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');
        $confirm_password = $request->input('confirm_password');

        $user = User::where(['user_id' => $user_id, 'user_active' => 1])->first();
        if(!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan!',
            ]);
        }

        if(!Hash::check($old_password, $user->user_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai!',
            ]);
        }

        DB::beginTransaction();
        try {
            // update setting bpjs
            User::where([
                'user_id' => $user_id,
            ])->update([
                'user_password' => Hash::make($new_password),
            ]);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Update password berhasil',
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}