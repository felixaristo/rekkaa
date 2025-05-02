<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Master\WajibPajakUserModel;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Ramsey\Uuid\v1;

class PengaturanProfilEntitasController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // public function index(Request $request)
    // {
    //     $user_id = session()->get('user_data')['user_id'];
    //     $wajibpajak_id = (session()->get('wajibpajak_current')) ? session()->get('wajibpajak_current')['wajibpajak_id'] : null;
    //     $wajibpajak = WajibPajakModel::select('ms_wajib_pajak.*')
    //     ->with(['regency', 'klu'])
    //     ->where(['ms_user_id' => $user_id, 'wajibpajak_id' => $wajibpajak_id])
    //     ->first();
    //     // dd($wajibpajak->klu);
    //     if(!$wajibpajak) {
    //         abort(404);
    //     }
    //     $data = [
    //         'title' => 'Profil Entitas',
    //         'content' => 'user.pengaturan.profil-entitas.index',
    //         'wajibpajak' => $wajibpajak
    //     ];
    //     // dd($wajibpajak);
    //     if($request->ajax()) {
    //         return view($data['content'], $data)->render();
    //     } else {
    //         return view('user.index', $data);
    //     }
    //     // dd('ooioi');
    // }

    public function save(Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        // dd($wajibpajak_id);
        // check if is owner
        $mruserwajibpajak = MrUserWajibPajakModel::where([
            'ms_user_id' => $user_id,
            'ms_wajibpajak_id' => $wajibpajak_id,
            'userwajibpajak_owner' => 1,
        ])->first();
        if(!$mruserwajibpajak->userwajibpajak_owner) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya owner yang bisa merubah data!'
            ]);
        }

        // find wajib pajak
        $wajibpajak = WajibPajakModel::where([
            'ms_wajib_pajak.ms_user_id' => $user_id,
            'wajibpajak_id' => $wajibpajak_id
        ])->first();
        // dd($wajibpajak);
        if(!$wajibpajak) {
            return response()->json([
                'success' => false,
                'message' => 'Wajib pajak tidak ditemukan!'
            ]);
        }

        $rules = [
            'wajibpajak_name' => 'required',
            // 'wajibpajak_email' => 'required|unique:ms_wajib_pajak,wajibpajak_email,'.$wajibpajak_id.',wajibpajak_id',
            'wajibpajak_phone' => 'required',
            'wajibpajak_city' => 'required|integer',
            'wajibpajak_address' => 'required',
            'ms_klu_id' => 'required|integer',
            'wajibpajak_postal_code' => 'required',
        ];
        if($wajibpajak->wajibpajak_type == 'BADAN') {
            $rules['wajibpajak_npwp'] = 'required|min:20|max:20|unique:ms_wajib_pajak,wajibpajak_npwp,'.$wajibpajak_id.',wajibpajak_id';
        } else {
            $rules['wajibpajak_nik'] = 'required|min:16|max:16|unique:ms_wajib_pajak,wajibpajak_nik,'.$wajibpajak_id.',wajibpajak_id';
        }
        $this->validate($request, $rules, [
            'wajibpajak_name.required' => 'Nama wajib diisi!',
            'ms_klu_id.required' => 'KLU wajib diisi!',
            'wajibpajak_npwp.required' => 'NPWP wajib diisi!',
            'wajibpajak_nik.required' => 'NIK wajib diisi!',
            'wajibpajak_address.required' => 'Alamat wajib diisi!',
            'wajibpajak_postal_code.required' => 'Alamat wajib diisi!',
            'wajibpajak_phone.required' => 'No. Telepon wajib sesuai!',
            'wajibpajak_city.required' => 'Kota wajib sesuai!',
            // 'wajibpajak_email.required' => 'Email wajib diisi!',
            // 'wajibpajak_email.email' => 'Format email tidak sesuai!',
            // 'wajibpajak_email.unique' => 'Email sudah digunakan!',
            'wajibpajak_nik.unique' => 'NIK sudah digunakan!',
            'wajibpajak_npwp.unique' => 'NPWP sudah digunakan!',
        ]);
        
        // dd($request->input('wajibpajak_city'));

        DB::beginTransaction();
        try {
            $save_data = [
                // 'wajibpajak_email' => $request->input('wajibpajak_email'),
                'wajibpajak_name' => $request->input('wajibpajak_name'),
                'ms_klu_id' => $request->input('ms_klu_id'),
                'wajibpajak_address' => $request->input('wajibpajak_address'),
                'wajibpajak_postal_code' => $request->input('wajibpajak_postal_code'),
                'wajibpajak_phone' => $request->input('wajibpajak_phone'),
                'ms_regency_id' => $request->input('wajibpajak_city'),
                'ms_country_id' => $request->input('wajibpajak_country'),
            ];
            // if($wajibpajak->wajibpajak_type == 'BADAN') {
                $save_data['wajibpajak_npwp'] = $request->input('wajibpajak_npwp');
            // } else {
            //     $save_data['wajibpajak_nik'] = $request->input('wajibpajak_nik');
            // }
            $wajibpajak->update($save_data);

            MrUserWajibPajakModel::where([
                'ms_user_id' => $user_id,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])->update([
                'userwajibpajak_phone' => $save_data['wajibpajak_phone']
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