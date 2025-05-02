<?php
namespace App\Http\Controllers\Admin\Profil;

use App\Http\Controllers\Controller;
use App\Model\Master\AdminModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfilController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $admin_id = session()->get('admin_data')['admin_id'];
        $admin = AdminModel::select('admin_name', 'admin_email', 'admin_phone')->where(['admin_id' => $admin_id])->first();
        $data = [
            'title' => 'Profil',
            'content' => 'admin.profil.index',
            'admin' => $admin
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
            'profil_name' => 'required',
            'profil_hp' => 'required',
        ], [
            'profil_name.required' => 'Nama wajib diisi!',
            'profil_hp.required' => 'No. HP wajib diisi!',
        ]);

        $admin_id = session()->get('admin_data')['admin_id'];
        $name = $request->input('profil_name');
        $phone = $request->input('profil_hp');

        DB::beginTransaction();
        try {
            // update setting bpjs
            AdminModel::where([
                'admin_id' => $admin_id,
            ])->update([
                'admin_name' => $name,
                'admin_phone' => $phone,
            ]);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Update profil berhasil',
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