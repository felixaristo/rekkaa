<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Master\BpjsRateModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingGroupTunjanganKaryawanModel;
use App\Model\Setting\SettingLemburKaryawanModel;
use App\Model\Setting\SettingModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanLemburController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        // $stgrouptunjangan_karyawan = SettingGroupTunjanganKaryawanModel::select('stgrouptunjangankaryawan_id', 'stgrouptunjangankaryawan_name')
        // ->where([
        //     'stgrouptunjangankaryawan_active' => 1,
        //     'ms_wajibpajak_id' => $wajibpajak_id,
        // ])->get();
        $stlembur_karyawan = SettingLemburKaryawanModel::where([
            'stlemburkaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        
        if(!$stlembur_karyawan) {
            // $save_data = [
            //     [
            //         "period" => "01-2019",
            //         "value" => [
            //             [
            //                 'ms_wajibpajak_id' => $wajibpajak_id,
            //                 'stlemburkaryawan_type' => 1,
            //                 'stlemburkaryawan_basevalue' => [
            //                     [
            //                         'name' => 'GAJI_POKOK',
            //                         'value' => 1,
            //                         'type' => 1
            //                     ]
            //                 ],
            //                 'stlemburkaryawan_value' => [
            //                     [
            //                         'jam_awal' => 0,
            //                         'jam_akhir' => 8,
            //                         'upah' => 2,
            //                     ]
            //                 ],
            //             ],
            //             [
            //                 'ms_wajibpajak_id' => $wajibpajak_id,
            //                 'stlemburkaryawan_type' => 2,
            //                 'stlemburkaryawan_basevalue' => [
            //                     [
            //                         'name' => 'GAJI_POKOK',
            //                         'value' => 1,
            //                         'type' => 2
            //                     ]
            //                 ],
            //                 'stlemburkaryawan_value' => [
            //                     [
            //                         'jam_awal' => 0,
            //                         'jam_akhir' => 8,
            //                         'upah' => 2,
            //                     ],
            //                     [
            //                         'jam_awal' => 8,
            //                         'jam_akhir' => 9,
            //                         'upah' => 3,
            //                     ],
            //                     [
            //                         'jam_awal' => 9,
            //                         'jam_akhir' => 12,
            //                         'upah' => 4,
            //                     ]
            //                 ],
            //             ],
            //             [
            //                 'ms_wajibpajak_id' => $wajibpajak_id,
            //                 'stlemburkaryawan_type' => 3,
            //                 'stlemburkaryawan_basevalue' => [
            //                     [
            //                         'name' => 'GAJI_POKOK',
            //                         'value' => 1,
            //                         'type' => 3
            //                     ]
            //                 ],
            //                 'stlemburkaryawan_value' => [
            //                     [
            //                         'jam_awal' => 0,
            //                         'jam_akhir' => 5,
            //                         'upah' => 2,
            //                     ],
            //                     [
            //                         'jam_awal' => 5,
            //                         'jam_akhir' => 6,
            //                         'upah' => 3,
            //                     ],
            //                     [
            //                         'jam_awal' => 6,
            //                         'jam_akhir' => 9,
            //                         'upah' => 4,
            //                     ]
            //                 ],
            //             ],
            //             [
            //                 'ms_wajibpajak_id' => $wajibpajak_id,
            //                 'stlemburkaryawan_type' => 4,
            //                 'stlemburkaryawan_basevalue' => [
            //                     [
            //                         'name' => 'GAJI_POKOK',
            //                         'value' => 1,
            //                         'type' => 4
            //                     ]
            //                 ],
            //                 'stlemburkaryawan_value' => [
            //                     [
            //                         'jam_awal' => 0,
            //                         'jam_akhir' => 7,
            //                         'upah' => 2,
            //                     ],
            //                     [
            //                         'jam_awal' => 7,
            //                         'jam_akhir' => 8,
            //                         'upah' => 3,
            //                     ],
            //                     [
            //                         'jam_awal' => 8,
            //                         'jam_akhir' => 11,
            //                         'upah' => 4,
            //                     ]
            //                 ],
            //             ]
            //         ]
            //     ]
            // ];
            
            SettingLemburKaryawanModel::insert(['ms_wajibpajak_id' => $wajibpajak_id]);
            $stlembur_karyawan = SettingLemburKaryawanModel::where([
                'stlemburkaryawan_active' => 1,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])->first();
        }

        $setting_lembur = SettingModel::where('setting_active', 1)->where('setting_key', 'LEMBUR')->first();
        $stlembur = json_decode($setting_lembur->setting_value); 
        usort($stlembur, function($a, $b) {
            return $b->period <=> $a->period;
        });
        
        $stlemburnew = $stlembur[0];
        $stlembur_group1 = $stlemburnew->value[0];
        $stlembur_group2 = $stlemburnew->value[1];
        $stlembur_group3 = $stlemburnew->value[2];
        $stlembur_group4 = $stlemburnew->value[3];

        $data = [
            'title' => 'Pengaturan Lembur',
            'content' => 'user.pengaturan.lembur.index',
            'stlembur_karyawan' => $stlembur_karyawan,
            'stlembur_group1' => $stlembur_group1,
            'stlembur_group2' => $stlembur_group2,
            'stlembur_group3' => $stlembur_group3,
            'stlembur_group4' => $stlembur_group4,
        ];
        // dd($data);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    // public function save(Request $request)
    // {
    //     $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
    //     $type = $request->input('type');
    //     $besaranlembur_tunjangan_lainnya = $request->input('besaranlembur_tunjangan_lainnya');
    //     $besaranlembur = $request->input('besaranlembur');
    //     $jklabelawal = $request->input('jklabelawal');
    //     $jklabelakhir = $request->input('jklabelakhir');
    //     $bupah = $request->input('bupah');
    //     $lainnya = false;
    //     $temp_data = [];
    //     $jamkerja_temp_data = [];

    //     // dd($request->all());
    //     $validation = [
    //         'type' => 'required|in:1,2,3,4',
    //         'jklabelawal' => 'required',
    //         'jklabelakhir' => 'required',
    //         'bupah' => 'required',
    //     ];

    //     if($besaranlembur) {
    //         if(in_array('LAINNYA', $besaranlembur)) { // lainnya is fill.
    //             $lainnya = true;
    //         }
    //     }
        
    //     $this->validate($request, $validation, [
    //         'type.required' => 'Tipe wajib diisi!',
    //         'jklabelawal.required' => 'Jam Kerja Awal wajib diisi!',
    //         'jklabelakhir.required' => 'Jam Kerja Akhir wajib diisi!',
    //         'bupah.required' => 'Besaran Upah wajib diisi!',
    //     ]);

    //     if($lainnya) {
    //         array_push($temp_data, [
    //             'name' => 'LAINNYA',
    //             'value' => $besaranlembur_tunjangan_lainnya,
    //             'type' => $type,
    //         ]);
    //     } else {
    //         $grouptunjangan_ids = [];
    //         foreach($besaranlembur as $bl) {
    //             if(strtoupper($bl) == 'GAJI_POKOK') {
    //                 array_push($temp_data, [
    //                     'name' => 'GAJI_POKOK',
    //                     'value' => 1,
    //                     'type' => $type
    //                 ]);
    //             } else {
    //                 array_push($grouptunjangan_ids, $bl);
    //             }
    //         }
    //         if($grouptunjangan_ids) { // Group Tunjangan
    //             array_push($temp_data, [
    //                 'name' => 'GROUP_TUNJANGAN',
    //                 'value' => implode(',', $grouptunjangan_ids),
    //                 'type' => $type
    //             ]);
    //         }
    //     }

    //     $i=0;
    //     foreach($jklabelawal as $awal) {
    //         array_push($jamkerja_temp_data, [
    //             'jam_awal' => $awal,
    //             'jam_akhir' => (isset($jklabelakhir[$i])) ? $jklabelakhir[$i] : $awal,
    //             'upah' => (isset($bupah[$i])) ? $bupah[$i] : 0,
    //         ]);
    //         $i++;
    //     }
    //     if(count($jamkerja_temp_data) < 1) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Silahkan lengkapi jam kerja!'
    //         ]);
    //     }
        
    //     $save_data = [
    //         'ms_wajibpajak_id' => $wajibpajak_id,
    //         'stlemburkaryawan_type' => $type,
    //         'stlemburkaryawan_basevalue' => (count($temp_data) > 0) ? json_encode($temp_data) : '',
    //         'stlemburkaryawan_value' => (count($jamkerja_temp_data) > 0) ? json_encode($jamkerja_temp_data) : '',
    //     ];
        
    //     $tunjanganlembur = SettingLemburKaryawanModel::where([
    //         'ms_wajibpajak_id' => $wajibpajak_id,
    //         'stlemburkaryawan_type' => $type,
    //         'stlemburkaryawan_active' => 1
    //     ])->first();
        
    //     DB::beginTransaction();
    //     try {
    //         if($tunjanganlembur) { // UPDATE
    //             $tunjanganlembur->update($save_data);
    //         } else { // CREATE
    //             SettingLemburKaryawanModel::create($save_data);
    //         }

    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Pengaturan Lembur berhasil disimpan',
    //             'data' => [
    //                 'wajibpajak_id' => $wajibpajak_id
    //             ]
    //         ]);
            

    //     } catch(Error $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    public function savetax(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $taxable = $request->input('taxable');
        $taxable = ($taxable) ? 1 : 0;
        
        DB::beginTransaction();
        try {
            SettingLemburKaryawanModel::where([
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])->update(['stlemburkaryawan_taxable' => $taxable]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Dikenakan Pajak berhasil disimpan',
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