<?php
namespace App\Http\Controllers\User\Akuntansi;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Pengaturan\PengaturanTunjanganController;
use App\Jobs\SendMailJob;
use App\Libraries\AppPPh21Library;
use App\Libraries\AppUploadLibrary;
use App\Libraries\TestLibrary;
use App\Model\Master\BankModel;
use App\Model\Master\KaryawanDivisiModel;
use App\Model\Master\KaryawanInfoFieldModel;
use App\Model\Master\KaryawanJabatanModel;
use App\Model\Master\KaryawanKalkulasiModel;
use App\Model\Master\KaryawanMasakerjaModel;
use App\Model\Master\KaryawanModel;
use App\Model\Master\PtkpModel;
use App\Model\Master\UserGroupMemberModel;
use App\Model\Master\UserGroupModel;
use App\Model\Master\WajibPajakModel;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\Model\Setting\SettingAnnouncementModel;
use App\Model\Setting\SettingAttendanceModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingLeaveModel;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use App\Model\Setting\SettingPotonganKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use App\Model\Setting\SettingPotonganKaryawanDetailModel;
use App\Model\Setting\SettingTunjanganKaryawanDetailModel;
use App\Model\Setting\SettingLeaveDetailModel;
use App\Model\Transaction\Akuntansi\AkunBiayaModel;
use App\Model\Transaction\Akuntansi\BiayaModel;
use App\Model\Transaction\HistoryImportModel;
use App\Model\Transaction\NotificationModel;
use App\Model\Transaction\PayrollModel;
use App\Model\Transaction\PPh21Model;
use App\User;
use Carbon\Carbon;
use DateTime;
use DOMDocument;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use PhpOffice\PhpSpreadsheet\Worksheet\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\DataType as PhpSpreadsheetDataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as PhpSpreadsheetDate;


