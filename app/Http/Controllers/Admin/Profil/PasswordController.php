<?php
namespace App\Http\Controllers\Admin\Profil;

use App\Http\Controllers\Controller;
use App\Model\Master\AdminModel;
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
            'content' => 'admin.profil.password',
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('admin.index', $data);
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

        $admin_id = session()->get('admin_data')['admin_id'];
        $old_password = $request->input('old_password');
        $new_password = $request->input('new_password');
        $confirm_password = $request->input('confirm_password');

        $admin = AdminModel::where(['admin_id' => $admin_id, 'admin_active' => 1])->first();
        if(!$admin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin tidak ditemukan!',
            ]);
        }

        if(!Hash::check($old_password, $admin->admin_password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai!',
            ]);
        }

        DB::beginTransaction();
        try {
            // update setting bpjs
            AdminModel::where([
                'admin_id' => $admin_id,
            ])->update([
                'admin_password' => Hash::make($new_password),
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