<?php
namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Model\Setting\SettingModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AlamatController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Alamat',
            'content' => 'admin.setting.alamat.index',
            'alamat' => SettingModel::where(['setting_key' => 'ADDRESS', 'setting_active' => 1])->first()
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('admin.index', $data);
        }
    }

    public function update($settingKey, Request $request)
    {
        $this->validate($request, [
            'setting_value' => 'required',
            'setting_description' => 'required',
        ], [
            // 'bpjsrate_category.required' => 'Kategori wajib diisi!',
            // 'bpjsrate_code.required' => 'Kode wajib diisi!',
            'setting_value.required' => 'Nilai wajib diisi!',
            'setting_description.required' => 'Deskripsi wajib diisi!',
        ]);
        $setting = SettingModel::where([
            'setting_key' => $settingKey, 
            'setting_active' => 1,
        ])->first();
        if(!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting Alamat tidak ditemukan!'
            ]);
        }

        $save_data = [
            // 'bpjsrate_category' => $request->input('bpjsrate_category'),
            // 'bpjsrate_code' => $request->input('bpjsrate_code'),
            'setting_value' => $request->input('setting_value'),
            'setting_description' => $request->input('setting_description'),
        ];

        DB::beginTransaction();
        try {
            
            $setting->update($save_data);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Setting Alamat berhasil disimpan',
                'data' => $setting
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