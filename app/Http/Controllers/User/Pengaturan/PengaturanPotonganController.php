<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanDivisiModel;
use App\Model\Master\KaryawanJabatanModel;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingGroupPotonganKaryawanModel;
use App\Model\Setting\SettingPotonganKaryawanDetailModel;
use App\Model\Setting\SettingPotonganKaryawanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengaturanPotonganController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Pengaturan Potongan',
            'content' => 'user.pengaturan.potongan.index',
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
        $order_columns = ['stpotongankaryawan_id','stpotongankaryawan_name','stpotongankaryawan_type','stpotongankaryawan_method','stpotongankaryawan_value','stpotongankaryawan_active'];
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
        $list = SettingPotonganKaryawanModel::select('st_potongan_karyawan.*'
            , DB::raw("(SELECT (SELECT json_agg(ttable) 
            FROM (
                SELECT stgrouptunjangankaryawan_id, stgrouptunjangankaryawan_name
                FROM st_group_tunjangan_karyawan
                WHERE stgrouptunjangankaryawan_id = ANY(string_to_array(REPLACE(stpotongankaryawan_formula, 'GP','0'), ',')::int[])
                AND st_group_tunjangan_karyawan.ms_wajibpajak_id = st_potongan_karyawan.ms_wajibpajak_id
                AND stgrouptunjangankaryawan_active = '1'
                ORDER BY stgrouptunjangankaryawan_id ASC
            )
            as ttable)) as grouptunjangan_json")
            , DB::raw("(SELECT (SELECT json_agg(pttable) 
            FROM (
                SELECT stgrouptunjangankaryawan_id, stgrouptunjangankaryawan_name
                FROM st_group_tunjangan_karyawan
                WHERE stgrouptunjangankaryawan_id = ANY(string_to_array(REPLACE(stpotongankaryawan_maxtypeformula, 'GP','0'), ',')::int[])
                AND st_group_tunjangan_karyawan.ms_wajibpajak_id = st_potongan_karyawan.ms_wajibpajak_id
                AND stgrouptunjangankaryawan_active = '1'
                ORDER BY stgrouptunjangankaryawan_id ASC
            )
            as pttable)) as persentasegrouptunjangan_json")
        )
        ->when($search, function ($q, $search) {
            $lowercaseSearch = strtolower($search);
            return $q->where(function ($query) use ($lowercaseSearch) {
                $query->where(DB::raw('LOWER(stpotongankaryawan_id::text)'), 'like', '%' . $lowercaseSearch . '%')
                ->orWhere(DB::raw('LOWER(stpotongankaryawan_name)'), 'like', '%' . $lowercaseSearch . '%');
            });
        })
        ->with(['stgrouppotongan'
        // , 'stpotongandetail',
        // 'stpotongandetailaktif.karyawan' => function($query) {
        //     $query->select('karyawan_id', 'karyawan_name');
        // }
        ])
        ->where($where)
        ->orderBy($order_col, $order_type)
        ->take($limit)->skip($offset)->get();

        foreach($list as &$ls) {
            // $ls->karyawans = ($ls->karyawan_json) ? json_decode($ls->karyawan_json) : [];
            // unset($ls->karyawan_json);
            $ls->grouptunjangans = ($ls->grouptunjangan_json) ? json_decode($ls->grouptunjangan_json) : [];
            unset($ls->grouptunjangan_json);
            $ls->persentasegrouptunjangans = ($ls->persentasegrouptunjangan_json) ? json_decode($ls->persentasegrouptunjangan_json) : [];
            unset($ls->persentasegrouptunjangan_json);
        }
        
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = SettingPotonganKaryawanModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    /**
     * Show Data of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($stpotongankaryawanId, Request $request)
    {
        // $where = [
        //     'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        //     'stpotongankaryawan_id' => $stpotongankaryawanId,
        // ];
        // $list = SettingPotonganKaryawanModel::select('st_potongan_karyawan.*'
        //     , DB::raw("(SELECT (SELECT json_agg(ktable) 
        //     FROM (
        //         SELECT karyawan_id, karyawan_name
        //         FROM ms_karyawan
        //         WHERE stpotongankaryawan_id = ANY(string_to_array(st_potongan_id, ',')::int[])
        //         AND ms_karyawan.ms_wajibpajak_id = st_potongan_karyawan.ms_wajibpajak_id
        //         AND karyawan_active = '1'
        //         ORDER BY karyawan_id ASC
        //     )
        //     as ktable)) as karyawan_json")
        //     , DB::raw("(SELECT (SELECT json_agg(ttable) 
        //     FROM (
        //         SELECT stgrouptunjangankaryawan_id, stgrouptunjangankaryawan_name
        //         FROM st_group_tunjangan_karyawan
        //         WHERE stgrouptunjangankaryawan_id = ANY(string_to_array(REPLACE(stpotongankaryawan_formula, 'GP','0'), ',')::int[])
        //         AND st_group_tunjangan_karyawan.ms_wajibpajak_id = st_potongan_karyawan.ms_wajibpajak_id
        //         AND stgrouptunjangankaryawan_active = '1'
        //         ORDER BY stgrouptunjangankaryawan_id ASC
        //     )
        //     as ttable)) as grouptunjangan_json")
        //     , DB::raw("(SELECT (SELECT json_agg(pttable) 
        //     FROM (
        //         SELECT stgrouptunjangankaryawan_id, stgrouptunjangankaryawan_name
        //         FROM st_group_tunjangan_karyawan
        //         WHERE stgrouptunjangankaryawan_id = ANY(string_to_array(REPLACE(stpotongankaryawan_maxtypeformula, 'GP','0'), ',')::int[])
        //         AND st_group_tunjangan_karyawan.ms_wajibpajak_id = st_potongan_karyawan.ms_wajibpajak_id
        //         AND stgrouptunjangankaryawan_active = '1'
        //         ORDER BY stgrouptunjangankaryawan_id ASC
        //     )
        //     as pttable)) as persentasegrouptunjangan_json")
        // )
        // ->with(['stgrouppotongan'])
        // ->where($where)
        // ->first();

        // $list->karyawans = ($list->karyawan_json) ? json_decode($list->karyawan_json) : [];
        // unset($list->karyawan_json);
        // $list->grouptunjangans = ($list->grouptunjangan_json) ? json_decode($list->grouptunjangan_json) : [];
        // unset($list->grouptunjangan_json);
        // $list->persentasegrouptunjangans = ($list->persentasegrouptunjangan_json) ? json_decode($list->persentasegrouptunjangan_json) : [];
        // unset($list->persentasegrouptunjangan_json);
        
        // return response()->json([
        //     'success' => true,
        //     'data' => $list
        // ]);
    }

    public function store(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        // dd($request->all());

        $active = $request->input('stpotongankaryawan_active');
        $stpotongankaryawan_is_all = $request->input('stpotongankaryawan_is_all');
        $taxable = $request->input('stpotongankaryawan_taxable');
        $employees = $request->input('stpotongankaryawan_employees');
        $divisis = $request->input('stpotongankaryawan_divisis');
        $jabatans = $request->input('stpotongankaryawan_jabatans');
        $type = $request->input('stpotongankaryawan_type');
        // $maxtype = $request->input('stpotongankaryawan_maxtype');
        // $maxtypevalue = $request->input('stpotongankaryawan_maxtypevalue');
        // $maxtypeformula = $request->input('stpotongankaryawan_maxtypevalueformula');
        $method = $request->input('stpotongankaryawan_method');
        $accumulationtime = $request->input('stpotongankaryawan_accumulationtime');
        $grouppotongan = $request->input('stpotongankaryawan_group');
        // dd($groups);
        $rules = [
            'stpotongankaryawan_name' => 'required',
            'stpotongankaryawan_group' => 'required',
            'stpotongankaryawan_type' => 'required',
            'stpotongankaryawan_method' => 'required',
            'stpotongankaryawan_taxable' => 'required|in:0,1',
            'stpotongankaryawan_active' => 'required|in:0,1',
        ];

        if($method === 'PRORATA') {
            $rules['stpotongankaryawan_formula'] = 'required';
        } else {
            $rules['stpotongankaryawan_value'] = 'required|integer';
        }
        // if($maxtype !== 'TIDAK_BERLAKU') {
            // $rules['stpotongankaryawan_maxtypevalue'] = 'required';
        // }
        // if($maxtype === 'PERSEN') {
            // $rules['stpotongankaryawan_maxtypevalueformula'] = 'required';
        // }

        if($method != 'BULAN') 
        {
            // $rules['stpotongankaryawan_maxtype'] = 'required|in:TIDAK_BERLAKU';
        // } else {
            // $rules['stpotongankaryawan_maxtype'] = 'required|in:TETAP,PERSEN,TIDAK_BERLAKU';
            
            if($method === 'KELIPATAN_HARI' || $method === 'KELIPATAN_BULAN') {
                $rules['stpotongankaryawan_accumulationtime'] = 'required';
            }
        }
        $this->validate($request, $rules, [
            'stpotongankaryawan_name.required' => 'Nama potongan wajib diisi!',
            'stpotongankaryawan_value.required' => 'Nilai potongan wajib diisi!',
            'stpotongankaryawan_value.integer' => 'Nilai potongan harus angka!',
            'stpotongankaryawan_group.required' => 'Label slip gaji wajib diisi!',
            'stpotongankaryawan_type.required' => 'Jenis potongan wajib diisi!',
            'stpotongankaryawan_method.required' => 'Metode potongan wajib diisi!',
            'stpotongankaryawan_taxable.required' => 'Potongan kena pajak wajib diisi!',
            'stpotongankaryawan_active.required' => 'Status potongan wajib diisi!',
        ]);

        if(!$stpotongankaryawan_is_all) {
            if(!$employees) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan pilih karyawan.',
                ]);       
            }
        }

        if($employees) {
            // check if the employees belong to entities
            $karyawans = KaryawanModel::select('karyawan_id', 'st_potongan_id')
            ->where([
                'karyawan_active' => 1,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])
            ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
            ->whereIn('karyawan_id', $employees)->get();
        }

        $potongan_value = 0;
        $potongan_formula = null;

        if($request->input('stpotongankaryawan_method') != 'PRORATA') {
            $potongan_value = $request->input('stpotongankaryawan_value');
        } else {
            $potongan_formula = $request->input('stpotongankaryawan_formula');
        }

        $divisis_jabatan = null;
        if($stpotongankaryawan_is_all == 4) {
            $divisis_jabatan = implode(',', $divisis);
        } else if($stpotongankaryawan_is_all == 5) {
            $divisis_jabatan = implode(',', $jabatans);
        }
        $save_data = [
            'stpotongankaryawan_name' => ucwords($request->input('stpotongankaryawan_name')),
            'stpotongankaryawan_code' => strtoupper(str_replace(' ', '_', $request->input('stpotongankaryawan_name'))),
            'stpotongankaryawan_value' => $potongan_value,
            'stpotongankaryawan_formula' => ($potongan_formula) ? implode(',', $potongan_formula) : null,
            'stpotongankaryawan_type' => $type,
            'stpotongankaryawan_method' => $method,
            'ms_wajibpajak_id' => $wajibpajak_id,
            'stpotongankaryawan_active' => $active,
            'stpotongankaryawan_taxable' => $taxable,
            // 'stpotongankaryawan_maxtype' => $maxtype,
            // 'stpotongankaryawan_maxtypevalue' => $maxtypevalue,
            // 'stpotongankaryawan_maxtypeformula' => ($maxtypeformula) ? implode(',', $maxtypeformula) : null,
            'stpotongankaryawan_accumulationtime' => $accumulationtime,
            'stpotongankaryawan_is_all' => $stpotongankaryawan_is_all == 1,
            'stpotongankaryawan_used_for' => $stpotongankaryawan_is_all,
            'stpotongankaryawan_division_jabatan' => $divisis_jabatan,
        ];

        DB::beginTransaction();
        try {
            // check group exist
            if(intval($grouppotongan) <= 0) {
                // insert new group
                $group = SettingGroupPotonganKaryawanModel::create([
                    'stgrouppotongankaryawan_name' => ucwords($grouppotongan),
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ]);
            } else {
                $group = SettingGroupPotonganKaryawanModel::where([
                    'stgrouppotongankaryawan_id' => $grouppotongan,
                    'stgrouppotongankaryawan_active' => 1,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])->first();
            }
            $save_data['st_grouppotongankaryawan_id'] = $group->stgrouppotongankaryawan_id;
            
            $stpotongankaryawan = SettingPotonganKaryawanModel::create($save_data);

            if(in_array($stpotongankaryawan_is_all, [1,2,3,4,5])) {
                // get all employees
                $where_karyawan = [
                    'karyawan_active' => '1',
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ];
                if($stpotongankaryawan_is_all == 2) { // perempuan
                    $where_karyawan['karyawan_gender'] = 'P';
                } else if($stpotongankaryawan_is_all == 3) { // laki-laki
                    $where_karyawan['karyawan_gender'] = 'L';
                }
                $karyawans = KaryawanModel::select('karyawan_id')
                ->when($stpotongankaryawan_is_all, function($q) use ($stpotongankaryawan_is_all, $divisis, $jabatans) {
                    if($stpotongankaryawan_is_all == 4) { // divisi
                        return $q->whereIn('ms_karyawandivisi_id', $divisis);
                    } else if($stpotongankaryawan_is_all == 5) { // jabatan
                        return $q->whereIn('ms_karyawanjabatan_id', $jabatans);
                    }
                })
                ->where($where_karyawan)
                ->whereNotIn('karyawan_status', ['NONKARYAWAN'])->get();   
            }

            $karyawan_data = [];
            foreach($karyawans as $kr) {
                array_push($karyawan_data, [
                    'st_potongankaryawan_id' => $stpotongankaryawan->stpotongankaryawan_id,
                    'ms_karyawan_id' => $kr->karyawan_id
                ]);
            }
            
            if($karyawan_data) {
                SettingPotonganKaryawanDetailModel::insert($karyawan_data);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Potongan karyawan berhasil disimpan',
                'data' => $stpotongankaryawan
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($stpotongankaryawanId, Request $request)
    {
        // dd($request->all());
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $potongan_name = $request->input('stpotongankaryawan_name');
        $active = $request->input('stpotongankaryawan_active');
        $stpotongankaryawan_is_all = $request->input('stpotongankaryawan_is_all');
        $taxable = $request->input('stpotongankaryawan_taxable');
        $employees = $request->input('stpotongankaryawan_employees');
        $divisis = $request->input('stpotongankaryawan_divisis');
        $jabatans = $request->input('stpotongankaryawan_jabatans');
        $type = $request->input('stpotongankaryawan_type');
        // $maxtype = $request->input('stpotongankaryawan_maxtype');
        // $maxtypevalue = $request->input('stpotongankaryawan_maxtypevalue');
        // $maxtypeformula = $request->input('stpotongankaryawan_maxtypevalueformula');
        $method = $request->input('stpotongankaryawan_method');
        $accumulationtime = $request->input('stpotongankaryawan_accumulationtime');
        $grouppotongan = $request->input('stpotongankaryawan_group');
        $deletedemployee_ids = $request->input('deletedemployee_ids');
        // dd($groups);
        $rules = [
            'stpotongankaryawan_name' => 'required',
            'stpotongankaryawan_group' => 'required',
            'stpotongankaryawan_type' => 'required',
            'stpotongankaryawan_method' => 'required',
            'stpotongankaryawan_taxable' => 'required|in:0,1',
            'stpotongankaryawan_active' => 'required|in:0,1',
        ];

        if($method === 'PRORATA') {
            $rules['stpotongankaryawan_formula'] = 'required';
        } else {
            $rules['stpotongankaryawan_value'] = 'required|integer';
        }
        // if($maxtype !== 'TIDAK_BERLAKU') {
        //     $rules['stpotongankaryawan_maxtypevalue'] = 'required';
        // }
        // if($maxtype === 'PERSEN') {
        //     $rules['stpotongankaryawan_maxtypevalueformula'] = 'required';
        // }
        if($method != 'BULAN') {
        //     $rules['stpotongankaryawan_maxtype'] = 'required|in:TIDAK_BERLAKU';
        // } else {
        //     $rules['stpotongankaryawan_maxtype'] = 'required|in:TETAP,PERSEN,TIDAK_BERLAKU';
            
            if($method === 'KELIPATAN_HARI' || $method === 'KELIPATAN_BULAN') {
                $rules['stpotongankaryawan_accumulationtime'] = 'required';
            }
        }
        $this->validate($request, $rules, [
            'stpotongankaryawan_name.required' => 'Nama potongan wajib diisi!',
            'stpotongankaryawan_value.required' => 'Nilai potongan wajib diisi!',
            'stpotongankaryawan_value.integer' => 'Nilai potongan harus angka!',
            'stpotongankaryawan_group.required' => 'Label slip gaji wajib diisi!',
            'stpotongankaryawan_type.required' => 'Jenis potongan wajib diisi!',
            'stpotongankaryawan_method.required' => 'Metode potongan wajib diisi!',
            'stpotongankaryawan_taxable.required' => 'Potongan kena pajak wajib diisi!',
            'stpotongankaryawan_active.required' => 'Status potongan wajib diisi!',
        ]);

        if(!$stpotongankaryawan_is_all) {
            if(!$employees) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan pilih karyawan.',
                ]);       
            }
        }

        $stpotongankaryawan = SettingPotonganKaryawanModel::where([
            'stpotongankaryawan_id' => $stpotongankaryawanId, 
            // 'stpotongankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->with(['stpotongandetail'])->first();
        if(!$stpotongankaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Potongan karyawan tidak ditemukan!'
            ]);
        }

        if($employees) {
            // check if the employees belong to entities
            $karyawans = KaryawanModel::select('karyawan_id', 'st_potongan_id')
            ->where([
                'karyawan_active' => 1,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])
            ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
            ->whereIn('karyawan_id', $employees)->get();

        }

        if($deletedemployee_ids) {
            // check if the deleted employees belong to entities
            $deletedkaryawans = KaryawanModel::select('karyawan_id', 'st_potongan_id')
            ->where([
                'karyawan_active' => 1,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])
            ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
            ->whereIn('karyawan_id', $deletedemployee_ids)->get();
        }

        $potongan_value = 0;
        $potongan_formula = null;
        if($request->input('stpotongankaryawan_method') != 'PRORATA') {
            $potongan_value = $request->input('stpotongankaryawan_value');
        } else {
            $potongan_formula = $request->input('stpotongankaryawan_formula');
        }

        $divisis_jabatan = null;
        if($stpotongankaryawan_is_all == 4) {
            $divisis_jabatan = implode(',', $divisis);
        } else if($stpotongankaryawan_is_all == 5) {
            $divisis_jabatan = implode(',', $jabatans);
        }

        $save_data = [
            'stpotongankaryawan_name' => $potongan_name,
            'stpotongankaryawan_value' => $potongan_value,
            'stpotongankaryawan_formula' => ($potongan_formula) ? implode(',', $potongan_formula) : null,
            'stpotongankaryawan_type' => $type,
            'stpotongankaryawan_method' => $method,
            'ms_wajibpajak_id' => $wajibpajak_id,
            'stpotongankaryawan_active' => $active,
            'stpotongankaryawan_taxable' => $taxable,
            // 'stpotongankaryawan_maxtype' => $maxtype,
            // 'stpotongankaryawan_maxtypevalue' => $maxtypevalue,
            // 'stpotongankaryawan_maxtypeformula' => ($maxtypeformula) ? implode(',', $maxtypeformula) : null,
            'stpotongankaryawan_accumulationtime' => $accumulationtime,
            'stpotongankaryawan_is_all' => $stpotongankaryawan_is_all == 1,
            'stpotongankaryawan_used_for' => $stpotongankaryawan_is_all,
            'stpotongankaryawan_division_jabatan' => $divisis_jabatan,
        ];
        
        DB::beginTransaction();
        try {
            // check group exist
            if(intval($grouppotongan) <= 0) {
                // insert new group
                $group = SettingGroupPotonganKaryawanModel::create([
                    'stgrouppotongankaryawan_name' => ucwords($grouppotongan),
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ]);
            } else {
                $group = SettingGroupPotonganKaryawanModel::where([
                    'stgrouppotongankaryawan_id' => $grouppotongan,
                    'stgrouppotongankaryawan_active' => 1,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])->first();
            }
            $save_data['st_grouppotongankaryawan_id'] = $group->stgrouppotongankaryawan_id;

            $stpotongankaryawan->update($save_data);

            $karyawan_data = [];
            $existing_karyawan_ids = [];
            $temp_existing_karyawan_ids = [];
            // check existing data detail
            if($stpotongankaryawan->stpotongandetail) {
                foreach($stpotongankaryawan->stpotongandetail as $existdet) {
                    array_push($temp_existing_karyawan_ids, $existdet->ms_karyawan_id);
                }
            }
            if(in_array($stpotongankaryawan_is_all, [1,2,3,4,5])) {
                // get all employees
                $where_karyawan = [
                    'karyawan_active' => '1',
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ];
                if($stpotongankaryawan_is_all == 2) { // perempuan
                    $where_karyawan['karyawan_gender'] = 'P';
                } else if($stpotongankaryawan_is_all == 3) { // laki-laki
                    $where_karyawan['karyawan_gender'] = 'L';
                }
                $karyawans = KaryawanModel::select('karyawan_id')
                ->when($stpotongankaryawan_is_all, function($q) use ($stpotongankaryawan_is_all, $divisis, $jabatans) {
                    if($stpotongankaryawan_is_all == 4) { // divisi
                        return $q->whereIn('ms_karyawandivisi_id', $divisis);
                    } else if($stpotongankaryawan_is_all == 5) { // jabatan
                        return $q->whereIn('ms_karyawanjabatan_id', $jabatans);
                    }
                })
                ->where($where_karyawan)
                ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
                ->get();

                // check existing data detail
                if($stpotongankaryawan->stpotongandetail) {
                    foreach($stpotongankaryawan->stpotongandetail as $existdet) {
                        // dd($existdet->karyawan);
                        if($stpotongankaryawan_is_all == 2) { // perempuan
                            if($existdet->karyawan->karyawan_gender == 'P') {
                                array_push($existing_karyawan_ids, $existdet->ms_karyawan_id);
                            }
                        } else if($stpotongankaryawan_is_all == 3) { // laki-laki
                            if($existdet->karyawan->karyawan_gender == 'L') {
                                array_push($existing_karyawan_ids, $existdet->ms_karyawan_id);
                            }
                        } else if($stpotongankaryawan_is_all == 4) { // divisi
                            if(in_array($existdet->karyawan->ms_karyawandivisi_id, $divisis)) {
                                array_push($existing_karyawan_ids, $existdet->ms_karyawan_id);
                            }
                        } else if($stpotongankaryawan_is_all == 5) { // jabatan
                            if(in_array($existdet->karyawan->ms_karyawanjabatan_id, $jabatans)) {
                                array_push($existing_karyawan_ids, $existdet->ms_karyawan_id);
                            }
                        } else { // semua karyawan
                            array_push($existing_karyawan_ids, $existdet->ms_karyawan_id);
                        }
                    }
                }
                foreach($karyawans as $kr) {
                    if(!in_array($kr->karyawan_id, $temp_existing_karyawan_ids)) {
                        array_push($karyawan_data, [
                            'st_potongankaryawan_id' => $stpotongankaryawan->stpotongankaryawan_id,
                            'ms_karyawan_id' => $kr->karyawan_id
                        ]);
                    }
                }
                // $existing_karyawan_ids = $temp_existing_karyawan_ids;
                if($stpotongankaryawan_is_all == 1) { 
                    $existing_karyawan_ids = $temp_existing_karyawan_ids;
                }

                SettingPotonganKaryawanDetailModel::
                    where(['st_potongankaryawan_id' => $stpotongankaryawan->stpotongankaryawan_id])
                    ->whereNotIn('ms_karyawan_id', $existing_karyawan_ids)
                    ->update(['stpotongankaryawandet_active' => '0']);
            } else {

                foreach($karyawans as $kr) {
                    if(!in_array($kr->karyawan_id, $temp_existing_karyawan_ids)) {
                        array_push($karyawan_data, [
                            'st_potongankaryawan_id' => $stpotongankaryawan->stpotongankaryawan_id,
                            'ms_karyawan_id' => $kr->karyawan_id
                        ]);
                    } else {
                        array_push($existing_karyawan_ids, $kr->karyawan_id);
                    }
                }
                if($existing_karyawan_ids) {
                    SettingPotonganKaryawanDetailModel::
                    where(['st_potongankaryawan_id' => $stpotongankaryawan->stpotongankaryawan_id])
                    ->whereNotIn('ms_karyawan_id', $existing_karyawan_ids)
                    ->update(['stpotongankaryawandet_active' => '0']);
                }

                $deletedrealemployee_ids = [];
                if(isset($deletedkaryawans)) { // deleted employee
                    foreach($deletedkaryawans as $dkr) {
                        array_push($deletedrealemployee_ids, $dkr->karyawan_id);
                    }
                    if($deletedrealemployee_ids) {
                        SettingPotonganKaryawanDetailModel::whereIn('ms_karyawan_id', $deletedrealemployee_ids)
                        ->update([
                            'stpotongankaryawandet_active' => 0
                        ]);
                    }

                    // dd($deletedrealemployee_ids);
                }
            }

            // update existing
            if($existing_karyawan_ids) {
                SettingPotonganKaryawanDetailModel::
                where(['st_potongankaryawan_id' => $stpotongankaryawan->stpotongankaryawan_id])
                ->whereIn('ms_karyawan_id', $existing_karyawan_ids)
                ->update(['stpotongankaryawandet_active' => '1']);
            }
            
            // new insert
            if($active == 1) {
                if($karyawan_data) {
                    SettingPotonganKaryawanDetailModel::insert($karyawan_data);
                }
            } else {
                // delete detail
                SettingPotonganKaryawanDetailModel::where(['st_potongankaryawan_id' => $stpotongankaryawan->stpotongankaryawan_id])
                ->update([
                    'stpotongankaryawandet_active' => 0
                ]);
            }
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Potongan karyawan berhasil disimpan',
                'data' => $stpotongankaryawan
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete($stpotongankaryawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $stpotongankaryawan = SettingPotonganKaryawanModel::where([
            'stpotongankaryawan_id' => $stpotongankaryawanId, 
            'stpotongankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$stpotongankaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Potongan karyawan tidak ditemukan!'
            ]); 
        }
        
        DB::beginTransaction();
        try {
            $stpotongankaryawan->update(['stpotongankaryawan_active' => 0]);
            // delete detail
            SettingPotonganKaryawanDetailModel::where(['st_potongankaryawan_id' => $stpotongankaryawan->stpotongankaryawan_id])
            ->update([
                'stpotongankaryawandet_active' => 0
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Potongan karyawan berhasil dihapus',
                'data' => $stpotongankaryawan
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

        $data = SettingPotonganKaryawanModel::select('stpotongankaryawan_id', 'stpotongankaryawan_name')
        ->when($q, function ($query, $q) {
            return $query->where('stpotongankaryawan_name', 'ilike', '%'.$q.'%');
        })
        ->where([
            'stpotongankaryawan_active' => 1,
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
    public function listkaryawan($stpotongankaryawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $data = SettingPotonganKaryawanDetailModel::select('karyawan_id', 'karyawan_name')
        ->join('ms_karyawan', 'karyawan_id', 'ms_karyawan_id')
        ->where([
            'st_potongankaryawan_id' => $stpotongankaryawanId,
            'karyawan_active' => 1,
            'stpotongankaryawandet_active' => 1,
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
    public function listdivisijabatan($stpotongankaryawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $stpotongankaryawan = SettingPotonganKaryawanModel::select('stpotongankaryawan_id', 'stpotongankaryawan_used_for', 'stpotongankaryawan_division_jabatan')
        ->where([
            'stpotongankaryawan_id' => $stpotongankaryawanId,
            'stpotongankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        
        $data = [];
        if($stpotongankaryawan) {
            if($stpotongankaryawan->stpotongankaryawan_used_for == 4) {
                $data = KaryawanDivisiModel::select('karyawandivisi_id', 'karyawandivisi_name')
                ->where([
                    'karyawandivisi_active' => 1,
                ])->whereIn('karyawandivisi_id', explode(',', $stpotongankaryawan->stpotongankaryawan_division_jabatan))->get();
            } else if($stpotongankaryawan->stpotongankaryawan_used_for == 5) {
                $data = KaryawanJabatanModel::select('karyawanjabatan_id', 'karyawanjabatan_name')
                ->where([
                    'karyawanjabatan_active' => 1,
                ])->whereIn('karyawanjabatan_id', explode(',', $stpotongankaryawan->stpotongankaryawan_division_jabatan))->get();
            }
        }
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }
}