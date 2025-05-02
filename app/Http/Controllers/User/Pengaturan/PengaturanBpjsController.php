<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Master\BpjsRateModel;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingGroupTunjanganKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanBpjsController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $stgrouptunjangan_karyawan = SettingGroupTunjanganKaryawanModel::select('stgrouptunjangankaryawan_id', 'stgrouptunjangankaryawan_name')
        ->where([
            'stgrouptunjangankaryawan_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])->get();
        $bpjs_rate_jkk = BpjsRateModel::select('ms_bpjs_rate.*')
        ->where(['bpjsrate_active' => 1, 'bpjsrate_category' => 'TK', 'bpjsrate_code' => 'JKK'])->get();
        $bpjs_rate = BpjsRateModel::select('bpjsrate_category', 'bpjsrate_code', DB::raw('min(bpjsrate_rate) bpjsrate_rate'), DB::raw('min(bpjsrate_calculationtype) bpjsrate_calculationtype')
        , DB::raw('min(bpjsrate_description) bpjsrate_description')
        , DB::raw("(SELECT json_agg(bpjsratejkktable) 
        FROM (
            SELECT mbr.*
            FROM ms_bpjs_rate as mbr
            WHERE mbr.bpjsrate_category = 'TK' AND mbr.bpjsrate_code = 'JKK'
        )
        as bpjsratejkktable) as bpjsratejkk_json"))
        ->where(['bpjsrate_active' => 1])
        ->groupBy(['bpjsrate_category', 'bpjsrate_code'])->get();

        $stbpjs_karyawan = SettingBpjsKaryawanModel::where([
            'stbpjskaryawan_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])->first();
        // dd($stbpjs_karyawan);

        $stbpjs_group_kesehatan = [];
        $stbpjs_group_tk = [];
        $stbpjs_value = [];
        if($stbpjs_karyawan) {
            $stbpjs_value = ($stbpjs_karyawan && $stbpjs_karyawan->stbpjskaryawan_value) ? json_decode($stbpjs_karyawan->stbpjskaryawan_value) : null;
            if($stbpjs_value) {
                $grouptunjangan_kes_ids = [];
                $grouptunjangan_tk_ids = [];
                foreach($stbpjs_value as $val) {
                    if($val->name == 'GROUP_TUNJANGAN') {
                        $exp_group = explode(',', $val->value);
                        if($val->type == 'KESEHATAN') {
                            $grouptunjangan_kes_ids = array_merge($grouptunjangan_kes_ids, $exp_group);
                        }
                        if($val->type == 'TENAGA_KERJA') {
                            $grouptunjangan_tk_ids = array_merge($grouptunjangan_tk_ids, $exp_group);
                        }
                    }
                }
                // dd($grouptunjangan_ids);
                $grouptunjangan_ids = array_merge($grouptunjangan_kes_ids, $grouptunjangan_tk_ids);
                $grouptunjanganselected = SettingGroupTunjanganKaryawanModel::whereIn('stgrouptunjangankaryawan_id', $grouptunjangan_ids)
                ->where(['stgrouptunjangankaryawan_active' => 1])->get();
                // dd($grouptunjanganselected);
                if($grouptunjanganselected) {
                    foreach($grouptunjanganselected as $tjselected) {
                        if(in_array($tjselected->stgrouptunjangankaryawan_id, $grouptunjangan_kes_ids)) {
                            array_push($stbpjs_group_kesehatan, $tjselected->stgrouptunjangankaryawan_id);
                        }
                        if(in_array($tjselected->stgrouptunjangankaryawan_id, $grouptunjangan_tk_ids)) {
                            array_push($stbpjs_group_tk, $tjselected->stgrouptunjangankaryawan_id);
                        }
                    }
                }
            }
        }

        $data = [
            'title' => 'Pengaturan BPJS',
            'content' => 'user.pengaturan.bpjs.index',
            'stgrouptunjangan_karyawan' => $stgrouptunjangan_karyawan,
            'stbpjs_karyawan' => $stbpjs_karyawan,
            'stbpjs_value' => $stbpjs_value,
            'bpjs_rate' => $bpjs_rate,
            'bpjs_rate_jkk' => $bpjs_rate_jkk,
            'stbpjs_group_kesehatan' => $stbpjs_group_kesehatan,
            'stbpjs_group_tk' => $stbpjs_group_tk,
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
        $jkk_bpjsrate_id = $request->input('jkk_bpjsrate_id');
        $menggunakanbpjskes = $request->input('menggunakanbpjskes');
        $menggunakanbpjskesditanggung = $request->input('menggunakanbpjskesditanggung');
        $menggunakanbpjstk = $request->input('menggunakanbpjstk');
        $menggunakanbpjstkditanggung = $request->input('menggunakanbpjstkditanggung');
        $kontrak_bpjs_kes = $request->input('kontrak_bpjs_kes');
        $kontrak_bpjs_kes_tunjangan_lainnya = $request->input('kontrak_bpjs_kes_tunjangan_lainnya');
        $kontrak_bpjs_tk = $request->input('kontrak_bpjs_tk');
        $kontrak_bpjs_tk_tunjangan_lainnya = $request->input('kontrak_bpjs_tk_tunjangan_lainnya');
        $stbpjskeskaryawan_lainnya = false;
        $stbpjstkkaryawan_lainnya = false;
        $temp_data = [];

        // dd($request->all());
        $validation = [
            'menggunakanbpjskes' => 'required|in:0,1',
            'menggunakanbpjstk' => 'required|in:0,1',
        ];

        if($menggunakanbpjskes == 1) {
            $validation['kontrak_bpjs_kes'] = 'required';
            
            if($kontrak_bpjs_kes) {
                if(in_array('LAINNYA', $kontrak_bpjs_kes)) { // lainnya is fill.
                    $stbpjskeskaryawan_lainnya = true;
                }
            }
        }
        if($menggunakanbpjstk == 1) {
            $validation['kontrak_bpjs_tk'] = 'required';
            if($kontrak_bpjs_tk) {
                if(in_array('LAINNYA', $kontrak_bpjs_tk)) { // lainnya is fill.
                    $stbpjstkkaryawan_lainnya = true;
                }
            }
        }
        
        $this->validate($request, $validation, [
            'menggunakanbpjskes.required' => 'Apakah menggunakan BPJS Kesehatan wajib diisi!',
            'menggunakanbpjstk.required' => 'Apakah menggunakan BPJS Tenaga Kerja wajib diisi!',
            'kontrak_bpjs_kes.required' => 'Pengaturan BPJS Kesehatan wajib diisi!',
            'kontrak_bpjs_tk.required' => 'Pengaturan BPJS Tenaga Kerja wajib diisi!',
        ]);

        if($menggunakanbpjskes == 1) {
            array_push($temp_data, [
                'name' => 'DITANGGUNG',
                'value' => ($menggunakanbpjskesditanggung == '1') ? 1 : 0,
                'type' => 'KESEHATAN',
            ]);
            
            if($stbpjskeskaryawan_lainnya) {
                array_push($temp_data, [
                    'name' => 'LAINNYA',
                    'value' => $kontrak_bpjs_kes_tunjangan_lainnya,
                    'type' => 'KESEHATAN',
                ]);
            } else {
                $grouptunjangan_ids = [];
                foreach($kontrak_bpjs_kes as $bpjskes) {
                    if(strtoupper($bpjskes) == 'GAJI_POKOK') {
                        array_push($temp_data, [
                            'name' => 'GAJI_POKOK',
                            'value' => 1,
                            'type' => 'KESEHATAN'
                        ]);
                    } else {
                        array_push($grouptunjangan_ids, $bpjskes);
                    }
                }
                if($grouptunjangan_ids) { // Group Tunjangan
                    array_push($temp_data, [
                        'name' => 'GROUP_TUNJANGAN',
                        'value' => implode(',', $grouptunjangan_ids),
                        'type' => 'KESEHATAN'
                    ]);
                }
            }
        }
        if($menggunakanbpjstk == 1) {
            array_push($temp_data, [
                'name' => 'JKK_RATE',
                'value' => ($jkk_bpjsrate_id) ? $jkk_bpjsrate_id : 1,
                'type' => 'TENAGA_KERJA',
            ]);
            array_push($temp_data, [
                'name' => 'DITANGGUNG',
                'value' => ($menggunakanbpjstkditanggung == '1') ? 1 : 0,
                'type' => 'TENAGA_KERJA'
            ]);
            if($stbpjstkkaryawan_lainnya) {
                array_push($temp_data, [
                    'name' => 'LAINNYA',
                    'value' => $kontrak_bpjs_tk_tunjangan_lainnya,
                    'type' => 'TENAGA_KERJA'
                ]);
            } else {
                $grouptunjangan_ids = [];
                foreach($kontrak_bpjs_tk as $bpjstk) {
                    if(strtoupper($bpjstk) == 'GAJI_POKOK') {
                        array_push($temp_data, [
                            'name' => 'GAJI_POKOK',
                            'value' => 1,
                            'type' => 'TENAGA_KERJA'
                        ]);
                    } else {
                        array_push($grouptunjangan_ids, $bpjstk);
                    }
                }
                if($grouptunjangan_ids) { // Group Tunjangan
                    array_push($temp_data, [
                        'name' => 'GROUP_TUNJANGAN',
                        'value' => implode(',', $grouptunjangan_ids),
                        'type' => 'TENAGA_KERJA'
                    ]);
                }
            }
        }
        // dd($temp_data);
        $save_data = [
            'ms_wajibpajak_id' => $wajibpajak_id,
            'stbpjskaryawan_value' => (count($temp_data) > 0) ? json_encode($temp_data) : '',
        ];
        // echo json_encode($save_data);
        // die;
        
        // dd($kontrak_bpjs_kes);
        // dd($stbpjstkpegawai_lainnya);
        $tunjanganbpjs = SettingBpjsKaryawanModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'stbpjskaryawan_active' => 1
        ])->first();
        // dd($tunjanganbpjs);
        DB::beginTransaction();
        try {
            if($tunjanganbpjs) { // UPDATE
                $tunjanganbpjs->update($save_data);
            } else { // CREATE
                SettingBpjsKaryawanModel::create($save_data);
            }

            // update karyawan if exist
            $save_data_karyawan = [];
            if($stbpjskeskaryawan_lainnya) {
                $save_data_karyawan['karyawan_bpjskeslainnya'] = $kontrak_bpjs_kes_tunjangan_lainnya;
            } else {
                $save_data_karyawan['karyawan_bpjskeslainnya'] = 0;
            }
            if($stbpjstkkaryawan_lainnya) {
                $save_data_karyawan['karyawan_bpjstklainnya'] = $kontrak_bpjs_tk_tunjangan_lainnya;
            } else {
                $save_data_karyawan['karyawan_bpjstklainnya'] = 0;
            }
            if($save_data_karyawan)
                KaryawanModel::where(['ms_wajibpajak_id' => $wajibpajak_id])->update($save_data_karyawan);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan BPJS berhasil disimpan',
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