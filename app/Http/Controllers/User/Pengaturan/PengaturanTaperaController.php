<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Master\BpjsRateModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\TaperaRateModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingGroupTunjanganKaryawanModel;
use App\Model\Setting\SettingTaperaKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanTaperaController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sttapera_karyawan = SettingTaperaKaryawanModel::where([
            'sttaperakaryawan_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])->first();
        $tapera_rate = TaperaRateModel::select('taperarate_category', 'taperarate_code', 'taperarate_description', 'taperarate_rate')
        ->where(['taperarate_active' => 1])->get();

        $data = [
            'title' => 'Pengaturan Tapera',
            'content' => 'user.pengaturan.tapera.index',
            // 'stgrouptunjangan_karyawan' => $stgrouptunjangan_karyawan,
            'sttapera_karyawan' => $sttapera_karyawan,
            // 'stbpjs_value' => $stbpjs_value,
            'tapera_rate' => $tapera_rate,
            // 'bpjs_rate_jkk' => $bpjs_rate_jkk,
            // 'stbpjs_group_kesehatan' => $stbpjs_group_kesehatan,
            // 'stbpjs_group_tk' => $stbpjs_group_tk,
        ];
        // dd($data);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function save(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $menggunakantapera = $request->input('menggunakantapera');
        $menggunakantaperaditanggung = $request->input('menggunakantaperaditanggung');
        $temp_data = [];

        // dd($request->all());
        $validation = [
            'menggunakantapera' => 'required|in:0,1',
        ];

        $this->validate($request, $validation, [
            'menggunakantapera.required' => 'Apakah menggunakan Tapera wajib diisi!',
        ]);

        if($menggunakantapera == 1) {
            array_push($temp_data, [
                'name' => 'DITANGGUNG',
                'value' => ($menggunakantaperaditanggung == '1') ? 1 : 0,
                'type' => 'TAPERA',
            ]);
        }
        // dd($temp_data);
        $save_data = [
            'ms_wajibpajak_id' => $wajibpajak_id,
            'sttaperakaryawan_value' => (count($temp_data) > 0) ? json_encode($temp_data) : '',
        ];
        // dd($kontrak_bpjs_kes);
        // dd($stbpjstkpegawai_lainnya);
        $potongantapera = SettingTaperaKaryawanModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'sttaperakaryawan_active' => 1
        ])->first();
        // dd($tunjanganbpjs);
        DB::beginTransaction();
        try {
            if($potongantapera) { // UPDATE
                $potongantapera->update($save_data);
            } else { // CREATE
                SettingTaperaKaryawanModel::create($save_data);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Tapera berhasil disimpan',
                'data' => [
                    'wajibpajak_id' => $wajibpajak_id
                ]
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