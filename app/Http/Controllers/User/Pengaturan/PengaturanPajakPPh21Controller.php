<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Libraries\AppUploadLibrary;
use App\Model\Master\KaryawanModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Setting\SettingPajakPph21Model;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanPajakPPh21Controller extends Controller
{
    public function save(Request $request)
    {
        // dd($request->all());
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $stpajakpph21_npwp = $request->input('stpajakpph21_npwp');
        $stpajakpph21_name = $request->input('stpajakpph21_name');
        $stpajakpph21_companyname = $request->input('stpajakpph21_companyname');
        $stpajakpph21_posisi = $request->input('stpajakpph21_posisi');
        $stpajakpph21_lokasi = $request->input('stpajakpph21_lokasi');
        
        // dd($groups);
        $rules = [
            'stpajakpph21_npwp' => 'required',
            'stpajakpph21_name' => 'required',
            'stpajakpph21_companyname' => 'required',
            'stpajakpph21_posisi' => 'required',
            'stpajakpph21_lokasi' => 'required',
        ];
        
        if ($request->hasfile('stpajakpph21_ttd')) {
            $rules['stpajakpph21_ttd'] = 'mimes:jpeg,jpg,png|max:256';
        }

        $this->validate($request, $rules, [
            'stpajakpph21_npwp.required' => 'NPWP wajib diisi!',
            'stpajakpph21_name.required' => 'NPWP wajib diisi!',
            'stpajakpph21_companyname.required' => 'NPWP wajib diisi!',
            'stpajakpph21_posisi.required' => 'NPWP wajib diisi!',
            'stpajakpph21_lokasi.required' => 'NPWP wajib diisi!',
        ]);
        // check if npwp is not badan
        $checknpwp = WajibPajakModel::where(['wajibpajak_npwp' => $stpajakpph21_npwp, 'wajibpajak_type' => 'BADAN'])->first();
        if($checknpwp) {
            return response()->json([
                'success' => false,
                'message' => 'NPWP Penanggung Jawab harus NPWP Individu.'
            ]);
        }

        $stpajakpph21 = SettingPajakPph21Model::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        
        $old_logo = ($stpajakpph21) ? $stpajakpph21->stpajakpph21_ttd : null;
        $logo_url = null;
        $logo_base64 = null;
        if ($request->hasfile('stpajakpph21_ttd')) {
            $file = $request->file('stpajakpph21_ttd');
            // dd($file->extension());
            $appupload_library = new AppUploadLibrary();
            $uploaded_photo = $appupload_library->uploadFile($file, 'images/pengaturan/pajak/pph21-ttd');

            if($uploaded_photo['success'] == false) {
                return response()->json([
                    'success' => false,
                    'message' => $uploaded_photo['message']
                ]);
            }
            // dd($uploaded_photo);
            $logo_url = $uploaded_photo['data']['url'];
            $logo_base64 = $uploaded_photo['data']['base64'];
        }

        $save_data = [
            'stpajakpph21_npwp' => $stpajakpph21_npwp,
            'stpajakpph21_name' => $stpajakpph21_name,
            'stpajakpph21_companyname' => $stpajakpph21_companyname,
            'stpajakpph21_posisi' => $stpajakpph21_posisi,
            'stpajakpph21_lokasi' => $stpajakpph21_lokasi,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ];
        if($logo_url) {
            $save_data['stpajakpph21_ttd'] = $logo_url;
            $save_data['stpajakpph21_ttd_base64'] = $logo_base64;
        }
        
        DB::beginTransaction();
        try {
            if($stpajakpph21) {
                SettingPajakPph21Model::where([
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])->update($save_data);
            } else {
                $stpajakpph21 = SettingPajakPph21Model::create($save_data);
            }
            
            // delete old photo if exist
            if($logo_url && $old_logo) {
                $appupload_library->deleteFile($old_logo, true);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Penggajian karyawan berhasil disimpan.',
                'data' => [
                    'stpajakpph21_ttd' => $logo_url
                ]
            ]);
            

        } catch(Error $e) {
            // delete file if already upload
            if(isset($uploaded_photo) && $uploaded_photo['success']) {
                if($uploaded_photo['data']) {
                    $appupload_library->deleteFile('images/pengaturan/pajak/pph21-ttd/'.$uploaded_photo['data']['filename']);
                }
            }
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}