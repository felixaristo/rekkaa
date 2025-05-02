<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Libraries\AppUploadLibrary;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingGroupTunjanganKaryawanModel;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class PengaturanPenggajianController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $setting = SettingPenggajianKaryawanModel::select('st_penggajian_karyawan.*')
            // ->where('stpenggajiankaryawan_active', '1')
            ->where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
            ->orderBy('stpenggajiankaryawan_created_at', 'desc')
            ->first();

        $data = [
            'data'=> $setting,
            'title' => 'Pengaturan Penggajian',
            'content' => 'user.pengaturan.penggajian.index',
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
        $order_columns = ['stpenggajiankaryawan_id','stpenggajiankaryawan_name','stpenggajiankaryawan_method','stpenggajiankaryawan_period','stpenggajiankaryawan_active'];
        $order_col = $order_columns[0];
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $where = [
            // 'stpenggajiankaryawan_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];
        $list = SettingPenggajianKaryawanModel::select('st_penggajian_karyawan.*')
        ->when($search, function ($q, $search) {
            $lowercaseSearch = strtolower($search);
            return $q->where(function ($query) use ($lowercaseSearch) {
                $query->where(DB::raw('LOWER(stpenggajiankaryawan_id::text)'), 'like', '%' . $lowercaseSearch . '%')
                ->orWhere(DB::raw('LOWER(stpenggajiankaryawan_name)'), 'like', '%' . $lowercaseSearch . '%');
            });
        })
        ->where($where)
        ->orderBy($order_col, $order_type)
        ->take($limit)->skip($offset)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = SettingPenggajianKaryawanModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $stpenggajiankaryawan_name = 'Penggajian';//$request->input('stpenggajiankaryawan_name');
        $stpenggajiankaryawan_pic = $request->input('stpenggajiankaryawan_pic');
        $stpenggajiankaryawan_location = $request->input('stpenggajiankaryawan_location');
        $stpenggajiankaryawan_period = $request->input('stpenggajiankaryawan_period');
        $active = $request->input('stpenggajiankaryawan_active');
        $stpenggajiankaryawan_startdate = $request->input('stpenggajiankaryawan_startdate');
        $stpenggajiankaryawan_enddate = $request->input('stpenggajiankaryawan_enddate');
        $stpenggajiankaryawan_paymentdate = $request->input('stpenggajiankaryawan_paymentdate');
        $stpenggajiankaryawan_method = $request->input('stpenggajiankaryawan_method');
        $stpenggajiankaryawan_day = $request->input('stpenggajiankaryawan_day');
        $stpenggajiankaryawan_weekendoption = $request->input('stpenggajiankaryawan_weekendoption');
        $stpenggajiankaryawan_autoemailpayslip = $request->input('stpenggajiankaryawan_autoemailpayslip');
        $stpenggajiankaryawan_isnonemployee = $request->input('stpenggajiankaryawan_isnonemployee');
        
        $employees = $request->input('stpenggajiankaryawan_employees');
        // dd($groups);
        $rules = [
            'stpenggajiankaryawan_name' => 'required',
            'stpenggajiankaryawan_pic' => 'required',
            'stpenggajiankaryawan_location' => 'required',
            'stpenggajiankaryawan_method' => 'required|in:KALENDER,KERJA,TETAP',
            'stpenggajiankaryawan_period' => 'required|in:KALENDER,TANGGAL',
            'stpenggajiankaryawan_weekendoption' => 'required|in:MAJU,MUNDUR',
            'stpenggajiankaryawan_autoemailpayslip' => 'required|in:0,1',
            'stpenggajiankaryawan_paymentdate' => 'required|integer|min:1|max:31',
            // 'stpenggajiankaryawan_active' => 'required|in:0,1',
            // 'stpenggajiankaryawan_isnonemployee' => 'required|in:0,1',
        ];
        if($stpenggajiankaryawan_method == 'TETAP') {
            $rules['stpenggajiankaryawan_day'] = 'required|integer|min:1|max:31';
            // $stpenggajiankaryawan_startdate = null;
            // $stpenggajiankaryawan_enddate = null;
        }
        if($stpenggajiankaryawan_period == 'TANGGAL') {
            $rules['stpenggajiankaryawan_startdate'] = 'required|integer|min:1|max:31';
            $rules['stpenggajiankaryawan_enddate'] = 'required|integer|min:1|max:31';
            // $stpenggajiankaryawan_day = null;
        }
        if ($request->hasfile('stpenggajiankaryawan_logo')) {
            $rules['stpenggajiankaryawan_logo'] = 'mimes:jpeg,jpg,png|max:256';
        }

        $this->validate($request, $rules, [
            'stpenggajiankaryawan_name.required' => 'Deskripsi Penggajian wajib diisi!',
            'stpenggajiankaryawan_pic.required' => 'PIC Penggajian wajib diisi!',
            'stpenggajiankaryawan_location.required' => 'Lokasi Penggajian wajib diisi!',
            'stpenggajiankaryawan_method.required' => 'Metode Penggajian wajib diisi!',
            'stpenggajiankaryawan_period.required' => 'Periode Penggajian wajib diisi!',
            'stpenggajiankaryawan_weekendoption.required' => 'Opsi Hari Libur wajib diisi!',
            'stpenggajiankaryawan_autoemailpayslip.required' => 'Auto Email Payslip wajib diisi!',
            'stpenggajiankaryawan_paymentdate.required' => 'Tanggal Penggajian wajib diisi!',
            'stpenggajiankaryawan_paymentdate.integer' => 'Tanggal Penggajian berupa angka!',
            'stpenggajiankaryawan_day.required' => 'Angkat Tetap wajib diisi!',
            'stpenggajiankaryawan_day.integer' => 'Angkat Tetap berupa angka!',
            'stpenggajiankaryawan_startdate.required' => 'Tanggal Awal wajib diisi!',
            'stpenggajiankaryawan_startdate.integer' => 'Tanggal Awal berupa angka!',
            'stpenggajiankaryawan_enddate.required' => 'Tanggal Akhir wajib diisi!',
            'stpenggajiankaryawan_enddate.integer' => 'Tanggal Akhir berupa angka!',
            // 'stpenggajiankaryawan_active.required' => 'Status wajib diisi!',
            'stpenggajiankaryawan_logo.mimes' => 'Format gambar harus jpg atau png!',
            'stpenggajiankaryawan_logo.max' => 'Ukuran gambar maksimal 250 Kb!',
            // 'stpenggajiankaryawan_isnonemployee.required' => 'Terapkan ke non karyawan wajib diisi!',
        ]);

        // check if other has non employee
        // $stpenggajiankaryawanisnonemployeedata = SettingPenggajianKaryawanModel::where([
        //     'stpenggajiankaryawan_active' => 1,
        //     'stpenggajiankaryawan_isnonemployee' => TRUE,
        //     'ms_wajibpajak_id' => $wajibpajak_id,
        // ])->first();

        $logo_url = null;
        $logo_base64 = null;
        if ($request->hasfile('stpenggajiankaryawan_logo')) {
            $file = $request->file('stpenggajiankaryawan_logo');
            // dd($file->extension());
            $appupload_library = new AppUploadLibrary();
            $uploaded_photo = $appupload_library->uploadFile($file, 'images/pengaturan/penggajian');

            if($uploaded_photo['success'] == false) {
                return response()->json([
                    'success' => false,
                    'message' => $uploaded_photo['message']
                ]);
            }
            $logo_url = $uploaded_photo['data']['url'];
            $logo_base64 = $uploaded_photo['data']['base64'];
        }

        $save_data = [
            'stpenggajiankaryawan_name' => ucwords($stpenggajiankaryawan_name),
            'stpenggajiankaryawan_pic' => $stpenggajiankaryawan_pic,
            'stpenggajiankaryawan_location' => $stpenggajiankaryawan_location,
            'stpenggajiankaryawan_period' => $stpenggajiankaryawan_period,
            'stpenggajiankaryawan_method' => $stpenggajiankaryawan_method,
            'stpenggajiankaryawan_paymentdate' => $stpenggajiankaryawan_paymentdate,
            'stpenggajiankaryawan_is_all' => true,
            'ms_wajibpajak_id' => $wajibpajak_id,
            // 'stpenggajiankaryawan_active' => ($active) ? 1 : 0,
            'stpenggajiankaryawan_day' => $stpenggajiankaryawan_day,
            'stpenggajiankaryawan_startdate' => $stpenggajiankaryawan_startdate,
            'stpenggajiankaryawan_enddate' => $stpenggajiankaryawan_enddate,
            'stpenggajiankaryawan_weekendoption' => $stpenggajiankaryawan_weekendoption,
            'stpenggajiankaryawan_autoemailpayslip' => $stpenggajiankaryawan_autoemailpayslip,
            'stpenggajiankaryawan_logo' => $logo_url,
            'stpenggajiankaryawan_logo_base64' => $logo_base64,
            'stpenggajiankaryawan_isnonemployee' => $stpenggajiankaryawan_isnonemployee == '1',
        ];

        DB::beginTransaction();
        try {
            // if($stpenggajiankaryawanisnonemployeedata) {
            //     SettingPenggajianKaryawanModel::where([
            //         'stpenggajiankaryawan_active' => 1,
            //         'stpenggajiankaryawan_isnonemployee' => TRUE,
            //         'ms_wajibpajak_id' => $wajibpajak_id,
            //     ])->update(['stpenggajiankaryawan_isnonemployee' => FALSE]);
            // }

            $stpenggajiankaryawan = SettingPenggajianKaryawanModel::create($save_data);

            // if($request->input('stpenggajiankaryawan_is_all') == 0 && $employees) {
            //     $karyawans = KaryawanModel::select('karyawan_id', 'st_penggajian_id')
            //         ->where([
            //             'karyawan_active' => 1,
            //             'ms_wajibpajak_id' => $wajibpajak_id,
            //         ])
            //         ->whereIn('karyawan_id', $employees)->get();

            //     if(isset($karyawans)) {
            //         $karyawan_ids = [];
            //         foreach($karyawans as $kr) {
            //             // $explode_karyawanpenggajianids = ($kr->st_penggajian_id) ? explode(',', $kr->st_penggajian_id) : [];
            //             // array_push($explode_karyawanpenggajianids, $stpenggajiankaryawan->stpenggajiankaryawan_id); // insert to current employees tunjangan.
                        
            //             array_push($karyawan_ids, $kr->karyawan_id);
            //         }   
            //         // Update the employees allowance
            //         KaryawanModel::whereIn('karyawan_id', $karyawan_ids)
            //         ->update([
            //             'st_penggajian_id' => $stpenggajiankaryawan->stpenggajiankaryawan_id
            //         ]);

            //         // begin for log only
            //         $karyawanforlogs = KaryawanModel::whereIn('karyawan_id', $karyawan_ids)->get();
            //         $this->logs($karyawanforlogs, $stpenggajiankaryawan, 'ms_karyawan', 'updated');
            //         // end for log only
            //     }
            // } else if($request->input('stpenggajiankaryawan_is_all') == 1) {
            //     KaryawanModel::where('ms_wajibpajak_id', $wajibpajak_id)                                                    
            //         ->where('karyawan_status', '!=', 'NONKARYAWAN')
            //         ->update(['st_penggajian_id' => $stpenggajiankaryawan->stpenggajiankaryawan_id]);

            //     // begin for log only
            //     $karyawanforlogs = KaryawanModel::where('ms_wajibpajak_id', $wajibpajak_id)                                                    
            //     ->where('karyawan_status', '!=', 'NONKARYAWAN')->get();
            //     $this->logs($karyawanforlogs, $stpenggajiankaryawan, 'ms_karyawan', 'updated');
            //     // end for log only
            // }

            KaryawanModel::where('ms_wajibpajak_id', $wajibpajak_id)
                ->where('karyawan_status', '!=', 'NONKARYAWAN')
                ->update(['st_penggajian_id' => $stpenggajiankaryawan->stpenggajiankaryawan_id]);

            // begin for log only
            $karyawanforlogs = KaryawanModel::where('ms_wajibpajak_id', $wajibpajak_id)                                                    
            ->where('karyawan_status', '!=', 'NONKARYAWAN')->get();
            $this->logs($karyawanforlogs, $stpenggajiankaryawan, 'ms_karyawan', 'updated');
            // end for log only

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Penggajian karyawan berhasil disimpan',
                'data' => $stpenggajiankaryawan
            ]);
            

        } catch(Error $e) {
            // delete file if already upload
            if(isset($uploaded_photo) && $uploaded_photo['success']) {
                if($uploaded_photo['data']) {
                    $appupload_library->deleteFile('images/pengaturan/penggajian/'.$uploaded_photo['data']['filename']);
                }
            }
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($stpenggajiankaryawanId, Request $request)
    {
        // dd($request->all());
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $stpenggajiankaryawan_name = 'Penggajian';//$request->input('stpenggajiankaryawan_name');
        $stpenggajiankaryawan_pic = $request->input('stpenggajiankaryawan_pic');
        $stpenggajiankaryawan_location = $request->input('stpenggajiankaryawan_location');
        $stpenggajiankaryawan_period = $request->input('stpenggajiankaryawan_period');
        $active = $request->input('stpenggajiankaryawan_active');
        $stpenggajiankaryawan_startdate = $request->input('stpenggajiankaryawan_startdate');
        $stpenggajiankaryawan_enddate = $request->input('stpenggajiankaryawan_enddate');
        $stpenggajiankaryawan_paymentdate = $request->input('stpenggajiankaryawan_paymentdate');
        $stpenggajiankaryawan_method = $request->input('stpenggajiankaryawan_method');
        $stpenggajiankaryawan_day = $request->input('stpenggajiankaryawan_day');
        // $deletedemployee_ids = $request->input('deletedemployee_ids');
        // $employees = $request->input('stpenggajiankaryawan_employees');
        $stpenggajiankaryawan_weekendoption = $request->input('stpenggajiankaryawan_weekendoption');
        $stpenggajiankaryawan_autoemailpayslip = $request->input('stpenggajiankaryawan_autoemailpayslip');
        $stpenggajiankaryawan_isnonemployee = $request->input('stpenggajiankaryawan_isnonemployee');
        // dd($stpenggajiankaryawan_enddate);
        // dd($groups);
        $rules = [
            // 'stpenggajiankaryawan_name' => 'required',
            'stpenggajiankaryawan_method' => 'required|in:KALENDER,KERJA,TETAP',
            'stpenggajiankaryawan_period' => 'required|in:KALENDER,TANGGAL',
            'stpenggajiankaryawan_weekendoption' => 'required|in:MAJU,MUNDUR',
            'stpenggajiankaryawan_autoemailpayslip' => 'required|in:0,1',
            'stpenggajiankaryawan_paymentdate' => 'required|integer|min:1|max:31',
            // 'stpenggajiankaryawan_active' => 'required|in:0,1',
            // 'stpenggajiankaryawan_isnonemployee' => 'required|in:0,1',
        ];
        if($stpenggajiankaryawan_method == 'TETAP') {
            $rules['stpenggajiankaryawan_day'] = 'required|integer|min:1|max:31';

            // $stpenggajiankaryawan_startdate = null;
            // $stpenggajiankaryawan_enddate = null;
        }
        if($stpenggajiankaryawan_period == 'TANGGAL') {
            $rules['stpenggajiankaryawan_startdate'] = 'required|integer|min:1|max:31';
            $rules['stpenggajiankaryawan_enddate'] = 'required|integer|min:1|max:31';

            // $stpenggajiankaryawan_day = null;
        }
        if ($request->hasfile('stpenggajiankaryawan_logo')) {
            $rules['stpenggajiankaryawan_logo'] = 'mimes:jpeg,jpg,png|max:256';
        }

        $this->validate($request, $rules, [
            'stpenggajiankaryawan_pic.required' => 'PIC Penggajian wajib diisi!',
            'stpenggajiankaryawan_location.required' => 'Lokasi Penggajian wajib diisi!',
            'stpenggajiankaryawan_method.required' => 'Metode Penggajian wajib diisi!',
            'stpenggajiankaryawan_period.required' => 'Periode Penggajian wajib diisi!',
            'stpenggajiankaryawan_paymentdate.required' => 'Tanggal Penggajian wajib diisi!',
            'stpenggajiankaryawan_paymentdate.integer' => 'Tanggal Penggajian berupa angka!',
            'stpenggajiankaryawan_day.required' => 'Angkat Tetap wajib diisi!',
            'stpenggajiankaryawan_day.integer' => 'Angkat Tetap berupa angka!',
            'stpenggajiankaryawan_startdate.required' => 'Tanggal Awal wajib diisi!',
            'stpenggajiankaryawan_startdate.integer' => 'Tanggal Awal berupa angka!',
            'stpenggajiankaryawan_enddate.required' => 'Tanggal Akhir wajib diisi!',
            'stpenggajiankaryawan_enddate.integer' => 'Tanggal Akhir berupa angka!',
            // 'stpenggajiankaryawan_active.required' => 'Status wajib diisi!',
            'stpenggajiankaryawan_logo.mimes' => 'Format gambar harus jpg atau png!',
            'stpenggajiankaryawan_logo.max' => 'Ukuran gambar maksimal 250 Kb!',
            // 'stpenggajiankaryawan_isnonemployee.required' => 'Terapkan ke non karyawan wajib diisi!',
        ]);
        $stpenggajiankaryawan = SettingPenggajianKaryawanModel::where([
            'stpenggajiankaryawan_id' => $stpenggajiankaryawanId, 
            // 'stpenggajiankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$stpenggajiankaryawan) {
            return response()->json([
                'success' => false,
                'message' => 'Pengaturan Penggajian tidak ditemukan!'
            ]);
        }
        // check if other has non employee
        // $stpenggajiankaryawanisnonemployeedata = SettingPenggajianKaryawanModel::where([
        //     'stpenggajiankaryawan_active' => 1,
        //     'stpenggajiankaryawan_isnonemployee' => TRUE,
        //     'ms_wajibpajak_id' => $wajibpajak_id,
        // ])
        // ->whereNotIn('stpenggajiankaryawan_id', [$stpenggajiankaryawanId])->first();

        // $statusBefore = $stpenggajiankaryawan->stpenggajiankaryawan_is_all;
        // $statusCurrent = $request->input('stpenggajiankaryawan_is_all');

        // $checkAnother = SettingPenggajianKaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
        //     ->where('stpenggajiankaryawan_is_all', true)
        //     ->first();

        $old_logo = $stpenggajiankaryawan->stpenggajiankaryawan_logo;
        $logo_url = null;
        $logo_base64 = null;
        if ($request->hasfile('stpenggajiankaryawan_logo')) {
            $file = $request->file('stpenggajiankaryawan_logo');
            // dd($file->extension());
            $appupload_library = new AppUploadLibrary();
            $uploaded_photo = $appupload_library->uploadFile($file, 'images/pengaturan/penggajian');

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
            'stpenggajiankaryawan_name' => ucwords($stpenggajiankaryawan_name),
            'stpenggajiankaryawan_pic' => $stpenggajiankaryawan_pic,
            'stpenggajiankaryawan_location' => $stpenggajiankaryawan_location,
            'stpenggajiankaryawan_period' => $stpenggajiankaryawan_period,
            'stpenggajiankaryawan_method' => $stpenggajiankaryawan_method,
            'stpenggajiankaryawan_paymentdate' => $stpenggajiankaryawan_paymentdate,
            'ms_wajibpajak_id' => $wajibpajak_id,
            // 'stpenggajiankaryawan_active' => ($active) ? 1 : 0,
            'stpenggajiankaryawan_day' => $stpenggajiankaryawan_day,
            'stpenggajiankaryawan_startdate' => $stpenggajiankaryawan_startdate,
            'stpenggajiankaryawan_enddate' => $stpenggajiankaryawan_enddate,
            'stpenggajiankaryawan_weekendoption' => $stpenggajiankaryawan_weekendoption,
            'stpenggajiankaryawan_autoemailpayslip' => $stpenggajiankaryawan_autoemailpayslip,
            // 'stpenggajiankaryawan_logo' => $logo_url,
            // 'stpenggajiankaryawan_is_all' => $statusCurrent,
            // 'stpenggajiankaryawan_logo_base64' => $logo_base64,
            'stpenggajiankaryawan_isnonemployee' => $stpenggajiankaryawan_isnonemployee == '1',
        ];
        if($logo_url) {
            $save_data['stpenggajiankaryawan_logo'] = $logo_url;
            $save_data['stpenggajiankaryawan_logo_base64'] = $logo_base64;
        }
        // dd($save_data);

        // if($employees) {
        //     // check if the employees belong to entities
        //     $karyawans = KaryawanModel::select('karyawan_id', 'st_penggajian_id')
        //     ->where([
        //         'karyawan_active' => 1,
        //         'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        //     ])
        //     ->whereIn('karyawan_id', $employees)->get();

        // }
        // // dd($karyawans);

        // if($deletedemployee_ids) {
        //     // check if the deleted employees belong to entities
        //     $deletedkaryawans = KaryawanModel::select('karyawan_id', 'st_penggajian_id')
        //     ->where([
        //         'karyawan_active' => 1,
        //         'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        //     ])
        //     ->whereIn('karyawan_id', $deletedemployee_ids)->get();
        // }
        
        DB::beginTransaction();
        try {
            // if($stpenggajiankaryawanisnonemployeedata) {
            //     SettingPenggajianKaryawanModel::where([
            //         'stpenggajiankaryawan_active' => 1,
            //         'stpenggajiankaryawan_isnonemployee' => TRUE,
            //         'ms_wajibpajak_id' => $wajibpajak_id,
            //     ])->update(['stpenggajiankaryawan_isnonemployee' => FALSE]);
            // }
            
            $stpenggajiankaryawan->update($save_data);
            // update employee 
            KaryawanModel::where([
                'ms_wajibpajak_id' => $wajibpajak_id,
                'karyawan_active' => 1,
            ])
            ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
            ->update(['st_penggajian_id' => $stpenggajiankaryawan->stpenggajiankaryawan_id]);

            // if($statusBefore == 0 && $statusCurrent == 1) {
            //     KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])                        
            //         ->where('karyawan_status', '!=', 'NONKARYAWAN')
            //         ->update(['st_penggajian_id' => $stpenggajiankaryawan->stpenggajiankaryawan_id]);

            //     // begin for log only
            //     $karyawanforlogs = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])                        
            //     ->where('karyawan_status', '!=', 'NONKARYAWAN')->get();
            //     $this->logs($karyawanforlogs, $stpenggajiankaryawan, 'ms_karyawan', 'updated');
            //     // end for log only

            //     if($checkAnother) {
            //         $checkAnother->update([
            //             'stpenggajiankaryawan_is_all' => 0
            //         ]);
            //     }
            // } else if(!$statusCurrent == 1){
            //     KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
            //         ->where('st_penggajian_id', $stpenggajiankaryawan->stpenggajiankaryawan_id)                            
            //         ->where('karyawan_status', '!=', 'NONKARYAWAN')
            //         ->update(['st_penggajian_id' => null]);
            //     // begin for log only
            //     $karyawanforlogs = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
            //     ->where('st_penggajian_id', $stpenggajiankaryawan->stpenggajiankaryawan_id)                            
            //     ->where('karyawan_status', '!=', 'NONKARYAWAN')->get();
            //     $this->logs($karyawanforlogs, $stpenggajiankaryawan, 'ms_karyawan', 'updated');
            //     // end for log only
                
            //     if(isset($employees) && count($employees) > 0) {
            //         KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
            //         ->whereIn('karyawan_id', $employees)                               
            //         ->where('karyawan_status', '!=', 'NONKARYAWAN')  
            //         ->update(['st_penggajian_id' => $stpenggajiankaryawan->stpenggajiankaryawan_id]);
                    
            //         // begin for log only
            //         $karyawanforlogs = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
            //         ->whereIn('karyawan_id', $employees)                               
            //         ->where('karyawan_status', '!=', 'NONKARYAWAN')->get();
            //         // dd($karyawanforlogs);
            //         $this->logs($karyawanforlogs, $stpenggajiankaryawan, 'ms_karyawan', 'updated');
            //         // end for log only

            //         if($checkAnother) {
            //             $checkAnother->update([
            //                 'stpenggajiankaryawan_is_all' => 0
            //             ]);
            //         }
            //     }
                    
                
            // }

            // if(isset($deletedkaryawans)) { // deleted employee
            //     foreach($deletedkaryawans as $dkr) {
            //         // $explode_delkaryawanpenggajianids = ($dkr->st_penggajian_id) ? explode(',', $dkr->st_penggajian_id) : [];
                    
            //         /// check tunjangan exist in employee
            //         // $check_penggajiankey = array_search($stpenggajiankaryawan->stpenggajiankaryawan_id, $explode_delkaryawanpenggajianids);
                    
            //         // if($check_penggajiankey > -1) { // check if not exist tunjangan.
            //              // remove tunjangan
            //             // unset($explode_delkaryawanpenggajianids[$check_penggajiankey]);
            //             // Update the employees tunjangan
            //             KaryawanModel::where(['karyawan_id' => $dkr->karyawan_id])
            //             ->update([
            //                 'st_penggajian_id' => 0
            //             ]);
            //         // }
            //     }
            // }
            // if(isset($karyawans)) {
            //     foreach($karyawans as $kr) {
            //         // $explode_karyawanpenggajianids = ($kr->st_penggajian_id) ? explode(',', $kr->st_penggajian_id) : [];
            //         // // new karyawan
            //         // if(!in_array($stpenggajiankaryawan->stpenggajiankaryawan_id, $explode_karyawanpenggajianids)) { // check if not exist tunjangan.
            //         //     array_push($explode_karyawanpenggajianids, $stpenggajiankaryawan->stpenggajiankaryawan_id); // insert to current employees tunjangan.
                    
            //             // Update the employees allowance
            //             KaryawanModel::where(['karyawan_id' => $kr->karyawan_id])
            //             ->update([
            //                 'st_penggajian_id' => $stpenggajiankaryawan->stpenggajiankaryawan_id
            //             ]);
            //         // }
            //     }
            // }
            // delete old photo if exist
            if($logo_url && $old_logo) {
                $appupload_library->deleteFile($old_logo, true);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Penggajian karyawan berhasil disimpan.',
                'data' => $stpenggajiankaryawan
            ]);
            

        } catch(Error $e) {
            // delete file if already upload
            if(isset($uploaded_photo) && $uploaded_photo['success']) {
                if($uploaded_photo['data']) {
                    $appupload_library->deleteFile('images/pengaturan/penggajian/'.$uploaded_photo['data']['filename']);
                }
            }
            
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

        $data = SettingPenggajianKaryawanModel::select('stpenggajiankaryawan_id', 'stpenggajiankaryawan_name')
        ->when($q, function ($query, $q) {
            return $query->where('stpenggajiankaryawan_name', 'ilike', '%'.$q.'%');
        })
        ->where([
            'stpenggajiankaryawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])
        ->limit($limit)->get();
        
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
    public function listkaryawan($stpenggajiankaryawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $data = KaryawanModel::select('karyawan_id', 'karyawan_name')
        ->where([
            'st_penggajian_id' => $stpenggajiankaryawanId,
            'karyawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    // logs the data
    private function logs($dataforlogs, $updateddata, $table = '', $eventname = 'created')
    {
        $datalogs = [];
        // dd($table);
        if($table == 'ms_karyawan') {
            $karyawanModel = new KaryawanModel();
            
            foreach($dataforlogs as $kr) {
                $activitylog_properties["old"] = $kr;
                // begin field that changes
                $kr->st_penggajian_id = $updateddata->stpenggajiankaryawan_id;
                // end field that changes

                $activitylog_properties["attributes"] = $kr;
                array_push($datalogs, [
                    "log_name" => $karyawanModel->getLogName(),
                    "description" => $karyawanModel->getDescriptionForEvent($eventname),
                    "subject_type" => $karyawanModel->getCauserTypeModel(),
                    "subject_id" => $kr->karyawan_id,
                    "causer_id" => $karyawanModel->getCauserId(),
                    "causer_type" => $karyawanModel->getCauserTypeModel(),
                    "properties" => json_encode($activitylog_properties),
                    "created_at" => date('Y-m-d H:i:s'),
                    "updated_at" => date('Y-m-d H:i:s')
                ]);
            }
        }
        // dd($karyawanforlogs);
        if($datalogs)
            Activity::insert($datalogs);
    }
}