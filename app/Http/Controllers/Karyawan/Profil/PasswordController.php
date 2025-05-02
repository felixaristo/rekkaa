<?php
namespace App\Http\Controllers\Karyawan\Profil;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
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
            'content' => 'karyawan.profil.password',
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('karyawan.index', $data);
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

        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');

        $karyawan = $request->get('karyawan');
        
        if(!Hash::check($old_password, $karyawan->karyawan_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai!',
            ]);
        }

        DB::beginTransaction();
        try {
            // update password
            KaryawanModel::where([
                'karyawan_email' => $karyawan->karyawan_email,
                'karyawan_isuser' => 1,
                'karyawan_active' => 1,
            ])->update([
                'karyawan_password' => Hash::make($new_password),
                'karyawan_forgot_password' => null,
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