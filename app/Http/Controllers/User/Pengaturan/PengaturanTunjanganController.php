<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanDivisiModel;
use App\Model\Master\KaryawanJabatanModel;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingGroupTunjanganKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanDetailModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanTunjanganController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Pengaturan Tunjangan',
            'content' => 'user.pengaturan.tunjangan.index',
        ];
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function datatable(Request $request)
    {
        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
        $search = (isset($request->input('search')['value']) && $request->input('search')['value']) ? $request->input('search')['value'] : null;
        $order_columns = ['sttunjangankaryawan_id','sttunjangankaryawan_name','sttunjangankaryawan_value','sttunjangankaryawan_active'];
        $order_col = $order_columns[0];
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $where = [
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];
        $list = SettingTunjanganKaryawanModel::select('st_tunjangan_karyawan.*')
        ->when($search, function ($q, $search) {
            $lowercaseSearch = strtolower($search);
            return $q->where(function ($query) use ($lowercaseSearch) {
                $query->where(DB::raw('LOWER(sttunjangankaryawan_id::text)'), 'like', '%' . $lowercaseSearch . '%')
                ->orWhere(DB::raw('LOWER(sttunjangankaryawan_name)'), 'like', '%' . $lowercaseSearch . '%');
            });
        })
        ->with(['stgrouptunjangan'
        // , 'sttunjangandetail', 
        // 'sttunjangandetailaktif.karyawan' => function($query) {
        //     $query->select('karyawan_id', 'karyawan_name');
        // }
        ])
        ->where($where)
        ->orderBy($order_col, $order_type)
        ->take($limit)->skip($offset)->get();
        // dd($list[6]->sttunjangandetail[0]->karyawan);

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = SettingTunjanganKaryawanModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $sttunjangankaryawan_period = $request->input('sttunjangankaryawan_period');
        $sttunjangankaryawan_is_all = $request->input('sttunjangankaryawan_is_all');
        $active = $request->input('sttunjangankaryawan_active');
        // $receivelate = $request->input('sttunjangankaryawan_recievelate');
        // $receiveabsence = $request->input('sttunjangankaryawan_recieveabsence');
        $taxable = $request->input('sttunjangankaryawan_taxable');
        $employees = $request->input('sttunjangankaryawan_employees');
        $divisis = $request->input('sttunjangankaryawan_divisis');
        $jabatans = $request->input('sttunjangankaryawan_jabatans');
        $grouptunjangan = $request->input('sttunjangankaryawan_group');
        $calculation = $request->input('sttunjangankaryawan_calculation');
        // $type = $request->input('sttunjangankaryawan_type');
        $formula = $request->input('sttunjangankaryawan_formula');
        // dd($groups);
        $rules = [
            'sttunjangankaryawan_name' => 'required',
            'sttunjangankaryawan_period' => 'required|in:HARI,MINGGU,BULAN,TAHUN',
            // 'sttunjangankaryawan_value' => 'required|integer',
            'sttunjangankaryawan_group' => 'required',
            // 'sttunjangankaryawan_recievelate' => 'required|in:0,1',
            // 'sttunjangankaryawan_recieveabsence' => 'required|in:0,1',
            'sttunjangankaryawan_taxable' => 'required|in:0,1',
            'sttunjangankaryawan_active' => 'required|in:0,1',
            'sttunjangankaryawan_calculation' => 'required|in:JUMLAH_TETAP,FORMULA',
        ];

        if($sttunjangankaryawan_period == 'TAHUN') {
            $rules['sttunjangankaryawan_paymentperiod'] = 'required|integer|min:1|max:12';
            // $rules['sttunjangankaryawan_type'] = 'required|in:BONUS,THR';
        }
        if($calculation == 'JUMLAH_TETAP') {
            $rules['sttunjangankaryawan_value'] = 'required|integer';
        } else if($calculation == 'FORMULA') {
            $rules['sttunjangankaryawan_formula'] = 'required';
        }
        $this->validate($request, $rules, [
            'sttunjangankaryawan_name.required' => 'Nama tunjangan wajib diisi!',
            'sttunjangankaryawan_value.required' => 'Nilai tunjangan wajib diisi!',
            'sttunjangankaryawan_value.integer' => 'Nilai tunjangan harus angka!',
            'sttunjangankaryawan_group.required' => 'Label slip gaji wajib diisi!',
            'sttunjangankaryawan_period.required' => 'Periode tunjangan wajib diisi!',
            'sttunjangankaryawan_paymentperiod.required' => 'Periode pembayaran tunjangan wajib diisi!',
            'sttunjangankaryawan_calculation.required' => 'Kalkulasi tunjangan wajib diisi!',
            // 'sttunjangankaryawan_recievelate.required' => 'Tunjangan diterima walaupun telat wajib diisi!',
            // 'sttunjangankaryawan_recieveabsence.required' => 'Tunjangan diterima walaupun absen wajib diisi!',
            'sttunjangankaryawan_taxable.required' => 'Tunjangan kena pajak wajib diisi!',
            'sttunjangankaryawan_active.required' => 'Status tunjangan wajib diisi!',
            // 'sttunjangankaryawan_type.required' => 'Tipe wajib diisi!',
        ]);

        if(!$sttunjangankaryawan_is_all) {
            if(!$employees) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan pilih karyawan.',
                ]);       
            }
        }

        if($employees) {
            // check if the employees belong to entities
            $karyawans = KaryawanModel::select('karyawan_id', 'st_tunjangan_id')
            ->where([
                'karyawan_active' => 1,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])
            ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
            ->whereIn('karyawan_id', $employees)->get();
        }

        $tunjanganValue = 0;
        $tunjanganFormula = null;
        $grouptunjanganformulaids = [];
        $tunjanganformulaids = [];
        if($calculation === 'JUMLAH_TETAP') {
            $tunjanganValue = $request->input('sttunjangankaryawan_value');
        } else {
            $tunjanganFormula = ($formula) ? json_encode($formula) : null;
            foreach($formula as $fm) {
                if(isset($fm['value']) && is_numeric($fm['value'])) {
                    array_push($grouptunjanganformulaids, $fm['value']);
                }
            }
            if(count($grouptunjanganformulaids) > 0) {
                $formulatunjangan = SettingTunjanganKaryawanModel::select('sttunjangankaryawan_id')
                ->whereIn('st_grouptunjangankaryawan_id', $grouptunjanganformulaids)
                ->where([
                    'sttunjangankaryawan_calculation' => 'TETAP', 
                    'sttunjangankaryawan_active' => '1',
                    'ms_wajibpajak_id' => $wajibpajak_id
                ])->get();

                if(count($formulatunjangan) > 0) {
                    foreach($formulatunjangan as $fmula) {
                        array_push($tunjanganformulaids, $fmula->sttunjangankaryawan_id);
                    }
                }
            }
        }
        //     // Define a regular expression pattern to match variable names
        //     $pattern = '/\b\w+\b/';

        //     $formula = $request->input('sttunjangankaryawan_formula');

        //     // Use preg_match_all to find all matches in the input string
        //     preg_match_all($pattern, $formula, $matches);

        //     $variableNames = $matches[0];
            

        //     foreach ($variableNames as $variableName) {
        //         // Query the database for records where stgrouptunjangankaryawan_name matches $variableName
        //         $records = SettingGroupTunjanganKaryawanModel::where('stgrouptunjangankaryawan_name', $variableName)->where('ms_wajibpajak_id', $wajibpajak_id)
        //             ->pluck('stgrouptunjangankaryawan_id'); // Use pluck to retrieve only the stgrouptunjangankaryawan_id
                

        //         if ($records->count() > 0) {
        //             if($variableName !== 'Gaji Pokok' && $variableName !== 'Gaji' && $variableName !== 'Pokok') {
        //                 $formula = str_replace($variableName, 'COMP'.$records->first(), $formula);
        //             } 
        //         }
        //     }

        //     $tunjanganFormula = $formula;
        // }
        $divisis_jabatan = null;
        if($sttunjangankaryawan_is_all == 4) {
            $divisis_jabatan = implode(',', $divisis);
        } else if($sttunjangankaryawan_is_all == 5) {
            $divisis_jabatan = implode(',', $jabatans);
        }
        $save_data = [
            'sttunjangankaryawan_name' => ucwords($request->input('sttunjangankaryawan_name')),
            'sttunjangankaryawan_code' => strtoupper(str_replace(' ', '_', $request->input('sttunjangankaryawan_name'))),
            'sttunjangankaryawan_value' => $tunjanganValue,
            'sttunjangankaryawan_formula' => $tunjanganFormula,
            'st_sttunjangankaryawan_id' => (count($tunjanganformulaids) > 0) ? implode(',', $tunjanganformulaids) : null,
            'sttunjangankaryawan_period' => $request->input('sttunjangankaryawan_period'),
            'sttunjangankaryawan_paymentperiod' => $sttunjangankaryawan_period == 'TAHUN' ? intval($request->input('sttunjangankaryawan_paymentperiod')) : 0,
            // 'sttunjangankaryawan_paymentperiod' => 0,
            'sttunjangankaryawan_calculation' => $calculation,
            'ms_wajibpajak_id' => $wajibpajak_id,
            'sttunjangankaryawan_active' => $active,
            // 'sttunjangankaryawan_recievelate' => $receivelate,
            // 'sttunjangankaryawan_recieveabsence' => $receiveabsence,
            'sttunjangankaryawan_taxable' => $taxable,
            'sttunjangankaryawan_is_all' => $sttunjangankaryawan_is_all == 1,
            'sttunjangankaryawan_used_for' => $sttunjangankaryawan_is_all,
            'sttunjangankaryawan_division_jabatan' => $divisis_jabatan,
            // 'sttunjangankaryawan_type' => $type,
        ];

        DB::beginTransaction();
        try {
            // check group exist
            if(intval($grouptunjangan) <= 0) {
                // insert new group
                $group = SettingGroupTunjanganKaryawanModel::create([
                    'stgrouptunjangankaryawan_name' => ucwords($grouptunjangan),
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ]);
            } else {
                $group = SettingGroupTunjanganKaryawanModel::where([
                    'stgrouptunjangankaryawan_id' => $grouptunjangan,
                    'stgrouptunjangankaryawan_active' => 1,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])->first();
            }
            $save_data['st_grouptunjangankaryawan_id'] = $group->stgrouptunjangankaryawan_id;
            
            $sttunjangankaryawan = SettingTunjanganKaryawanModel::create($save_data);

            if(in_array($sttunjangankaryawan_is_all, [1,2,3,4,5])) {
                $where_karyawan = [
                    'karyawan_active' => '1',
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ];
                if($sttunjangankaryawan_is_all == 2) { // perempuan
                    $where_karyawan['karyawan_gender'] = 'P';
                } else if($sttunjangankaryawan_is_all == 3) { // laki-laki
                    $where_karyawan['karyawan_gender'] = 'L';
                }
                // get all employees
                $karyawans = KaryawanModel::select('karyawan_id')
                ->when($sttunjangankaryawan_is_all, function($q) use ($sttunjangankaryawan_is_all, $divisis, $jabatans) {
                    if($sttunjangankaryawan_is_all == 4) { // divisi
                        return $q->whereIn('ms_karyawandivisi_id', $divisis);
                    } else if($sttunjangankaryawan_is_all == 5) { // jabatan
                        return $q->whereIn('ms_karyawanjabatan_id', $jabatans);
                    }
                })
                ->where($where_karyawan)
                ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
                ->get();   
            }

            $karyawan_data = [];
            foreach($karyawans as $kr) {
                array_push($karyawan_data, [
                    'st_tunjangankaryawan_id' => $sttunjangankaryawan->sttunjangankaryawan_id,
                    'ms_karyawan_id' => $kr->karyawan_id
                ]);
            }
            
            if($karyawan_data) {
                SettingTunjanganKaryawanDetailModel::insert($karyawan_data);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Tunjangan karyawan berhasil disimpan',
                'data' => $sttunjangankaryawan
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($sttunjangankaryawanId, Request $request)
    {
        // dd($request->all());
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $sttunjangankaryawan_period = $request->input('sttunjangankaryawan_period');
        $sttunjangankaryawan_is_all = $request->input('sttunjangankaryawan_is_all');
        $active = $request->input('sttunjangankaryawan_active');
        // $receivelate = $request->input('sttunjangankaryawan_recievelate');
        // $receiveabsence = $request->input('sttunjangankaryawan_recieveabsence');
        $taxable = $request->input('sttunjangankaryawan_taxable');
        $employees = $request->input('sttunjangankaryawan_employees');
        $divisis = $request->input('sttunjangankaryawan_divisis');
        $jabatans = $request->input('sttunjangankaryawan_jabatans');
        $grouptunjangan = $request->input('sttunjangankaryawan_group');
        $deletedemployee_ids = $request->input('deletedemployee_ids');
        // $type = $request->input('sttunjangankaryawan_type');
        // $calculation = $request->input('sttunjangankaryawan_calculation');
        $formula = $request->input('sttunjangankaryawan_formula');
        // dd($formula);

        $rules = [
            'sttunjangankaryawan_name' => 'required',
            'sttunjangankaryawan_period' => 'required|in:HARI,MINGGU,BULAN,TAHUN',
            // 'sttunjangankaryawan_value' => 'required|integer',
            'sttunjangankaryawan_group' => 'required',
            // 'sttunjangankaryawan_recievelate' => 'required|in:0,1',
            // 'sttunjangankaryawan_recieveabsence' => 'required|in:0,1',
            'sttunjangankaryawan_taxable' => 'required|in:0,1',
            'sttunjangankaryawan_active' => 'required|in:0,1',
            // 'sttunjangankaryawan_calculation' => 'required|in:JUMLAH_TETAP,FORMULA',
        ];

        if($sttunjangankaryawan_period == 'TAHUN') {
            $rules['sttunjangankaryawan_paymentperiod'] = 'required|integer|min:1|max:12';
            // $rules['sttunjangankaryawan_type'] = 'required|in:BONUS,THR';
        }
        
        // if($calculation == 'JUMLAH_TETAP') {
        //     $rules['sttunjangankaryawan_value'] = 'required|integer';
        // } else if($calculation == 'FORMULA') {
        //     $rules['sttunjangankaryawan_formula'] = 'required';
        // }

        $this->validate($request, $rules, [
            'sttunjangankaryawan_value.required' => 'Nilai tunjangan wajib diisi!',
            'sttunjangankaryawan_value.integer' => 'Nilai tunjangan harus angka!',
            'sttunjangankaryawan_group.required' => 'Label slip gaji wajib diisi!',
            'sttunjangankaryawan_period.required' => 'Periode tunjangan wajib diisi!',
            'sttunjangankaryawan_paymentperiod.required' => 'Periode pembayaran tunjangan wajib diisi!',
            'sttunjangankaryawan_calculation.required' => 'Kalkulasi tunjangan wajib diisi!',
            // 'sttunjangankaryawan_recievelate.required' => 'Tunjangan diterima walaupun telat wajib diisi!',
            // 'sttunjangankaryawan_recieveabsence.required' => 'Tunjangan diterima walaupun absen wajib diisi!',
            'sttunjangankaryawan_taxable.required' => 'Tunjangan kena pajak wajib diisi!',
            'sttunjangankaryawan_active.required' => 'Status tunjangan wajib diisi!',
            // 'sttunjangankaryawan_type.required' => 'Tipe wajib diisi!',
        ]);

        if(!$sttunjangankaryawan_is_all) {
            if(!$employees) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan pilih karyawan.',
                ]);       
            }
        }
        
        $sttunjangankaryawan = SettingTunjanganKaryawanModel::where([
            'sttunjangankaryawan_id' => $sttunjangankaryawanId, 
            // 'sttunjangankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->with(['sttunjangandetail'])->first();
        if(!$sttunjangankaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Tunjangan karyawan tidak ditemukan!'
            ]);
        }

        if($employees) {
            // check if the employees belong to entities
            $karyawans = KaryawanModel::select('karyawan_id', 'st_tunjangan_id')
            ->where([
                'karyawan_active' => 1,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])
            ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
            ->whereIn('karyawan_id', $employees)->get();

        }

        if($deletedemployee_ids) {
            // check if the deleted employees belong to entities
            $deletedkaryawans = KaryawanModel::select('karyawan_id', 'st_tunjangan_id')
            ->where([
                'karyawan_active' => 1,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])
            ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
            ->whereIn('karyawan_id', $deletedemployee_ids)->get();
        }

        $tunjanganValue = 0;
        $tunjanganFormula = null;
        $grouptunjanganformulaids = [];
        $tunjanganformulaids = [];

        $calculation = $sttunjangankaryawan->sttunjangankaryawan_calculation;

        if($calculation === 'JUMLAH_TETAP') {
            $tunjanganValue = $request->input('sttunjangankaryawan_value');
        } else {
            $tunjanganFormula = ($formula) ? json_encode($formula) : null;
            if($formula) {
                foreach($formula as $fm) {
                    if(isset($fm['value']) && is_numeric($fm['value'])) {
                        array_push($grouptunjanganformulaids, $fm['value']);
                    }
                }
            }
            // dd($grouptunjanganformulaids);

            if(count($grouptunjanganformulaids) > 0) {
                $formulatunjangan = SettingTunjanganKaryawanModel::select('sttunjangankaryawan_id')
                ->whereIn('st_grouptunjangankaryawan_id', $grouptunjanganformulaids)
                ->where([
                    'sttunjangankaryawan_calculation' => 'JUMLAH_TETAP', 
                    'sttunjangankaryawan_active' => '1',
                    'ms_wajibpajak_id' => $wajibpajak_id
                ])->get();
                // dd($formulatunjangan);

                if(count($formulatunjangan) > 0) {
                    foreach($formulatunjangan as $fmula) {
                        array_push($tunjanganformulaids, $fmula->sttunjangankaryawan_id);
                    }
                }
            }
        }
        //     // Define a regular expression pattern to match variable names
        //     $pattern = '/\b\w+\b/';

        //     $formula = $request->input('sttunjangankaryawan_formula');

        //     // Use preg_match_all to find all matches in the input string
        //     preg_match_all($pattern, $formula, $matches);

        //     $variableNames = $matches[0];
            

        //     foreach ($variableNames as $variableName) {
        //         // Query the database for records where stgrouptunjangankaryawan_name matches $variableName
        //         $records = SettingGroupTunjanganKaryawanModel::where('stgrouptunjangankaryawan_name', $variableName)->where('ms_wajibpajak_id', $wajibpajak_id)
        //             ->pluck('stgrouptunjangankaryawan_id'); // Use pluck to retrieve only the stgrouptunjangankaryawan_id
                

        //         if ($records->count() > 0) {
        //             $formula = str_replace($variableName, 'COMP'.$records->first(), $formula);
        //         }
        //     }

        //     $tunjanganFormula = $formula;
        // }
        $divisis_jabatan = null;
        if($sttunjangankaryawan_is_all == 4) {
            $divisis_jabatan = implode(',', $divisis);
        } else if($sttunjangankaryawan_is_all == 5) {
            $divisis_jabatan = implode(',', $jabatans);
        }
        $save_data = [
            'sttunjangankaryawan_name' => $request->input('sttunjangankaryawan_name'),
            'sttunjangankaryawan_code' => strtoupper(str_replace(' ', '_', $request->input('sttunjangankaryawan_name'))),
            'sttunjangankaryawan_value' => $tunjanganValue,
            'sttunjangankaryawan_formula' => $tunjanganFormula,
            'st_sttunjangankaryawan_id' => (count($tunjanganformulaids) > 0) ? implode(',', $tunjanganformulaids) : null,
            'sttunjangankaryawan_period' => $sttunjangankaryawan_period,
            'sttunjangankaryawan_paymentperiod' => $sttunjangankaryawan_period == 'TAHUN' ? intval($request->input('sttunjangankaryawan_paymentperiod')) : 0,
            // 'sttunjangankaryawan_paymentperiod' => 0,
            // 'sttunjangankaryawan_calculation' => $request->input('sttunjangankaryawan_calculation'),
            'sttunjangankaryawan_active' => $active,
            // 'sttunjangankaryawan_recievelate' => $receivelate,
            // 'sttunjangankaryawan_recieveabsence' => $receiveabsence,
            'sttunjangankaryawan_taxable' => $taxable,
            'sttunjangankaryawan_is_all' => $sttunjangankaryawan_is_all == 1,
            'sttunjangankaryawan_used_for' => $sttunjangankaryawan_is_all,
            'sttunjangankaryawan_division_jabatan' => $divisis_jabatan,
            // 'sttunjangankaryawan_type' => $type,
        ];
        
        DB::beginTransaction();
        try {
            // check group exist
            if(intval($grouptunjangan) <= 0) {
                // insert new group
                $group = SettingGroupTunjanganKaryawanModel::create([
                    'stgrouptunjangankaryawan_name' => ucwords($grouptunjangan),
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ]);
            } else {
                $group = SettingGroupTunjanganKaryawanModel::where([
                    'stgrouptunjangankaryawan_id' => $grouptunjangan,
                    'stgrouptunjangankaryawan_active' => 1,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])->first();
            }
            $save_data['st_grouptunjangankaryawan_id'] = $group->stgrouptunjangankaryawan_id;

            $sttunjangankaryawan->update($save_data);

            $karyawan_data = [];
            $existing_karyawan_ids = [];
            $temp_existing_karyawan_ids = [];
            // check existing data detail
            if($sttunjangankaryawan->sttunjangandetail) {
                foreach($sttunjangankaryawan->sttunjangandetail as $existdet) {
                    array_push($temp_existing_karyawan_ids, $existdet->ms_karyawan_id);
                }
            }
            $where_karyawan = [
                'karyawan_active' => '1',
                'ms_wajibpajak_id' => $wajibpajak_id,
            ];
            if(in_array($sttunjangankaryawan_is_all, [1,2,3,4,5])) {
                if($sttunjangankaryawan_is_all == 2) { // perempuan
                    $where_karyawan['karyawan_gender'] = 'P';
                } else if($sttunjangankaryawan_is_all == 3) { // laki-laki
                    $where_karyawan['karyawan_gender'] = 'L';
                }
                // get all employees
                $karyawans = KaryawanModel::select('karyawan_id')
                ->when($sttunjangankaryawan_is_all, function($q) use ($sttunjangankaryawan_is_all, $divisis, $jabatans) {
                    if($sttunjangankaryawan_is_all == 4) { // divisi
                        return $q->whereIn('ms_karyawandivisi_id', $divisis);
                    } else if($sttunjangankaryawan_is_all == 5) { // jabatan
                        return $q->whereIn('ms_karyawanjabatan_id', $jabatans);
                    }
                })
                ->where($where_karyawan)
                ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
                ->get();

                // check existing data detail
                // dd($sttunjangankaryawan->sttunjangandetail);
                if($sttunjangankaryawan->sttunjangandetail) {
                    foreach($sttunjangankaryawan->sttunjangandetail as $existdet) {
                        // dd($existdet->karyawan);
                        if($sttunjangankaryawan_is_all == 2) { // perempuan
                            if($existdet->karyawan->karyawan_gender == 'P') {
                                array_push($existing_karyawan_ids, $existdet->ms_karyawan_id);
                            }
                        } else if($sttunjangankaryawan_is_all == 3) { // laki-laki
                            if($existdet->karyawan->karyawan_gender == 'L') {
                                array_push($existing_karyawan_ids, $existdet->ms_karyawan_id);
                            }
                        } else if($sttunjangankaryawan_is_all == 4) { // divisi
                            if(in_array($existdet->karyawan->ms_karyawandivisi_id, $divisis)) {
                                array_push($existing_karyawan_ids, $existdet->ms_karyawan_id);
                            }
                        } else if($sttunjangankaryawan_is_all == 5) { // jabatan
                            if(in_array($existdet->karyawan->ms_karyawanjabatan_id, $jabatans)) {
                                array_push($existing_karyawan_ids, $existdet->ms_karyawan_id);
                            }
                        } else { // semua karyawan
                            array_push($existing_karyawan_ids, $existdet->ms_karyawan_id);
                        }
                        
                    }
                    
                    // dd($temp_existing_karyawan_ids, $existing_karyawan_ids, $karyawans);
                    foreach($karyawans as $kr) {
                        if(!in_array($kr->karyawan_id, $temp_existing_karyawan_ids)) {
                            array_push($karyawan_data, [
                                'st_tunjangankaryawan_id' => $sttunjangankaryawan->sttunjangankaryawan_id,
                                'ms_karyawan_id' => $kr->karyawan_id
                            ]);
                        }
                    }
                    if($sttunjangankaryawan_is_all == 1) { 
                        $existing_karyawan_ids = $temp_existing_karyawan_ids;
                    }

                    SettingTunjanganKaryawanDetailModel::
                        where(['st_tunjangankaryawan_id' => $sttunjangankaryawan->sttunjangankaryawan_id])
                        ->whereNotIn('ms_karyawan_id', $existing_karyawan_ids)
                        ->update(['sttunjangankaryawandet_active' => '0']);
                }
                // dd($existing_karyawan_ids, $temp_existing_karyawan_ids);
                
            } else {
                if(is_array($employees) && count($employees) > 0) {
                    $karyawans = KaryawanModel::select('karyawan_id')->where($where_karyawan)->whereIn('karyawan_id', $employees)
                    ->whereNotIn('karyawan_status', ['NONKARYAWAN'])->get();
                    foreach($karyawans as $kr) {
                        if(!in_array($kr->karyawan_id, $temp_existing_karyawan_ids)) {
                            array_push($karyawan_data, [
                                'st_tunjangankaryawan_id' => $sttunjangankaryawan->sttunjangankaryawan_id,
                                'ms_karyawan_id' => $kr->karyawan_id
                            ]);
                        } else {
                            array_push($existing_karyawan_ids, $kr->karyawan_id);
                        }
                    }
    
                    if($existing_karyawan_ids) {
                        SettingTunjanganKaryawanDetailModel::
                        where(['st_tunjangankaryawan_id' => $sttunjangankaryawan->sttunjangankaryawan_id])
                        ->whereNotIn('ms_karyawan_id', $existing_karyawan_ids)
                        ->update(['sttunjangankaryawandet_active' => '0']);
                    }
                } 

                $deletedrealemployee_ids = [];
                if(isset($deletedkaryawans)) { // deleted employee
                    foreach($deletedkaryawans as $dkr) {
                        array_push($deletedrealemployee_ids, $dkr->karyawan_id);
                    }
                    if($deletedrealemployee_ids) {
                        SettingTunjanganKaryawanDetailModel::whereIn('ms_karyawan_id', $deletedrealemployee_ids)
                        ->update([
                            'sttunjangankaryawandet_active' => 0
                        ]);
                    }

                    // dd($deletedrealemployee_ids);
                }
            }

            // update existing
            if($existing_karyawan_ids) {
                SettingTunjanganKaryawanDetailModel::
                where(['st_tunjangankaryawan_id' => $sttunjangankaryawan->sttunjangankaryawan_id])
                ->whereIn('ms_karyawan_id', $existing_karyawan_ids)
                ->update(['sttunjangankaryawandet_active' => '1']);
            }
            
            if($active == 1) {
                // new insert
                if($karyawan_data) {
                    SettingTunjanganKaryawanDetailModel::insert($karyawan_data);
                }
            } else {
                // delete detail
                SettingTunjanganKaryawanDetailModel::where(['st_tunjangankaryawan_id' => $sttunjangankaryawan->sttunjangankaryawan_id])
                ->update([
                    'sttunjangankaryawandet_active' => 0
                ]);
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Tunjangan karyawan berhasil disimpan',
                'data' => $sttunjangankaryawan
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($sttunjangankaryawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $sttunjangankaryawan = SettingTunjanganKaryawanModel::where([
            'sttunjangankaryawan_id' => $sttunjangankaryawanId, 
            'sttunjangankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$sttunjangankaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Tunjangan karyawan tidak ditemukan!'
            ]); 
        }
        
        DB::beginTransaction();
        try {
            $sttunjangankaryawan->update(['sttunjangankaryawan_active' => 0]);

            // delete detail
            SettingTunjanganKaryawanDetailModel::where([
                'st_tunjangankaryawan_id' => $sttunjangankaryawan->sttunjangankaryawan_id,
            ])
            ->update([
                'sttunjangankaryawandet_active' => 0
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Tunjangan karyawan berhasil dihapus',
                'data' => $sttunjangankaryawan
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display a select of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $q = $request->get('q');
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $data = SettingTunjanganKaryawanModel::select('sttunjangankaryawan_id', 'sttunjangankaryawan_name')
        ->when($q, function ($query, $q) {
            return $query->where('sttunjangankaryawan_name', 'ilike', '%'.$q.'%');
        })
        ->where([
            'sttunjangankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    /**
     * Display a listkaryawan of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listkaryawan($sttunjangankaryawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $data = SettingTunjanganKaryawanDetailModel::select('karyawan_id', 'karyawan_name')
        ->join('ms_karyawan', 'karyawan_id', 'ms_karyawan_id')
        ->where([
            'st_tunjangankaryawan_id' => $sttunjangankaryawanId,
            'karyawan_active' => 1,
            'sttunjangankaryawandet_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    /**
     * Display a listdivisijabatan of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listdivisijabatan($sttunjangankaryawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $sttunjangankaryawan = SettingTunjanganKaryawanModel::select('sttunjangankaryawan_id', 'sttunjangankaryawan_used_for', 'sttunjangankaryawan_division_jabatan')
        ->where([
            'sttunjangankaryawan_id' => $sttunjangankaryawanId,
            'sttunjangankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        
        $data = [];
        if($sttunjangankaryawan) {
            if($sttunjangankaryawan->sttunjangankaryawan_used_for == 4) {
                $data = KaryawanDivisiModel::select('karyawandivisi_id', 'karyawandivisi_name')
                ->where([
                    'karyawandivisi_active' => 1,
                ])->whereIn('karyawandivisi_id', explode(',', $sttunjangankaryawan->sttunjangankaryawan_division_jabatan))->get();
            } else if($sttunjangankaryawan->sttunjangankaryawan_used_for == 5) {
                $data = KaryawanJabatanModel::select('karyawanjabatan_id', 'karyawanjabatan_name')
                ->where([
                    'karyawanjabatan_active' => 1,
                ])->whereIn('karyawanjabatan_id', explode(',', $sttunjangankaryawan->sttunjangankaryawan_division_jabatan))->get();
            }
        }
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}