class JurnalUmumController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $data = [
            'title' => 'Data Jurnal Umum',
            'content' => 'user.akuntansi.jurnal-umum.index',
        ];
        // dd($calculate_pph21 / 12);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    // public function create(Request $request)
    // {
    //     $user_id = session()->get('user_data')['user_id'];
    //     $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
    //     $data = [
    //         'title' => 'Biaya Baru',
    //         'content' => 'user.akuntansi.biaya.create',
    //     ];

    //     // dd($data);

    //     if($request->ajax()) {
    //         return view($data['content'], $data)->render();
    //     } else {
    //         return view('user.index', $data);
    //     }
    // }

    // public function edit($karyawanId, Request $request)
    // {
    //     $user_id = session()->get('user_data')['user_id'];
    //     $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

    //     $karyawan = KaryawanModel::
    //     select("*"
    //     , DB::raw("(SELECT json_agg(ttable) 
    //     FROM (
    //         SELECT std.st_tunjangankaryawan_id, stk.sttunjangankaryawan_name, stk.sttunjangankaryawan_used_for
    //         FROM st_tunjangan_karyawan_detail as std
    //         JOIN st_tunjangan_karyawan as stk ON std.st_tunjangankaryawan_id = stk.sttunjangankaryawan_id
    //         WHERE std.ms_karyawan_id = ms_karyawan.karyawan_id
    //             AND std.sttunjangankaryawandet_active = '1'
    //             AND stk.sttunjangankaryawan_active = '1'
    //     ) as ttable) as tunjangan_json")
    //     , DB::raw("(SELECT json_agg(ttable) 
    //     FROM (
    //         SELECT spd.st_potongankaryawan_id, spk.stpotongankaryawan_name, spk.stpotongankaryawan_used_for
    //         FROM st_potongan_karyawan_detail as spd
    //         JOIN st_potongan_karyawan as spk ON spd.st_potongankaryawan_id = spk.stpotongankaryawan_id
    //         WHERE spd.ms_karyawan_id = ms_karyawan.karyawan_id
    //             AND spd.stpotongankaryawandet_active = '1'
    //             AND spk.stpotongankaryawan_active = '1'
    //     ) as ttable) as potongan_json")
    //     , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_lock='1' LIMIT 1) payroll_islock")
    //     , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_lock='1' AND TO_CHAR(payroll_period, 'Y') = TO_CHAR(NOW(), 'Y') LIMIT 1) payroll_currentyear_exist")
    //     )
    //     ->with(['bank', 'penggajian', 'ptkp', 'manager', 'divisi', 'jabatan', 'attendance'])
    //     ->where([
    //         'karyawan_id' => $karyawanId, 
    //         'karyawan_active' => 1,
    //         // 'karyawan_contract_end' => null,
    //         'ms_wajibpajak_id' => $wajibpajak_id,
    //         // 'ms_user_id' => $user_id,
    //     ])->first();

    //     // dd($karyawan);

    //     if(!$karyawan) {
    //         abort(404);
    //     }
    //     // dd($karyawan->tunjangan_json);
        
    //     $setting = DB::select(DB::raw("SELECT (SELECT json_agg(ptable) 
    //     FROM (
    //         SELECT st_tunjangan_karyawan.*
    //         FROM st_tunjangan_karyawan
    //         WHERE ms_wajibpajak_id = {$wajibpajak_id}
    //         AND sttunjangankaryawan_active = '1'
    //         ORDER BY sttunjangankaryawan_id ASC
    //     )
    //     as ptable) as setting_tunjangankaryawan_json
    //     , 
    //     (SELECT json_agg(ptable) 
    //     FROM (
    //         SELECT st_potongan_karyawan.*
    //         FROM st_potongan_karyawan
    //         WHERE ms_wajibpajak_id = {$wajibpajak_id}
    //         AND stpotongankaryawan_active = '1'
    //         ORDER BY stpotongankaryawan_id ASC
    //     )
    //     as ptable) as setting_potongankaryawan_json
    //     ,
    //     (SELECT row_to_json(ptable) 
    //     FROM (
    //         SELECT st_bpjs_karyawan.*
    //         FROM st_bpjs_karyawan
    //         WHERE ms_wajibpajak_id = {$wajibpajak_id}
    //         AND stbpjskaryawan_active = '1'
    //         ORDER BY stbpjskaryawan_id ASC
    //     )
    //     as ptable) as setting_bpjspegawai_json
    //     "));

    //     $karyawan_infofield = KaryawanInfoFieldModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_user_id' => $user_id])->first();
    //     $stbpjskaryawan_data = ($setting[0]->setting_bpjspegawai_json) ? json_decode($setting[0]->setting_bpjspegawai_json) : null;
    //     $data = [
    //         'title' => 'Edit Karyawan',
    //         'content' => 'user.master.karyawan.edit',
    //         'karyawan' => $karyawan,
    //         'sttunjangankaryawan_data' => ($setting[0]->setting_tunjangankaryawan_json) ? json_decode($setting[0]->setting_tunjangankaryawan_json) : null,
    //         'stpotongankaryawan_data' => ($setting[0]->setting_potongankaryawan_json) ? json_decode($setting[0]->setting_potongankaryawan_json) : null,
    //         'stbpjskaryawan_data' => ($stbpjskaryawan_data) ? json_decode($stbpjskaryawan_data->stbpjskaryawan_value) : null,
    //         'karyawan_infofield' => $karyawan_infofield,
    //     ];

    //     // check if any payroll is lock
    //     // $payroll = PayrollModel::where(['ms_karyawan_id' => $karyawanId, 'payroll_lock' => 1])->first();
    //     // $data['payroll'] = $payroll;
    //     // dd($karyawan->manager);
    //     if($request->ajax()) {
    //         return view($data['content'], $data)->render();
    //     } else {
    //         return view('user.index', $data);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
    //     $is_user = $request->input('karyawan_isuser');
    //     $is_manager = 0;
    //     $manager_id = intval($request->input('karyawan_manager'));
    //     $email = $request->input('karyawan_email');
    //     $karyawan_tambahan = $request->input('karyawan_tambahan');
    //     // dd($request->input('karyawan_tambahan'));
    //     // echo json_encode($request->input('bpjs_tk'));
    //     // return json_encode($request->input('bpjs_tk'));
        
    //     $rules = [
    //         'karyawan_enid' => ['required', 
    //             Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id) {
    //                 return $query->where([
    //                     'ms_wajibpajak_id' => $wajibpajak_id
    //                 ]);
    //             })
    //         ],
    //         'karyawan_nik' => ['required', 
    //             Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id) {
    //                 return $query->where([
    //                     'ms_wajibpajak_id' => $wajibpajak_id
    //                 ]);
    //             })
    //         ],
    //         'karyawan_name' => 'required',
    //         'karyawan_email' => $email ? 'email' : '',
    //         'karyawan_citizenship' => 'required|in:WNI,WNA',
    //         'ptkp_id' => 'required',
    //         'karyawan_gender' => 'required',
    //         'karyawan_status' => 'required|in:TETAP,KONTRAK,PERCOBAAN',
    //         'karyawan_contract_begin' => 'required|date_format:d-m-Y',
    //         'karyawan_calculation_method' =>  'required|in:GROSS,GROSS_UP,NETT',
    //         'karyawan_salary' => 'required',
    //         // 'karyawan_payroll' => 'required',
    //         'karyawan_npwp' => ['required', 
    //             Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id) {
    //                 return $query->where([
    //                     'ms_wajibpajak_id' => $wajibpajak_id
    //                 ])
    //                 ->where('karyawan_npwp', '<>', '00.000.000.0-000.000');;
    //             })
    //         ],
    //         // 'karyawan_bankid' => 'required',
    //         // 'karyawan_banknokartu' => 'required',
    //     ];

    //     if($email) {
    //         $rules['karyawan_email'] = ['required','email',
    //             Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id) {
    //                 return $query->where([
    //                     'ms_wajibpajak_id' => $wajibpajak_id
    //                 ]);
    //             }),
    //         ];
    //     }

    //     // if($request->input('karyawan_npwp')) {
    //     //     $rules['karyawan_npwp'] = [
    //     //         Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id) {
    //     //             return $query->where([
    //     //                 'ms_wajibpajak_id' => $wajibpajak_id
    //     //             ]);
    //     //         })
    //     //     ];
    //     // }
    //     if ($request->hasfile('karyawan_photo')) {
    //         $rules['karyawan_photo'] = 'mimes:jpeg,jpg,png|max:256';
    //     }
    //     if($request->input('karyawan_status') != 'TETAP') {
    //         $rules['karyawan_contract_end'] = 'required|date_format:d-m-Y';
    //     }
    //     if($request->input('karyawan_isgetbpjskes') == 1) {
    //         $rules['karyawan_bpjskesdate'] = 'required|date_format:d-m-Y';
    //         $rules['karyawan_bpjskesno'] = 'required|unique:ms_karyawan,karyawan_bpjskesno';
    //     }
    //     if($request->input('karyawan_isgetbpjstk') == 1) {
    //         $rules['karyawan_bpjstkdate'] = 'required|date_format:d-m-Y';
    //         $rules['karyawan_bpjstkno'] = 'required|unique:ms_karyawan,karyawan_bpjstkno';
    //     }
        
    //     $this->validate($request, $rules, [
    //         'karyawan_enid.required' => 'ID Karyawan wajib diisi!',
    //         'karyawan_enid.unique' => 'ID Karyawan sudah terdaftar!',
    //         'karyawan_nik.required' => 'NIK wajib diisi!',
    //         'karyawan_nik.unique' => 'NIK sudah terdaftar! Silahkan gunakan NIK lainnya.',
    //         'karyawan_npwp.required' => 'NPWP wajib diisi!',
    //         'karyawan_npwp.unique' => 'NPWP sudah terdaftar! Silahkan gunakan NPWP lainnya.',
    //         'karyawan_name.required' => 'Nama wajib diisi!',
    //         'karyawan_email.required' => 'Email wajib diisi!',
    //         'karyawan_email.email' => 'Format email tidak sesuai!',
    //         'karyawan_email.unique' => 'Email sudah digunakan. Silahkan gunakan email lainnya!',
    //         'karyawan_citizenship.required' => 'Kewarganegaraan wajib diisi!',
    //         'ptkp_id.required' => 'Status perkawinan wajib diisi!',
    //         'karyawan_gender.required' => 'Jenis Kelamin wajib diisi!',
    //         'karyawan_status.required' => 'Status karyawan wajib diisi!',
    //         'karyawan_contract_begin.required' => 'Tgl. Kontrak wajib diisi!',
    //         'karyawan_calculation_method.required' => 'Metode perhitungan wajib diisi!',
    //         'karyawan_salary.required' => 'Gaji pokok wajib diisi!',
    //         // 'karyawan_payroll.required' => 'Penggajian wajib diisi!',
    //         // 'karyawan_bankid.required' => 'Nama Bank wajib diisi!',
    //         // 'karyawan_banknokartu.required' => 'Nomor Kartu Bank wajib diisi!',
    //         'karyawan_bpjskesno.unique' => 'No. Kartu BPJS Kesehatan sudah digunakan!',
    //         'karyawan_bpjstkno.unique' => 'No. Kartu BPJS TK sudah digunakan!',
    //         'karyawan_photo.mimes' => 'Format gambar harus jpg atau png!',
    //         'karyawan_photo.max' => 'Ukuran gambar maksimal 250 Kb!',
    //     ]);

    //     $photo_url = null;
    //     if ($request->hasfile('karyawan_photo')) {
    //         $file = $request->file('karyawan_photo');
    //         // dd($file->extension());
    //         $appupload_library = new AppUploadLibrary();
    //         $uploaded_photo = $appupload_library->uploadFile($file, 'images/karyawan/profil');

    //         if($uploaded_photo['success'] == false) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => $uploaded_photo['message']
    //             ]);
    //         }
    //         $photo_url = $uploaded_photo['data']['url'];
    //     }

    //     $dt = null;
    //     if($request->input('karyawan_birthdate')) {
    //         $dt = \Carbon\Carbon::parse($request->input('karyawan_birthdate'));
    //     }
        
    //     $dt_cbegin = \Carbon\Carbon::parse($request->input('karyawan_contract_begin'));
        
    //     $dt_bpjskes = null;
    //     $dt_bpjstk = null;
    //     $dt_pend = null;
    //     $karyawan_bpjskeslainnya = 0;
    //     $karyawan_bpjstklainnya = 0;
    //     $tunjanganbpjs = SettingBpjsKaryawanModel::where([
    //         'ms_wajibpajak_id' => $wajibpajak_id,
    //         'stbpjskaryawan_active' => 1
    //     ])->first();
    //     $stbpjskaryawan_data = ($tunjanganbpjs) ? json_decode($tunjanganbpjs->stbpjskaryawan_value) : [];
    //     $bpjskes = false;
    //     $bpjstk = false;
    //     $stbpjskes_lainnya = false;
    //     $stbpjstk_lainnya = false;
    //     foreach ($stbpjskaryawan_data as $stbpjs) {
    //         if ($stbpjs->type == 'KESEHATAN') {
    //             $bpjskes = true;
    //             if($stbpjs->name == 'LAINNYA') {
    //                 $stbpjskes_lainnya = true;
    //             }
    //         }
    //         if ($stbpjs->type == 'TENAGA_KERJA') {
    //             $bpjstk = true;
    //             if($stbpjs->name == 'LAINNYA') {
    //                 $stbpjstk_lainnya = true;
    //             }
    //         }
    //     }
    //     if($bpjskes && $request->input('karyawan_isgetbpjskes') == 1) {
    //         $dt_bpjskes = \Carbon\Carbon::parse($request->input('karyawan_bpjskesdate'));
    //         if($stbpjskes_lainnya)
    //             $karyawan_bpjskeslainnya = $request->input('karyawan_bpjskeslainnya');
    //     }
    //     if($bpjstk && $request->input('karyawan_isgetbpjstk') == 1) {
    //         $dt_bpjstk = \Carbon\Carbon::parse($request->input('karyawan_bpjstkdate'));
    //         if($stbpjstk_lainnya)
    //             $karyawan_bpjstklainnya = $request->input('karyawan_bpjstklainnya');
    //     }
    //     if($request->input('karyawan_status') != 'TETAP') {
    //         $dt_pend = \Carbon\Carbon::parse($request->input('karyawan_contract_end'));
    //     }

    //     $karyawan_calculation_method = $request->input('karyawan_calculation_method');
    //     $escape_email = DB::connection()->getPdo()->quote($email);

    //     $wajibpajak = WajibPajakModel::select("*"
    //     , DB::raw("(SELECT row_to_json(ptable) 
    //     FROM (
    //         SELECT ms_karyawan_infofield.*
    //         FROM ms_karyawan_infofield
    //         WHERE ms_wajibpajak_id = {$wajibpajak_id}
    //         ORDER BY karyawaninfofield_id ASC
    //         LIMIT 1
    //     )
    //     as ptable) as karyawaninfofield_json")
    //     , DB::raw("(SELECT row_to_json(mtable) 
    //     FROM (
    //         SELECT manager.karyawan_id, manager.karyawan_ismanager
    //         FROM ms_karyawan as manager
    //         WHERE manager.ms_wajibpajak_id = {$wajibpajak_id}
    //         AND manager.karyawan_id = {$manager_id}
    //         LIMIT 1
    //     )
    //     as mtable) as manager_json")
    //     )->where(['wajibpajak_id' => $wajibpajak_id])->first();
        
    //     $total_entity = intval($wajibpajak->total_karyawan);
    //     // dd($wajibpajak->total_karyawan);

    //     $karyawan_infofield = ($wajibpajak->karyawaninfofield_json) ? json_decode($wajibpajak->karyawaninfofield_json) : null;
    //     // dd($karyawan_infofield);
    //     $tempdata_tambahan = [];
    //     $karyawan_infokey = [];
    //     if($karyawan_tambahan) {
    //         foreach($karyawan_tambahan as $ktb) {
    //             array_push($karyawan_infokey, $ktb['nama']);
    //             if(isset($ktb['nama']) && isset($ktb['keterangan'])) {
    //                 array_push($tempdata_tambahan, $ktb);
    //             }
    //         }
    //     }

    //     DB::beginTransaction();
    //     try {
    //         if(!$karyawan_infofield) { // if there is no info field 
    //             KaryawanInfoFieldModel::create([
    //                 'ms_user_id' => $wajibpajak->ms_user_id,
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //                 'karyawaninfofield_fields' => ($karyawan_infokey) ? implode(',', $karyawan_infokey) : null
    //             ]);
    //         } else {
    //             KaryawanInfoFieldModel::where([
    //                 'ms_user_id' => $wajibpajak->ms_user_id,
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //             ])->update([
    //                 'karyawaninfofield_fields' => ($karyawan_infokey) ? implode(',', $karyawan_infokey) : null
    //             ]);
    //         }
    //         $division_id = $request->input('karyawan_division');
    //         if($division_id) {
    //             if(!is_numeric($division_id)) {
    //                 $division = KaryawanDivisiModel::create([
    //                     'karyawandivisi_name' => $request->input('karyawan_division'),
    //                     'ms_user_id' => $wajibpajak->ms_user_id,
    //                     'ms_wajibpajak_id' => $wajibpajak_id,
    //                 ]);
    //                 $division_id = $division->karyawandivisi_id;
    //             }
    //         }

    //         $position_id = $request->input('karyawan_position');
    //         if($position_id) {
    //             if(!is_numeric($position_id)) {
    //                 $position = KaryawanJabatanModel::create([
    //                     'karyawanjabatan_name' => $request->input('karyawan_position'),
    //                     'ms_user_id' => $wajibpajak->ms_user_id,
    //                     'ms_wajibpajak_id' => $wajibpajak_id,
    //                 ]);
    //                 $position_id = $position->karyawanjabatan_id;
    //             }
    //         }
            
    //         $random_password = null;
    //         $generate_forgot_password = null;
    //         if($is_user == '1') {
    //             $random_password = Hash::make(Str::random(6));
    //             $generate_forgot_password = Hash::make('FGP'.$email.uniqid());
    //         }

    //         $checkPayroll = SettingPenggajianKaryawanModel::where([
    //             'ms_wajibpajak_id' => $wajibpajak_id,
    //             'stpenggajiankaryawan_is_all' => true
    //         ])->first();

    //         $checkAttendance = SettingAttendanceModel::where([
    //             'ms_wajibpajak_id' => $wajibpajak_id,
    //             'attendance_is_all' => true
    //         ])->first();

    //         $karyawan = KaryawanModel::create([
    //             'karyawan_enid' => $request->input('karyawan_enid'),
    //             'karyawan_nik' => $request->input('karyawan_nik'),
    //             'karyawan_npwp' => $request->input('karyawan_npwp'),
    //             'karyawan_name' => $request->input('karyawan_name'),
    //             'karyawan_birthdate' => ($dt) ? $dt->translatedFormat('Y-m-d') : null,
    //             'karyawan_birthplace' => $request->input('karyawan_birthplace'),
    //             'karyawan_contract_begin' => ($dt_cbegin) ? $dt_cbegin->translatedFormat('Y-m-d') : null,
    //             'karyawan_contract_end' => ($dt_pend) ? $dt_pend->translatedFormat('Y-m-d') : null,
    //             'karyawan_phone' => $request->input('karyawan_phone'),
    //             'karyawan_email' => $request->input('karyawan_email'),
    //             'karyawan_password' => $random_password,
    //             'karyawan_forgot_password' => $generate_forgot_password,
    //             'karyawan_citizenship' => $request->input('karyawan_citizenship'),
    //             'karyawan_gender' => $request->input('karyawan_gender'),
    //             'karyawan_address' => $request->input('karyawan_address'),
    //             'karyawan_status' => $request->input('karyawan_status'),
    //             'karyawan_calculation_method' => $karyawan_calculation_method,
    //             'karyawan_salary' => ($request->input('karyawan_salary')) ? intval($request->input('karyawan_salary')) : 0,
    //             'ms_ptkp_id' => $request->input('ptkp_id'),
    //             'ms_country_id' => ($request->input('karyawan_citizenship') == 'WNI') ? 100 : $request->input('country_id'),
    //             'ms_bank_id' => $request->input('karyawan_bankid') ? $request->input('karyawan_bankid') : null,
    //             'ms_wajibpajak_id' => $wajibpajak_id,
    //             'ms_user_id' => $wajibpajak->ms_user_id,
    //             'ms_karyawandivisi_id' => $division_id,
    //             'ms_karyawanjabatan_id' => $position_id,
    //             'st_attendance_id' => $checkAttendance ? $checkAttendance->attendance_id : null,
    //             'st_tunjangan_id' => ($request->input('karyawan_allowance')) ? implode(',', $request->input('karyawan_allowance')) : null,
    //             'st_potongan_id' => ($request->input('karyawan_deduction')) ? implode(',', $request->input('karyawan_deduction')) : null,
    //             'st_penggajian_id' => $checkPayroll ? $checkPayroll->stpenggajiankaryawan_id : null,
    //             // 'karyawan_ismanager' => $is_manager,
    //             'karyawan_isuser' => ($is_user == '1') ? 1 : 0,
    //             'karyawan_manager_id' => ($manager_id) ? $manager_id : null,
    //             'karyawan_bankno' => $request->input('karyawan_banknokartu') ? $request->input('karyawan_banknokartu') : null,
    //             'karyawan_isbpjskes' => $request->input('karyawan_isgetbpjskes'),
    //             'karyawan_bpjskesdate' => ($dt_bpjskes) ? $dt_bpjskes->translatedFormat('Y-m-d') : null,
    //             'karyawan_bpjskesno' => $request->input('karyawan_isgetbpjskes') == 1 ? $request->input('karyawan_bpjskesno') : null,
    //             'karyawan_isbpjstk' => $request->input('karyawan_isgetbpjstk'),
    //             'karyawan_bpjstkdate' => ($dt_bpjstk) ? $dt_bpjstk->translatedFormat('Y-m-d') : null,
    //             'karyawan_bpjstkno' => $request->input('karyawan_isgetbpjstk') == 1 ? $request->input('karyawan_bpjstkno') : null,
    //             'karyawan_bpjskeslainnya' => $karyawan_bpjskeslainnya,
    //             'karyawan_bpjstklainnya' => $karyawan_bpjstklainnya,
    //             'karyawan_photo' => $photo_url,
    //             'karyawan_code_objekpajak' => '21-100-01',
    //             'karyawan_additionalinfo' => ($tempdata_tambahan) ? json_encode($tempdata_tambahan) : null,
    //         ]);

    //         // remove column
    //         unset($karyawan->karyawan_password);
    //         unset($karyawan->karyawan_forgot_password);

    //         // insert masa kerja
    //         KaryawanMasakerjaModel::create([
    //             'ms_karyawan_id' => $karyawan->karyawan_id,
    //             'ms_ptkp_id' => $request->input('ptkp_id'),
    //             'ms_user_id' => $wajibpajak->ms_user_id,
    //             'ms_wajibpajak_id' => $wajibpajak_id,
    //             'karyawanmasakerja_status' => $request->input('karyawan_status'),
    //             'karyawanmasakerja_calculation_method' => $karyawan_calculation_method,
    //             'karyawanmasakerja_salary' => ($request->input('karyawan_salary')) ? intval($request->input('karyawan_salary')) : 0,
    //             'karyawanmasakerja_position' => $request->input('karyawan_position'),
    //             'karyawanmasakerja_nik' => $request->input('karyawan_nik'),
    //             'karyawanmasakerja_npwp' => $request->input('karyawan_npwp'),
    //             'ms_objekpajak_code' => '21-100-01',
    //             'karyawanmasakerja_contract_begin' => $dt_cbegin->translatedFormat('Y-m-d'),
    //             'karyawanmasakerja_data' => json_encode($karyawan),
    //         ]);

    //         // check if manager already set or not
    //         if($wajibpajak->manager_json) {
    //             $manager = json_decode($wajibpajak->manager_json);
    //             if($manager->karyawan_ismanager != 1) {
    //                 KaryawanModel::where(['karyawan_id' => $manager->karyawan_id, 'ms_wajibpajak_id' => $wajibpajak_id])
    //                 ->update(['karyawan_ismanager' => 1]);
    //             }
    //         }

    //         // if($request->input('karyawan_attendance')) {
    //         //     $checkAttendance = SettingAttendanceModel::where([
    //         //         'ms_wajibpajak_id' => $wajibpajak_id,
    //         //         'attendance_is_all' => true
    //         //     ])
    //         //     ->where('attendance_id', '<>', $request->input('karyawan_attendance'))->first();

    //         //     if($checkAttendance) {
    //         //         $checkAttendance->update([
    //         //             'attendance_is_all' => false
    //         //         ]);
    //         //     }
    //         // }

    //         // if($request->input('karyawan_payroll')) {
    //         //     $checkPayroll = SettingPenggajianKaryawanModel::where([
    //         //         'ms_wajibpajak_id' => $wajibpajak_id,
    //         //         'stpenggajiankaryawan_is_all' => true
    //         //     ])
    //         //     ->where('stpenggajiankaryawan_id', '<>', $request->input('karyawan_payroll'))->first();

    //         //     if($checkPayroll) {
    //         //         $checkPayroll->update([
    //         //             'stpenggajiankaryawan_is_all' => false
    //         //         ]);
    //         //     }
    //         // }

    //         if($request->input('karyawan_allowance')) {
    //             $checkAllowance = SettingTunjanganKaryawanModel::where([
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //                 'sttunjangankaryawan_is_all' => true
    //             ])->pluck('sttunjangankaryawan_id')->toArray();

    //             $diffAllowance = array_diff($checkAllowance, $request->input('karyawan_allowance'));
                
    //             SettingTunjanganKaryawanModel::whereIn('sttunjangankaryawan_id', $diffAllowance)
    //             ->where([
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //             ])->update([
    //                'sttunjangankaryawan_is_all' => false
    //             ]);

    //             $bulkAllowance = [];
    //             foreach ($request->input('karyawan_allowance') as $st_tunjangankaryawan_id) {
    //                 $bulkAllowance[] = [
    //                     'st_tunjangankaryawan_id' => $st_tunjangankaryawan_id,
    //                     'ms_karyawan_id' => $karyawan->karyawan_id,
    //                     'sttunjangankaryawandet_active' => '1'
    //                 ];
    //             }

    //             SettingTunjanganKaryawanDetailModel::insert($bulkAllowance);
    //         }

    //         if($request->input('karyawan_deduction')) {
    //             $checkDeduction = SettingPotonganKaryawanModel::where([
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //                 'stpotongankaryawan_is_all' => true
    //             ])->pluck('stpotongankaryawan_id')->toArray();

    //             $diffDeduction = array_diff($checkDeduction, $request->input('karyawan_deduction'));
                
    //             SettingPotonganKaryawanModel::whereIn('stpotongankaryawan_id', $diffDeduction)
    //             ->where([
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //             ])->update([
    //                'stpotongankaryawan_is_all' => false
    //             ]);

    //             $bulkDeduction = [];
    //             foreach ($request->input('karyawan_deduction') as $st_potongankaryawan_id) {
    //                 $bulkDeduction[] = [
    //                     'st_potongankaryawan_id' => $st_potongankaryawan_id,
    //                     'ms_karyawan_id' => $karyawan->karyawan_id,
    //                     'stpotongankaryawandet_active' => '1'
    //                 ];
    //             }

    //             SettingPotonganKaryawanDetailModel::insert($bulkDeduction);
    //         }
            
    //         // processed the leave setting
    //         $stleave = SettingLeaveModel::where([
    //             'leave_status_active' => true,
    //             'ms_wajibpajak_id' => $wajibpajak_id,
    //         ])
    //         ->whereIn('leave_used_for', [1,2,3,4,5])->get();

    //         if($stleave) {
    //             $tempdata_leave = $this->stleave_tempdata($stleave, [
    //                 [
    //                     'karyawan_id' => $karyawan->karyawan_id,
    //                     'karyawan_gender' => $request->input('karyawan_gender'),
    //                     'ms_karyawandivisi_id' => $division_id,
    //                     'ms_karyawanjabatan_id' => $position_id,
    //                 ]
    //             ]);

    //             if($tempdata_leave) {
    //                 SettingLeaveDetailModel::insert($tempdata_leave);
    //             }
    //         }

    //         if($is_user) {
    //             // insert tr_notification
    //             $notification = NotificationModel::create([
    //                 'notification_title' => 'Rekkaa - Undangan Dari '.$wajibpajak->wajibpajak_name,
    //                 'notification_type' => 'EMAIL',
    //                 'notification_from' => env("MAIL_FROM_ADDRESS"),
    //                 'notification_to' => $email,
    //                 'ms_user_id' => $wajibpajak->ms_user_id,
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //                 'notification_view' => 'email.email-undangan-akun-karyawan',
    //                 'notification_data' => json_encode([
    //                     'entity' => [
    //                         'name' => $wajibpajak->wajibpajak_name,
    //                         'user_name' => $request->input('karyawan_name'),
    //                         'inviter_email' => session()->get('user_data')['user_email'],
    //                         'forgot_token' => $generate_forgot_password,
    //                         'total_entity' => $total_entity,
    //                     ]
    //                 ])
    //             ]);

    //             dispatch(new SendMailJob($notification->notification_id));
    //         }

    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Data karyawan berhasil disimpan',
    //             'data' => $karyawan
    //         ]);
            

    //     } catch(Error $e) {
    //         // delete file if already upload
    //         if(isset($uploaded_photo) && $uploaded_photo['success']) {
    //             if($uploaded_photo['data']) {
    //                 $appupload_library->deleteFile('images/karyawan/profil/'.$uploaded_photo['data']['filename']);
    //             }
    //         }
            
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //             'data' => [
    //                 'uploaded_photo' => isset($uploaded_photo) ? $uploaded_photo : null
    //             ]
    //         ]);
    //     } catch(Exception $e) {
    //         // delete file if already upload
    //         if(isset($uploaded_photo) && $uploaded_photo['success']) {
    //             if($uploaded_photo['data']) {
    //                 $appupload_library->deleteFile('images/karyawan/profil/'.$uploaded_photo['data']['filename']);
    //             }
    //         }
            
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //             'data' => [
    //                 'uploaded_photo' => isset($uploaded_photo) ? $uploaded_photo : null
    //             ]
    //         ]);
    //     }
    // }

    // public function update($karyawan_id, Request $request)
    // {
    //     $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
    //     $is_user = $request->input('karyawan_isuser');
    //     $is_manager = 0;
    //     $manager_id = intval($request->input('karyawan_manager'));
    //     $removemanager_id = 0;
    //     $email = $request->input('karyawan_email');
    //     $karyawan_tambahan = $request->input('karyawan_tambahan');
    //     $user_id = session()->get('user_data')['user_id'];
    //     // dd($request->input());

    //     $karyawan = KaryawanModel::select("*"
    //     , DB::raw("(SELECT COUNT(currkaryawan.*) FROM ms_karyawan as currkaryawan 
    //     WHERE currkaryawan.karyawan_email = ms_karyawan.karyawan_email
    //     AND currkaryawan.karyawan_email IS NOT NULL) as total_karyawan")
    //     , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_lock='1' LIMIT 1) payroll_islock")
    //     , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_lock='1' AND TO_CHAR(payroll_period, 'Y') = TO_CHAR(NOW(), 'Y') LIMIT 1) payroll_currentyear_exist")
    //     )->where([
    //         'karyawan_id' => $karyawan_id, 
    //         'karyawan_active' => 1,
    //         'ms_wajibpajak_id' => $wajibpajak_id,
    //     ])->first();
        
    //     if(!$karyawan) {
    //         abort(404);
    //     }
    //     if($karyawan->karyawan_manager_id != $manager_id) {
    //         $removemanager_id = intval($karyawan->karyawan_manager_id);
    //     }
    //     // dd($removemanager_id);
    //     // dd($karyawan->total_karyawan);
    //     $rules = [
    //         'karyawan_enid' => ['required', 
    //             Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id, $karyawan_id, $user_id) {
    //                 return $query->where([
    //                     'ms_user_id' => $user_id,
    //                     'ms_wajibpajak_id' => $wajibpajak_id
    //                 ])
    //                 ->where('karyawan_id', '<>', $karyawan_id);
    //             })
    //         ],
    //         'karyawan_nik' => [
    //             'required',
    //             Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id, $karyawan_id, $user_id) {
    //                 return $query->where([
    //                     'ms_user_id' => $user_id,
    //                     'ms_wajibpajak_id' => $wajibpajak_id
    //                 ])
    //                 ->where('karyawan_id', '<>', $karyawan_id);
    //             })
    //         ],
    //         'karyawan_name' => 'required',
    //         'karyawan_email' => $email ? 'email' : '',
    //         'karyawan_citizenship' => 'required|in:WNI,WNA',
    //         'ptkp_id' => 'required',
    //         'karyawan_gender' => 'required',
    //         'karyawan_status' => 'required|in:TETAP,KONTRAK,PERCOBAAN',
    //         'karyawan_contract_begin' => 'required|date_format:d-m-Y',
    //         'karyawan_calculation_method' =>  'required|in:GROSS,GROSS_UP,NETT',
    //         'karyawan_salary' => 'required',
    //         // 'karyawan_payroll' => 'required',
    //         'karyawan_npwp' => ['required', 
    //             Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id, $karyawan_id, $user_id) {
    //                 return $query->where([
    //                     'ms_user_id' => $user_id,
    //                     'ms_wajibpajak_id' => $wajibpajak_id
    //                 ])
    //                 ->where('karyawan_id', '<>', $karyawan_id)
    //                 ->where('karyawan_npwp', '<>', '00.000.000.0-000.000');
    //             })
    //         ],
    //         // 'karyawan_bankid' => 'required',
    //         // 'karyawan_banknokartu' => 'required',
    //     ];
        
    //     if($email) {
    //         $rules['karyawan_email'] = ['required','email',
    //             Rule::unique('ms_karyawan')->where(function ($query) use($wajibpajak_id, $karyawan_id, $user_id) {
    //                 return $query->where([
    //                     'ms_user_id' => $user_id,
    //                     'ms_wajibpajak_id' => $wajibpajak_id
    //                 ])->where('karyawan_id', '<>', $karyawan_id);
    //             }),
    //         ];
    //     }

    //     // if($request->input('karyawan_npwp')) {
    //     //     $rules['karyawan_npwp'] = [
    //     //         Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id, $karyawan_id, $user_id) {
    //     //             return $query->where([
    //     //                 'ms_user_id' => $user_id,
    //     //                 'ms_wajibpajak_id' => $wajibpajak_id
    //     //             ])->where('karyawan_id', '<>', $karyawan_id);
    //     //         })
    //     //     ];
    //     // }
    //     if ($request->hasfile('karyawan_photo')) {
    //         $rules['karyawan_photo'] = 'mimes:jpeg,jpg,png|max:256';
    //     }
    //     if ($request->input('karyawan_status') == 'PERCOBAAN' || $request->input('karyawan_status') == 'KONTRAK') {
    //         $rules['karyawan_contract_end'] = 'required|date_format:d-m-Y';
    //     }
    //     if($request->input('karyawan_isgetbpjskes') == 1) {
    //         $rules['karyawan_bpjskesdate'] = 'required|date_format:d-m-Y';
    //         $rules['karyawan_bpjskesno'] = 'required|unique:ms_karyawan,karyawan_bpjskesno,'.$karyawan_id.',karyawan_id';
    //     }
    //     if($request->input('karyawan_isgetbpjstk') == 1) {
    //         $rules['karyawan_bpjstkdate'] = 'required|date_format:d-m-Y';
    //         $rules['karyawan_bpjstkno'] = 'required|unique:ms_karyawan,karyawan_bpjstkno,'.$karyawan_id.',karyawan_id';
    //     }

    //     $dt_cbegin = \Carbon\Carbon::parse($request->input('karyawan_contract_begin'));
    //     // check tgl masuk is different
    //     $deleted_prev_payroll = false;
    //     if($request->input('karyawan_contract_begin') && $karyawan->karyawan_contract_begin != $dt_cbegin->translatedFormat('Y-m-d')) {
    //         if($karyawan->payroll_islock > 0) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Tanggal kontrak tidak bisa dirubah. Sudah ada transaksi penggajian!'
    //             ]);
    //         }
    //         $deleted_prev_payroll = true;
    //     } else {
    //         $rules['karyawan_contract_begin'] = '';
    //         $request->request->add(['karyawan_contract_begin' => $karyawan->karyawan_contract_begin]);
    //     }
    //     // check if ptkp is different
    //     if($request->input('ptkp_id') && $karyawan->ms_ptkp_id != $request->input('ptkp_id')) {
    //         if($karyawan->payroll_currentyear_exist > 0) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'PTKP tidak bisa dirubah. Sudah ada transaksi penggajian periode ini!'
    //             ]);
    //         }
    //     } else {
    //         $rules['ptkp_id'] = '';
    //         $request->request->add(['ptkp_id' => $karyawan->ms_ptkp_id]);
    //     }
    //     // check if calculation method is different
    //     if($request->input('karyawan_calculation_method') && $karyawan->karyawan_calculation_method != $request->input('karyawan_calculation_method')) {
    //         if($karyawan->payroll_currentyear_exist > 0) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Metode PPh21 tidak bisa dirubah. Sudah ada transaksi penggajian periode ini!'
    //             ]);
    //         }
    //     } else {
    //         $rules['karyawan_calculation_method'] = '';
    //         $request->request->add(['karyawan_calculation_method' => $karyawan->karyawan_calculation_method]);
    //     }
    //     $this->validate($request, $rules, [
    //         'karyawan_nik.required' => 'NIK wajib diisi!',
    //         'karyawan_nik.unique' => 'NIK sudah terdaftar! Silahkan gunakan NIK lainnya.',
    //         'karyawan_npwp.unique' => 'NPWP sudah terdaftar! Silahkan gunakan NPWP lainnya.',
    //         'karyawan_name.required' => 'Nama wajib diisi!',
    //         'karyawan_email.required' => 'Email wajib diisi!',
    //         'karyawan_email.email' => 'Format email tidak sesuai!',
    //         'karyawan_email.unique' => 'Email sudah digunakan. Silahkan gunakan email lainnya!',
    //         'karyawan_citizenship.required' => 'Kewarganegaraan wajib diisi!',
    //         'ptkp_id.required' => 'Status perkawinan wajib diisi!',
    //         'karyawan_gender.required' => 'Jenis Kelamin wajib diisi!',
    //         'karyawan_status.required' => 'Status karyawan wajib diisi!',
    //         'karyawan_contract_begin.required' => 'Tgl. Kontrak wajib diisi!',
    //         'karyawan_calculation_method.required' => 'Metode perhitungan wajib diisi!',
    //         'karyawan_salary.required' => 'Gaji pokok wajib diisi!',
    //         // 'karyawan_payroll.required' => 'Penggajian wajib diisi!',
    //         // 'karyawan_bankid.required' => 'Nama Bank wajib diisi!',
    //         // 'karyawan_banknokartu.required' => 'Nomor Kartu Bank wajib diisi!',
    //         'karyawan_bpjskesno.unique' => 'No. Kartu BPJS Kesehatan sudah digunakan!',
    //         'karyawan_bpjstkno.unique' => 'No. Kartu BPJS TK sudah digunakan!',
    //         'karyawan_photo.mimes' => 'Format gambar harus jpg atau png!',
    //         'karyawan_photo.max' => 'Ukuran gambar maksimal 250 Kb!',
    //     ]);

    //     $old_photo = $karyawan->karyawan_photo;
    //     $old_isuser = $karyawan->karyawan_isuser;
    //     $old_status = $karyawan->karyawan_status;
    //     $old_email = $karyawan->karyawan_email;
    //     $photo_url = null;
    //     if ($request->hasfile('karyawan_photo')) {
    //         $file = $request->file('karyawan_photo');
    //         // dd($file->extension());
    //         $appupload_library = new AppUploadLibrary();
    //         $uploaded_photo = $appupload_library->uploadFile($file, 'images/karyawan/profil');

    //         if($uploaded_photo['success'] == false) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => $uploaded_photo['message']
    //             ]);
    //         }
    //         $photo_url = $uploaded_photo['data']['url'];
    //     }

    //     $dt = null;
    //     if($request->input('karyawan_birthdate')) {
    //         $dt = \Carbon\Carbon::parse($request->input('karyawan_birthdate'));
    //     }
        
        
    //     $dt_bpjskes = null;
    //     $dt_bpjstk = null;
    //     $dt_pend = null;
    //     $karyawan_bpjskeslainnya = 0;
    //     $karyawan_bpjstklainnya = 0;
    //     $tunjanganbpjs = SettingBpjsKaryawanModel::where([
    //         'ms_wajibpajak_id' => $wajibpajak_id,
    //         'stbpjskaryawan_active' => 1
    //     ])->first();
    //     $stbpjskaryawan_data = ($tunjanganbpjs) ? json_decode($tunjanganbpjs->stbpjskaryawan_value) : [];
    //     $bpjskes = false;
    //     $bpjstk = false;
    //     $stbpjskes_lainnya = false;
    //     $stbpjstk_lainnya = false;
    //     foreach ($stbpjskaryawan_data as $stbpjs) {
    //         if ($stbpjs->type == 'KESEHATAN') {
    //             $bpjskes = true;
    //             if($stbpjs->name == 'LAINNYA') {
    //                 $stbpjskes_lainnya = true;
    //             }
    //         }
    //         if ($stbpjs->type == 'TENAGA_KERJA') {
    //             $bpjstk = true;
    //             if($stbpjs->name == 'LAINNYA') {
    //                 $stbpjstk_lainnya = true;
    //             }
    //         }
    //     }

    //     if($bpjskes && $request->input('karyawan_isgetbpjskes') == 1) {
    //         $dt_bpjskes = \Carbon\Carbon::parse($request->input('karyawan_bpjskesdate'));
    //         if($stbpjskes_lainnya)
    //             $karyawan_bpjskeslainnya = $request->input('karyawan_bpjskeslainnya');
    //     }
    //     if($bpjstk && $request->input('karyawan_isgetbpjstk')) {
    //         $dt_bpjstk = \Carbon\Carbon::parse($request->input('karyawan_bpjstkdate'));
    //         if($stbpjstk_lainnya)
    //             $karyawan_bpjstklainnya = $request->input('karyawan_bpjstklainnya');
    //     }
    //     if($request->input('karyawan_status') != 'TETAP') {
    //         $dt_pend = \Carbon\Carbon::parse($request->input('karyawan_contract_end'));
    //     }

    //     $karyawan_calculation_method = $request->input('karyawan_calculation_method');

    //     $escape_email = DB::connection()->getPdo()->quote($email);
    //     $wajibpajak = WajibPajakModel::select("*"
    //     , DB::raw("(SELECT COUNT(currkaryawan.*) FROM ms_karyawan as currkaryawan 
    //     WHERE currkaryawan.karyawan_email = {$escape_email}
    //     AND currkaryawan.karyawan_email IS NOT NULL LIMIT 1) as total_karyawan")
    //     , DB::raw("(SELECT row_to_json(mtable) 
    //     FROM (
    //         SELECT manager.karyawan_id, manager.karyawan_ismanager
    //         FROM ms_karyawan as manager
    //         WHERE manager.ms_wajibpajak_id = {$wajibpajak_id}
    //         AND manager.karyawan_id = {$manager_id}
    //         LIMIT 1
    //     )
    //     as mtable) as manager_json")
    //     , DB::raw("(SELECT COUNT(subkaryawan.*) FROM ms_karyawan as subkaryawan 
    //     WHERE subkaryawan.ms_wajibpajak_id = {$wajibpajak_id}
    //     AND subkaryawan.karyawan_manager_id = {$removemanager_id}
    //     LIMIT 1) as total_subkaryawan")
    //     )
    //     ->where(['wajibpajak_id' => $wajibpajak_id])->first();

    //     // dd($wajibpajak->total_subkaryawan);

    //     $total_entity = ($karyawan->karyawan_email == $email && intval($wajibpajak->total_karyawan) == 1) ? 0 : intval($wajibpajak->total_karyawan);
    //     // $karyawan_infofield = ($wajibpajak->karyawan_infofield) ? json_decode($wajibpajak->karyawan_infofield) : null;
        
    //     $tempdata_tambahan = [];
    //     $karyawan_infokey = [];
    //     if($karyawan_tambahan) {
    //         foreach($karyawan_tambahan as $ktb) {
    //             array_push($karyawan_infokey, $ktb['nama']);
    //             if(isset($ktb['nama']) && isset($ktb['keterangan'])) {
    //                 array_push($tempdata_tambahan, $ktb);
    //             }
    //         }
    //     }

    //     DB::beginTransaction();
    //     try {
    //         KaryawanInfoFieldModel::where([
    //             'ms_user_id' => $wajibpajak->ms_user_id,
    //             'ms_wajibpajak_id' => $wajibpajak_id,
    //         ])->update([
    //             'karyawaninfofield_fields' => ($karyawan_infokey) ? implode(',', $karyawan_infokey) : null
    //         ]);

    //         $division_id = $request->input('karyawan_division');
    //         if($division_id) {
    //             if(!is_numeric($division_id)) {
    //                 $division = KaryawanDivisiModel::create([
    //                     'karyawandivisi_name' => $request->input('karyawan_division'),
    //                     'ms_user_id' => $wajibpajak->ms_user_id,
    //                     'ms_wajibpajak_id' => $wajibpajak_id,
    //                 ]);
    //                 $division_id = $division->karyawandivisi_id;
    //             }
    //         }

    //         $position_id = $request->input('karyawan_position');
    //         if($position_id) {
    //             if(!is_numeric($position_id)) {
    //                 $position = KaryawanJabatanModel::create([
    //                     'karyawanjabatan_name' => $request->input('karyawan_position'),
    //                     'ms_user_id' => $wajibpajak->ms_user_id,
    //                     'ms_wajibpajak_id' => $wajibpajak_id,
    //                 ]);
    //                 $position_id = $position->karyawanjabatan_id;
    //             }
    //         }

    //         $save_data_karyawan = [
    //             'karyawan_enid' => $request->input('karyawan_enid'),
    //             'karyawan_nik' => $request->input('karyawan_nik'),
    //             'karyawan_npwp' => $request->input('karyawan_npwp'),
    //             'karyawan_name' => $request->input('karyawan_name'),
    //             'karyawan_birthdate' => ($dt) ? $dt->translatedFormat('Y-m-d') : null,
    //             'karyawan_birthplace' => $request->input('karyawan_birthplace'),
    //             'karyawan_contract_begin' => ($dt_cbegin) ? $dt_cbegin->translatedFormat('Y-m-d') : null,
    //             'karyawan_contract_end' => ($dt_pend) ? $dt_pend->translatedFormat('Y-m-d') : null,
    //             'karyawan_phone' => $request->input('karyawan_phone'),
    //             'karyawan_email' => $request->input('karyawan_email'),
    //             'karyawan_citizenship' => $request->input('karyawan_citizenship'),
    //             'karyawan_gender' => $request->input('karyawan_gender'),
    //             'karyawan_address' => $request->input('karyawan_address'),
    //             'karyawan_status' => $request->input('karyawan_status'),
    //             'karyawan_calculation_method' => $karyawan_calculation_method,
    //             'karyawan_salary' => ($request->input('karyawan_salary')) ? intval($request->input('karyawan_salary')) : 0,
    //             'ms_ptkp_id' => $request->input('ptkp_id'),
    //             'ms_country_id' => ($request->input('karyawan_citizenship') == 'WNI') ? 100 : $request->input('country_id'),
    //             'ms_bank_id' => $request->input('karyawan_bankid'),
    //             'ms_karyawandivisi_id' => $division_id,
    //             'ms_karyawanjabatan_id' => $position_id,
    //             // 'st_attendance_id' => $request->input('karyawan_attendance'),
    //             'st_tunjangan_id' => ($request->input('karyawan_allowance')) ? implode(',', $request->input('karyawan_allowance')) : null,
    //             'st_potongan_id' => ($request->input('karyawan_deduction')) ? implode(',', $request->input('karyawan_deduction')) : null,
    //             // 'st_penggajian_id' => $request->input('karyawan_payroll'),
    //             // 'karyawan_ismanager' => $is_manager,
    //             'karyawan_isuser' => ($is_user == '1') ? 1 : 0,
    //             'karyawan_manager_id' => ($manager_id) ? $manager_id : 0,
    //             'karyawan_bankno' => $request->input('karyawan_banknokartu'),
    //             'karyawan_isbpjskes' => $request->input('karyawan_isgetbpjskes'),
    //             'karyawan_bpjskesdate' => ($dt_bpjskes) ? $dt_bpjskes->translatedFormat('Y-m-d') : null,
    //             'karyawan_bpjskesno' => $request->input('karyawan_isgetbpjskes') == 1 ? $request->input('karyawan_bpjskesno') : null,
    //             'karyawan_isbpjstk' => $request->input('karyawan_isgetbpjstk'),
    //             'karyawan_bpjstkdate' => ($dt_bpjstk) ? $dt_bpjstk->translatedFormat('Y-m-d') : null,
    //             'karyawan_bpjstkno' => $request->input('karyawan_isgetbpjstk') == 1 ? $request->input('karyawan_bpjstkno') : null,
    //             'karyawan_bpjskeslainnya' => $karyawan_bpjskeslainnya,
    //             'karyawan_bpjstklainnya' => $karyawan_bpjstklainnya,
    //             // 'karyawan_photo' => $photo_url,
    //             'karyawan_additionalinfo' => ($tempdata_tambahan) ? json_encode($tempdata_tambahan) : null,
    //         ];
    //         // dd($save_data_karyawan);
            
    //         if($photo_url) {
    //             $save_data_karyawan['karyawan_photo'] = $photo_url;
    //         }
            
    //         if($is_user == '1') {
    //             if($old_email != $email || $old_isuser == '0') {
    //                 $save_data_karyawan['karyawan_password'] = Hash::make(Str::random(6));
    //                 $save_data_karyawan['karyawan_forgot_password'] = Hash::make('FGP'.$email.uniqid());
    //             }
    //         }
    //         $karyawan->update($save_data_karyawan);

    //         // remove column
    //         unset($karyawan->karyawan_password);
    //         unset($karyawan->karyawan_forgot_password);
             
    //         // update tr_karyawan_masakerja if status not different
    //         if($request->input('karyawan_status') == $old_status) {
    //             // update masa kerja
    //             KaryawanMasakerjaModel::where([
    //                 'ms_karyawan_id' => $karyawan_id,
    //                 'ms_user_id' => $wajibpajak->ms_user_id,
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //                 'karyawanmasakerja_active' => 1,
    //             ])->update([
    //                 'ms_ptkp_id' => $request->input('ptkp_id'),
    //                 'karyawanmasakerja_calculation_method' => $karyawan_calculation_method,
    //                 'karyawanmasakerja_salary' => ($request->input('karyawan_salary')) ? intval($request->input('karyawan_salary')) : 0,
    //                 'karyawanmasakerja_position' => $request->input('karyawan_position'),
    //                 'karyawanmasakerja_nik' => $request->input('karyawan_nik'),
    //                 'karyawanmasakerja_npwp' => $request->input('karyawan_npwp'),
    //                 'ms_objekpajak_code' => $karyawan->karyawan_code_objekpajak,
    //                 'karyawanmasakerja_contract_begin' => $dt_cbegin->translatedFormat('Y-m-d'),
    //                 'karyawanmasakerja_data' => json_encode($karyawan),
    //             ]);
    //         } else {
    //             // insert masa kerja
    //             KaryawanMasakerjaModel::create([
    //                 'ms_karyawan_id' => $karyawan->karyawan_id,
    //                 'ms_ptkp_id' => $request->input('ptkp_id'),
    //                 'ms_user_id' => $wajibpajak->ms_user_id,
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //                 'karyawanmasakerja_status' => $request->input('karyawan_status'),
    //                 'karyawanmasakerja_calculation_method' => $karyawan_calculation_method,
    //                 'karyawanmasakerja_salary' => ($request->input('karyawan_salary')) ? intval($request->input('karyawan_salary')) : 0,
    //                 'karyawanmasakerja_position' => $request->input('karyawan_position'),
    //                 'karyawanmasakerja_nik' => $request->input('karyawan_nik'),
    //                 'karyawanmasakerja_npwp' => $request->input('karyawan_npwp'),
    //                 'ms_objekpajak_code' => $karyawan->karyawan_code_objekpajak,
    //                 'karyawanmasakerja_contract_begin' => $dt_cbegin->translatedFormat('Y-m-d'),
    //                 'karyawanmasakerja_data' => json_encode($karyawan),
    //             ]);
    //         }

    //         // check if manager already set or not
    //         if($wajibpajak->manager_json) {
    //             $manager = json_decode($wajibpajak->manager_json);
    //             if($manager->karyawan_ismanager != 1) {
    //                 KaryawanModel::where(['karyawan_id' => $manager->karyawan_id, 'ms_wajibpajak_id' => $wajibpajak_id])
    //                 ->update(['karyawan_ismanager' => 1]);
    //             }
    //         }
    //         // check if manager has subordinate or not
    //         if($removemanager_id) {
    //             if($wajibpajak->total_subkaryawan <= 1) {
    //                 KaryawanModel::where(['karyawan_id' => $removemanager_id, 'ms_wajibpajak_id' => $wajibpajak_id])
    //                 ->update(['karyawan_ismanager' => 0]);
    //             }
    //         }

    //         // if($request->input('karyawan_attendance')) {
    //         //     $checkAttendance = SettingAttendanceModel::where([
    //         //         'ms_wajibpajak_id' => $wajibpajak_id,
    //         //         'attendance_is_all' => true
    //         //     ])
    //         //     ->where('attendance_id', '<>', $request->input('karyawan_attendance'))->first();

    //         //     if($checkAttendance) {
    //         //         $checkAttendance->update([
    //         //             'attendance_is_all' => false
    //         //         ]);
    //         //     }
    //         // }

    //         // if($request->input('karyawan_payroll')) {
    //         //     $checkPayroll = SettingPenggajianKaryawanModel::where([
    //         //         'ms_wajibpajak_id' => $wajibpajak_id,
    //         //         'stpenggajiankaryawan_is_all' => true
    //         //     ])
    //         //     ->where('stpenggajiankaryawan_id', '<>', $request->input('karyawan_payroll'))->first();

    //         //     if($checkPayroll) {
    //         //         $checkPayroll->update([
    //         //             'stpenggajiankaryawan_is_all' => false
    //         //         ]);
    //         //     }
    //         // }

    //         if($request->input('karyawan_allowance')) {
    //             $checkAllowance = SettingTunjanganKaryawanModel::where([
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //                 'sttunjangankaryawan_is_all' => true
    //             ])->pluck('sttunjangankaryawan_id')->toArray();

    //             $diffAllowance = array_diff($checkAllowance, $request->input('karyawan_allowance'));
                
    //             SettingTunjanganKaryawanModel::whereIn('sttunjangankaryawan_id', $diffAllowance)
    //             ->where([
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //             ])
    //             ->update([
    //                'sttunjangankaryawan_is_all' => false
    //             ]);

    //             $checkDetailAllowance = SettingTunjanganKaryawanDetailModel::where([
    //                 'ms_karyawan_id' => $karyawan->karyawan_id,
    //             ])->pluck('st_tunjangankaryawan_id')->toArray();
                
    //             $diffDetailAllowance = array_diff($checkDetailAllowance, $request->input('karyawan_allowance'));
                
    //             SettingTunjanganKaryawanDetailModel::whereIn('st_tunjangankaryawan_id', $diffDetailAllowance)
    //             ->where([
    //                 'ms_karyawan_id' => $karyawan->karyawan_id,
    //             ])
    //             ->update([
    //                 'sttunjangankaryawandet_active' => '0'
    //             ]);

    //             $bulkAllowance = [];

    //             foreach ($request->input('karyawan_allowance') as $st_tunjangankaryawan_id) {
    //                 $bulkAllowance[] = [
    //                     'st_tunjangankaryawan_id' => $st_tunjangankaryawan_id,
    //                     'ms_karyawan_id' => $karyawan->karyawan_id,
    //                     'sttunjangankaryawandet_active' => '1'
    //                 ];
    //             }
                
    //             // Loop through each record and perform update or insert
    //             foreach ($bulkAllowance as $allowance) {
    //                 SettingTunjanganKaryawanDetailModel::updateOrInsert(
    //                     [
    //                         'st_tunjangankaryawan_id' => $allowance['st_tunjangankaryawan_id'],
    //                         'ms_karyawan_id' => $allowance['ms_karyawan_id'],
    //                     ],
    //                     $allowance
    //                 );
    //             }
    //         }

    //         if($request->input('karyawan_deduction')) {
    //             $checkDeduction = SettingPotonganKaryawanModel::where([
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //                 'stpotongankaryawan_is_all' => true
    //             ])->pluck('stpotongankaryawan_id')->toArray();

    //             $diffDeduction = array_diff($checkDeduction, $request->input('karyawan_deduction'));
                
    //             SettingPotonganKaryawanModel::whereIn('stpotongankaryawan_id', $diffDeduction)
    //             ->where([
    //                 'ms_wajibpajak_id' => $wajibpajak_id,
    //             ])
    //             ->update([
    //                'stpotongankaryawan_is_all' => false
    //             ]);

    //             $checkDetailDeduction = SettingPotonganKaryawanDetailModel::where([
    //                 'ms_karyawan_id' => $karyawan->karyawan_id
    //             ])->pluck('st_potongankaryawan_id')->toArray();
                
    //             $diffDetailDeduction = array_diff($checkDetailDeduction, $request->input('karyawan_deduction'));
                
    //             SettingPotonganKaryawanDetailModel::whereIn('st_potongankaryawan_id', $diffDetailDeduction)
    //             ->where([
    //                 'ms_karyawan_id' => $karyawan->karyawan_id,
    //             ])
    //             ->update([
    //                 'stpotongankaryawandet_active' => '0'
    //             ]);

    //             $bulkDeduction = [];

    //             foreach ($request->input('karyawan_deduction') as $st_potongankaryawan_id) {
    //                 $bulkDeduction[] = [
    //                     'st_potongankaryawan_id' => $st_potongankaryawan_id,
    //                     'ms_karyawan_id' => $karyawan->karyawan_id,
    //                     'stpotongankaryawandet_active' => '1'
    //                 ];
    //             }

    //              // Loop through each record and perform update or insert
    //             foreach ($bulkDeduction as $deduction) {
    //                 SettingPotonganKaryawanDetailModel::updateOrInsert(
    //                     [
    //                         'st_potongankaryawan_id' => $deduction['st_potongankaryawan_id'],
    //                         'ms_karyawan_id' => $deduction['ms_karyawan_id'],
    //                     ],
    //                     $deduction
    //                 );
    //             }
    //         }

    //         // processed the leave setting not all employees
    //         $stleave = SettingLeaveModel::where([
    //             'leave_status_active' => true,
    //             'ms_wajibpajak_id' => $wajibpajak_id,
    //         ])
    //         ->whereIn('leave_used_for', [1,2,3,4,5])->get();
    //         if($stleave) {
    //             $tempdata_leave = $this->stleave_tempdata($stleave, [
    //                 [
    //                     'karyawan_id' => $karyawan->karyawan_id,
    //                     'karyawan_gender' => $save_data_karyawan['karyawan_gender'],
    //                     'ms_karyawandivisi_id' => $save_data_karyawan['ms_karyawandivisi_id'],
    //                     'ms_karyawanjabatan_id' => $save_data_karyawan['ms_karyawanjabatan_id'],
    //                 ]
    //             ]);
    //             // check detail leave
    //             $stleavedetail = SettingLeaveDetailModel::where([
    //                 'ms_karyawan_id' => $karyawan->karyawan_id,
    //             ])->get();
                
    //             $stleaveids = [];
    //             foreach($tempdata_leave as $tdleave) {
    //                 array_push($stleaveids, $tdleave['st_leave_id']);
    //                 SettingLeaveDetailModel::updateOrInsert(
    //                     [
    //                         'st_leave_id' => $tdleave['st_leave_id'],
    //                         'ms_karyawan_id' => $tdleave['ms_karyawan_id'],
    //                     ],
    //                     $tdleave
    //                 );
    //             }
    //             $deletedstleaveids = []; 
    //             foreach($stleavedetail as $lvdet) {
    //                 if(!in_array($lvdet->st_leave_id, $stleaveids)) {
    //                     array_push($deletedstleaveids, $lvdet->st_leave_id);
    //                 }
    //             }
    //             if($deletedstleaveids) {
    //                 SettingLeaveDetailModel::whereIn('st_leave_id', $deletedstleaveids)
    //                 ->where(['ms_karyawan_id' => $karyawan->karyawan_id])
    //                 ->update(['leavedetail_active' => false]);
    //             }
    //         }

    //         if($is_user == '1') {
    //             if($old_email != $email || $old_isuser == '0') {
    //                 // insert tr_notification
    //                 $notification = NotificationModel::create([
    //                     'notification_title' => 'Rekkaa - Undangan Dari '.$wajibpajak->wajibpajak_name,
    //                     'notification_type' => 'EMAIL',
    //                     'notification_from' => env("MAIL_FROM_ADDRESS"),
    //                     'notification_to' => $email,
    //                     'ms_user_id' => $wajibpajak->ms_user_id,
    //                     'ms_wajibpajak_id' => $wajibpajak_id,
    //                     'notification_view' => 'email.email-undangan-akun-karyawan',
    //                     'notification_data' => json_encode([
    //                         'entity' => [
    //                             'name' => $wajibpajak->wajibpajak_name,
    //                             'user_name' => $request->input('karyawan_name'),
    //                             'inviter_email' => session()->get('user_data')['user_email'],
    //                             'forgot_token' => $save_data_karyawan['karyawan_forgot_password'],
    //                             'total_entity' => $total_entity,
    //                         ]
    //                     ])
    //                 ]);

    //                 dispatch(new SendMailJob($notification->notification_id));
    //             }
    //         }

    //         if($deleted_prev_payroll) {
    //             $payroll_dtparse = Carbon::parse($save_data_karyawan['karyawan_contract_begin']);
    //             $payrolls = PayrollModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_karyawan_id' => $karyawan_id, 'payroll_lock' => 0])
    //             ->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') < ?)", [$payroll_dtparse->format('Y-m')])->get();

    //             $payroll_uuids = [];
    //             foreach($payrolls as $py) {
    //                 array_push($payroll_uuids, $py['payroll_uuid']);
    //             }
    //             if(count($payroll_uuids) > 0) {
    //                 PPh21Model::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_karyawan_id' => $karyawan_id])
    //                 ->whereRaw("(TO_CHAR(pph21_period, 'YYYY-MM') < ?)", [$payroll_dtparse->format('Y-m')])
    //                 ->whereIn('tr_payroll_uuid', $payroll_uuids)->delete();

    //                 PayrollModel::whereIn('payroll_uuid', $payroll_uuids)->delete();
    //             }
    //         }

    //         DB::commit();

    //         // delete old photo if exist
    //         if($photo_url && $old_photo) {
    //             $appupload_library->deleteFile($old_photo, true);
    //         }
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Data karyawan berhasil diperbarui',
    //             'data' => $karyawan
    //         ]);
            

    //     } catch(Error $e) {
    //         // delete file if already upload
    //         if(isset($uploaded_photo) && $uploaded_photo['success']) {
    //             if($uploaded_photo['data']) {
    //                 $appupload_library->deleteFile('images/karyawan/profil/'.$uploaded_photo['data']['filename']);
    //             }
    //         }
            
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //             'data' => [
    //                 'uploaded_photo' => isset($uploaded_photo) ? $uploaded_photo : null
    //             ]
    //         ]);
    //     } catch(Exception $e) {
    //         // delete file if already upload
    //         if(isset($uploaded_photo) && $uploaded_photo['success']) {
    //             if($uploaded_photo['data']) {
    //                 $appupload_library->deleteFile('images/karyawan/profil/'.$uploaded_photo['data']['filename']);
    //             }
    //         }
            
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage(),
    //             'data' => [
    //                 'uploaded_photo' => isset($uploaded_photo) ? $uploaded_photo : null
    //             ]
    //         ]);
    //     }
    // }

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
        $order_columns = ['akunbiaya_id'];
        $order_col = $order_columns[0];//(isset($request->input('order')[0]) && isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : null;
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $where = [
            'akunbiaya_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];
        $list = AkunBiayaModel::select('tr_akun_biaya.*')
        ->where($where)
        // ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
        // ->when($search, function($q, $search) {
        //     return $q->where(DB::raw('LOWER(karyawan_nik)'), 'like', '%'.strtolower($search).'%')
        //     ->orWhere(DB::raw('LOWER(karyawan_npwp)'), 'like', '%'.strtolower($search).'%')
        //     ->orWhere(DB::raw('LOWER(karyawan_name)'), 'like', '%'.strtolower($search).'%');
        // })
        ->take($limit)->skip($offset)->orderBy($order_col, $order_type)->get();
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = AkunBiayaModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    // public function delete($karyawanId, Request $request)
    // {
    //     $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
    //     $karyawan = KaryawanModel::where([
    //         'karyawan_id' => $karyawanId, 
    //         'karyawan_active' => 1,
    //         'ms_wajibpajak_id' => $wajibpajak_id,
    //     ])
    //     ->first();
    //     if(!$karyawan) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Karyawan tidak ditemukan!'
    //         ]); 
    //     }

    //     $validator = Validator::make($request->all(), [
    //         'nonaktif_tgl' => 'required',
    //         'nonaktif_alasan' => 'required|in:RESIGN,LAINNYA,KONTRAK_HABIS',
    //     ], [
    //         'nonaktif_tgl.required' => 'Tanggal wajib diisi!',
    //         'nonaktif_alasan.required' => 'Alasan wajib diisi!',
    //     ]);

    //     if ($validator->fails()) {
    //         $error = $validator->errors()->first();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $error
    //         ]);
    //     }
        
    //     $dt_cend = \Carbon\Carbon::parse($request->input('nonaktif_tgl'));
    //     $dt_cend_date = $dt_cend->translatedFormat('Y-m-d');
    //     DB::beginTransaction();
    //     try {
    //         KaryawanModel::where(['karyawan_id' => $karyawan->karyawan_id])
    //         ->update([
    //             'karyawan_active' => 0,
    //             'karyawan_contract_end' => $dt_cend_date,
    //             'karyawan_end_type' => $request->input('nonaktif_alasan'),
    //             'karyawan_end_reason' => $request->input('nonaktif_keterangan'),
    //         ]);

    //         // update masa kerja
    //         KaryawanMasakerjaModel::where([
    //             'ms_karyawan_id' => $karyawan->karyawan_id,
    //             'karyawanmasakerja_active' => 1,
    //         ])->update([
    //             'karyawanmasakerja_contract_end' => $dt_cend_date,
    //             'karyawanmasakerja_end_type' => $request->input('nonaktif_alasan'),
    //             'karyawanmasakerja_end_reason' => $request->input('nonaktif_keterangan'),
    //             'karyawanmasakerja_active' => 0,
    //         ]);

    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Karyawan berhasil dinonaktifkan',
    //         ]);

    //     } catch(Error $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }
}