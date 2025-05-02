<?php
namespace App\Http\Controllers\User\Master;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\Info\InfoController;
use App\Http\Controllers\User\Pengaturan\PengaturanTunjanganController;
use App\Jobs\ImportJob;
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


class KaryawanController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $data = [
            'title' => 'Data Karyawan',
            'content' => 'user.master.karyawan.index',
        ];
        // dd($calculate_pph21 / 12);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function create(Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $setting = DB::select(DB::raw("
        SELECT (
                SELECT json_agg(ptable)
                FROM (
                    SELECT st_tunjangan_karyawan.*
                    FROM st_tunjangan_karyawan
                    WHERE ms_wajibpajak_id = {$wajibpajak_id}
                    AND sttunjangankaryawan_active = '1'
                    AND sttunjangankaryawan_used_for IN (1,2,3,4,5)
                    ORDER BY sttunjangankaryawan_id ASC
                ) as ptable
            ) as setting_tunjangankaryawan_json,
            (
                SELECT json_agg(ptable)
                FROM (
                    SELECT st_potongan_karyawan.*
                    FROM st_potongan_karyawan
                    WHERE ms_wajibpajak_id = {$wajibpajak_id}
                    AND stpotongankaryawan_active = '1'
                    AND stpotongankaryawan_used_for IN (1,2,3,4,5)
                    ORDER BY stpotongankaryawan_id ASC
                ) as ptable
            ) as setting_potongankaryawan_json,
            (
                SELECT json_agg(ptable)
                FROM (
                    SELECT st_attendance.*
                    FROM st_attendance
                    WHERE ms_wajibpajak_id = {$wajibpajak_id}
                    AND attendance_status_active = true
                    AND attendance_is_all = true
                    ORDER BY attendance_id ASC
                ) as ptable
            ) as setting_attendance,
            (
                SELECT json_agg(ptable)
                FROM (
                    SELECT st_penggajian_karyawan.*
                    FROM st_penggajian_karyawan
                    WHERE ms_wajibpajak_id = {$wajibpajak_id}
                    AND stpenggajiankaryawan_active = '1'
                    AND stpenggajiankaryawan_is_all = true
                    ORDER BY stpenggajiankaryawan_id ASC
                ) as ptable
            ) as setting_penggajian,
            (
                SELECT row_to_json(ptable)
                FROM (
                    SELECT st_bpjs_karyawan.*
                    FROM st_bpjs_karyawan
                    WHERE ms_wajibpajak_id = {$wajibpajak_id}
                    AND stbpjskaryawan_active = '1'
                    ORDER BY stbpjskaryawan_id ASC
                ) as ptable
            ) as setting_bpjspegawai_json
        "));
    

        $karyawan_infofield = KaryawanInfoFieldModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_user_id' => $user_id])->first();
        $stbpjskaryawan_data = ($setting[0]->setting_bpjspegawai_json) ? json_decode($setting[0]->setting_bpjspegawai_json) : null;
        $data = [
            'title' => 'Karyawan Baru',
            'content' => 'user.master.karyawan.create',
            'sttunjangankaryawan_data' => ($setting[0]->setting_tunjangankaryawan_json) ? json_decode($setting[0]->setting_tunjangankaryawan_json) : null,
            'stpotongankaryawan_data' => ($setting[0]->setting_potongankaryawan_json) ? json_decode($setting[0]->setting_potongankaryawan_json) : null,
            'st_attendance' => ($setting[0]->setting_attendance) ? json_decode($setting[0]->setting_attendance) : null,
            'st_penggajian' => ($setting[0]->setting_penggajian) ? json_decode($setting[0]->setting_penggajian) : null,
            'stbpjskaryawan_data' => ($stbpjskaryawan_data) ? json_decode($stbpjskaryawan_data->stbpjskaryawan_value) : null,
            'karyawan_infofield' => $karyawan_infofield,
        ];

        // dd($data);

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function edit($karyawanId, Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $karyawan = KaryawanModel::
        select("*"
        , DB::raw("(SELECT json_agg(ttable) 
        FROM (
            SELECT std.st_tunjangankaryawan_id, stk.sttunjangankaryawan_name, stk.sttunjangankaryawan_used_for
            FROM st_tunjangan_karyawan_detail as std
            JOIN st_tunjangan_karyawan as stk ON std.st_tunjangankaryawan_id = stk.sttunjangankaryawan_id
            WHERE std.ms_karyawan_id = ms_karyawan.karyawan_id
                AND std.sttunjangankaryawandet_active = '1'
                AND stk.sttunjangankaryawan_active = '1'
        ) as ttable) as tunjangan_json")
        , DB::raw("(SELECT json_agg(ttable) 
        FROM (
            SELECT spd.st_potongankaryawan_id, spk.stpotongankaryawan_name, spk.stpotongankaryawan_used_for
            FROM st_potongan_karyawan_detail as spd
            JOIN st_potongan_karyawan as spk ON spd.st_potongankaryawan_id = spk.stpotongankaryawan_id
            WHERE spd.ms_karyawan_id = ms_karyawan.karyawan_id
                AND spd.stpotongankaryawandet_active = '1'
                AND spk.stpotongankaryawan_active = '1'
        ) as ttable) as potongan_json")
        , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_lock='1' AND payroll_status <= '2' LIMIT 1) payroll_islockunpaid")
        , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_lock='1' AND payroll_status ='3' LIMIT 1) payroll_islockpaid")
        , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_lock='1' AND TO_CHAR(payroll_period, 'YYYY') = TO_CHAR(NOW(), 'YYYY') LIMIT 1) payroll_currentyear_exist")
        )
        ->with(['bank', 'penggajian', 'ptkp', 'manager', 'divisi', 'jabatan', 'attendance'])
        ->where([
            'karyawan_id' => $karyawanId, 
            'karyawan_active' => 1,
            // 'karyawan_contract_end' => null,
            'ms_wajibpajak_id' => $wajibpajak_id,
            // 'ms_user_id' => $user_id,
        ])->first();

        // dd($karyawan);

        if(!$karyawan) {
            abort(404);
        }
        // dd($karyawan->tunjangan_json);
        
        $setting = DB::select(DB::raw("SELECT (SELECT json_agg(ptable) 
        FROM (
            SELECT st_tunjangan_karyawan.*
            FROM st_tunjangan_karyawan
            WHERE ms_wajibpajak_id = {$wajibpajak_id}
            AND sttunjangankaryawan_active = '1'
            ORDER BY sttunjangankaryawan_id ASC
        )
        as ptable) as setting_tunjangankaryawan_json
        , 
        (SELECT json_agg(ptable) 
        FROM (
            SELECT st_potongan_karyawan.*
            FROM st_potongan_karyawan
            WHERE ms_wajibpajak_id = {$wajibpajak_id}
            AND stpotongankaryawan_active = '1'
            ORDER BY stpotongankaryawan_id ASC
        )
        as ptable) as setting_potongankaryawan_json
        ,
        (SELECT row_to_json(ptable) 
        FROM (
            SELECT st_bpjs_karyawan.*
            FROM st_bpjs_karyawan
            WHERE ms_wajibpajak_id = {$wajibpajak_id}
            AND stbpjskaryawan_active = '1'
            ORDER BY stbpjskaryawan_id ASC
        )
        as ptable) as setting_bpjspegawai_json
        "));

        $karyawan_infofield = KaryawanInfoFieldModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_user_id' => $user_id])->first();
        $stbpjskaryawan_data = ($setting[0]->setting_bpjspegawai_json) ? json_decode($setting[0]->setting_bpjspegawai_json) : null;
        $data = [
            'title' => 'Edit Karyawan',
            'content' => 'user.master.karyawan.edit',
            'karyawan' => $karyawan,
            'sttunjangankaryawan_data' => ($setting[0]->setting_tunjangankaryawan_json) ? json_decode($setting[0]->setting_tunjangankaryawan_json) : null,
            'stpotongankaryawan_data' => ($setting[0]->setting_potongankaryawan_json) ? json_decode($setting[0]->setting_potongankaryawan_json) : null,
            'stbpjskaryawan_data' => ($stbpjskaryawan_data) ? json_decode($stbpjskaryawan_data->stbpjskaryawan_value) : null,
            'karyawan_infofield' => $karyawan_infofield,
        ];

        // check if any payroll is lock
        // $payroll = PayrollModel::where(['ms_karyawan_id' => $karyawanId, 'payroll_lock' => 1])->first();
        // $data['payroll'] = $payroll;
        // dd($karyawan->manager);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function store(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $is_user = $request->input('karyawan_isuser');
        $is_manager = 0;
        $manager_id = intval($request->input('karyawan_manager'));
        $email = $request->input('karyawan_email');
        $karyawan_tambahan = $request->input('karyawan_tambahan');
        // dd($request->input('karyawan_tambahan'));
        // echo json_encode($request->input('bpjs_tk'));
        // return json_encode($request->input('bpjs_tk'));
        
        $rules = [
            'karyawan_enid' => ['required', 
                Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id) {
                    return $query->where([
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ]);
                })
            ],
            'karyawan_nik' => ['required', 
                Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id) {
                    return $query->where([
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ]);
                })
            ],
            'karyawan_name' => 'required',
            'karyawan_email' => $email ? 'email' : '',
            'karyawan_citizenship' => 'required|in:WNI,WNA',
            'ptkp_id' => 'required',
            'karyawan_gender' => 'required',
            'karyawan_status' => 'required|in:TETAP,KONTRAK,PERCOBAAN',
            'karyawan_contract_begin' => 'required|date_format:d-m-Y',
            'karyawan_calculation_method' =>  'required|in:GROSS,GROSS_UP,NETT',
            'karyawan_salary' => 'required',
            // 'karyawan_payroll' => 'required',
            'karyawan_npwp' => ['required', 
                Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id) {
                    return $query->where([
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ])
                    ->where('karyawan_npwp', '<>', '00.000.000.0-000.000');;
                })
            ],
            // 'karyawan_bankid' => 'required',
            // 'karyawan_banknokartu' => 'required',
        ];

        if($email) {
            $rules['karyawan_email'] = ['required','email',
                Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id) {
                    return $query->where([
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ]);
                }),
            ];
        }

        // if($request->input('karyawan_npwp')) {
        //     $rules['karyawan_npwp'] = [
        //         Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id) {
        //             return $query->where([
        //                 'ms_wajibpajak_id' => $wajibpajak_id
        //             ]);
        //         })
        //     ];
        // }
        if ($request->hasfile('karyawan_photo')) {
            $rules['karyawan_photo'] = 'mimes:jpeg,jpg,png|max:256';
        }
        if($request->input('karyawan_status') != 'TETAP') {
            $rules['karyawan_contract_end'] = 'required|date_format:d-m-Y';
        }
        if($request->input('karyawan_isgetbpjskes') == 1) {
            $rules['karyawan_bpjskesdate'] = 'required|date_format:d-m-Y';
            $rules['karyawan_bpjskesno'] = 'required|unique:ms_karyawan,karyawan_bpjskesno';
        }
        if($request->input('karyawan_isgetbpjstk') == 1) {
            $rules['karyawan_bpjstkdate'] = 'required|date_format:d-m-Y';
            $rules['karyawan_bpjstkno'] = 'required|unique:ms_karyawan,karyawan_bpjstkno';
        }
        
        $this->validate($request, $rules, [
            'karyawan_enid.required' => 'ID Karyawan wajib diisi!',
            'karyawan_enid.unique' => 'ID Karyawan sudah terdaftar!',
            'karyawan_nik.required' => 'NIK wajib diisi!',
            'karyawan_nik.unique' => 'NIK sudah terdaftar! Silahkan gunakan NIK lainnya.',
            'karyawan_npwp.required' => 'NPWP wajib diisi!',
            'karyawan_npwp.unique' => 'NPWP sudah terdaftar! Silahkan gunakan NPWP lainnya.',
            'karyawan_name.required' => 'Nama wajib diisi!',
            'karyawan_email.required' => 'Email wajib diisi!',
            'karyawan_email.email' => 'Format email tidak sesuai!',
            'karyawan_email.unique' => 'Email sudah digunakan. Silahkan gunakan email lainnya!',
            'karyawan_citizenship.required' => 'Kewarganegaraan wajib diisi!',
            'ptkp_id.required' => 'Status perkawinan wajib diisi!',
            'karyawan_gender.required' => 'Jenis Kelamin wajib diisi!',
            'karyawan_status.required' => 'Status karyawan wajib diisi!',
            'karyawan_contract_begin.required' => 'Tgl. Kontrak wajib diisi!',
            'karyawan_calculation_method.required' => 'Metode perhitungan wajib diisi!',
            'karyawan_salary.required' => 'Gaji pokok wajib diisi!',
            // 'karyawan_payroll.required' => 'Penggajian wajib diisi!',
            // 'karyawan_bankid.required' => 'Nama Bank wajib diisi!',
            // 'karyawan_banknokartu.required' => 'Nomor Kartu Bank wajib diisi!',
            'karyawan_bpjskesno.unique' => 'No. Kartu BPJS Kesehatan sudah digunakan!',
            'karyawan_bpjstkno.unique' => 'No. Kartu BPJS TK sudah digunakan!',
            'karyawan_photo.mimes' => 'Format gambar harus jpg atau png!',
            'karyawan_photo.max' => 'Ukuran gambar maksimal 250 Kb!',
        ]);

        $photo_url = null;
        if ($request->hasfile('karyawan_photo')) {
            $file = $request->file('karyawan_photo');
            // dd($file->extension());
            $appupload_library = new AppUploadLibrary();
            $uploaded_photo = $appupload_library->uploadFile($file, 'images/karyawan/profil');

            if($uploaded_photo['success'] == false) {
                return response()->json([
                    'success' => false,
                    'message' => $uploaded_photo['message']
                ]);
            }
            $photo_url = $uploaded_photo['data']['url'];
        }

        $dt = null;
        if($request->input('karyawan_birthdate')) {
            $dt = \Carbon\Carbon::parse($request->input('karyawan_birthdate'));
        }
        
        $dt_cbegin = \Carbon\Carbon::parse($request->input('karyawan_contract_begin'));
        
        $dt_bpjskes = null;
        $dt_bpjstk = null;
        $dt_pend = null;
        $karyawan_bpjskeslainnya = 0;
        $karyawan_bpjstklainnya = 0;
        $tunjanganbpjs = SettingBpjsKaryawanModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'stbpjskaryawan_active' => 1
        ])->first();
        $stbpjskaryawan_data = ($tunjanganbpjs) ? json_decode($tunjanganbpjs->stbpjskaryawan_value) : [];
        $bpjskes = false;
        $bpjstk = false;
        $stbpjskes_lainnya = false;
        $stbpjstk_lainnya = false;
        foreach ($stbpjskaryawan_data as $stbpjs) {
            if ($stbpjs->type == 'KESEHATAN') {
                $bpjskes = true;
                if($stbpjs->name == 'LAINNYA') {
                    $stbpjskes_lainnya = true;
                }
            }
            if ($stbpjs->type == 'TENAGA_KERJA') {
                $bpjstk = true;
                if($stbpjs->name == 'LAINNYA') {
                    $stbpjstk_lainnya = true;
                }
            }
        }
        if($bpjskes && $request->input('karyawan_isgetbpjskes') == 1) {
            $dt_bpjskes = \Carbon\Carbon::parse($request->input('karyawan_bpjskesdate'));
            if($stbpjskes_lainnya)
                $karyawan_bpjskeslainnya = $request->input('karyawan_bpjskeslainnya');
        }
        if($bpjstk && $request->input('karyawan_isgetbpjstk') == 1) {
            $dt_bpjstk = \Carbon\Carbon::parse($request->input('karyawan_bpjstkdate'));
            if($stbpjstk_lainnya)
                $karyawan_bpjstklainnya = $request->input('karyawan_bpjstklainnya');
        }
        if($request->input('karyawan_status') != 'TETAP') {
            $dt_pend = \Carbon\Carbon::parse($request->input('karyawan_contract_end'));
        }

        $karyawan_calculation_method = $request->input('karyawan_calculation_method');
        $escape_email = DB::connection()->getPdo()->quote($email);

        $wajibpajak = WajibPajakModel::select("*"
        , DB::raw("(SELECT row_to_json(ptable) 
        FROM (
            SELECT ms_karyawan_infofield.*
            FROM ms_karyawan_infofield
            WHERE ms_wajibpajak_id = {$wajibpajak_id}
            ORDER BY karyawaninfofield_id ASC
            LIMIT 1
        )
        as ptable) as karyawaninfofield_json")
        , DB::raw("(SELECT row_to_json(mtable) 
        FROM (
            SELECT manager.karyawan_id, manager.karyawan_ismanager
            FROM ms_karyawan as manager
            WHERE manager.ms_wajibpajak_id = {$wajibpajak_id}
            AND manager.karyawan_id = {$manager_id}
            LIMIT 1
        )
        as mtable) as manager_json")
        )->where(['wajibpajak_id' => $wajibpajak_id])->first();
        
        $total_entity = intval($wajibpajak->total_karyawan);
        // dd($wajibpajak->total_karyawan);

        $karyawan_infofield = ($wajibpajak->karyawaninfofield_json) ? json_decode($wajibpajak->karyawaninfofield_json) : null;
        // dd($karyawan_infofield);
        $tempdata_tambahan = [];
        $karyawan_infokey = [];
        if($karyawan_tambahan) {
            foreach($karyawan_tambahan as $ktb) {
                array_push($karyawan_infokey, $ktb['nama']);
                if(isset($ktb['nama']) && isset($ktb['keterangan'])) {
                    array_push($tempdata_tambahan, $ktb);
                }
            }
        }

        DB::beginTransaction();
        try {
            if(!$karyawan_infofield) { // if there is no info field 
                KaryawanInfoFieldModel::create([
                    'ms_user_id' => $wajibpajak->ms_user_id,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'karyawaninfofield_fields' => ($karyawan_infokey) ? implode(',', $karyawan_infokey) : null
                ]);
            } else {
                KaryawanInfoFieldModel::where([
                    'ms_user_id' => $wajibpajak->ms_user_id,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])->update([
                    'karyawaninfofield_fields' => ($karyawan_infokey) ? implode(',', $karyawan_infokey) : null
                ]);
            }
            $division_id = $request->input('karyawan_division');
            if($division_id) {
                if(!is_numeric($division_id)) {
                    $division = KaryawanDivisiModel::create([
                        'karyawandivisi_name' => $request->input('karyawan_division'),
                        'ms_user_id' => $wajibpajak->ms_user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id,
                    ]);
                    $division_id = $division->karyawandivisi_id;
                }
            }

            $position_id = $request->input('karyawan_position');
            if($position_id) {
                if(!is_numeric($position_id)) {
                    $position = KaryawanJabatanModel::create([
                        'karyawanjabatan_name' => $request->input('karyawan_position'),
                        'ms_user_id' => $wajibpajak->ms_user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id,
                    ]);
                    $position_id = $position->karyawanjabatan_id;
                }
            }
            
            $random_password = null;
            $generate_forgot_password = null;
            if($is_user == '1') {
                $random_password = Hash::make(Str::random(6));
                $generate_forgot_password = Hash::make('FGP'.$email.uniqid());
            }

            $checkPayroll = SettingPenggajianKaryawanModel::where([
                'ms_wajibpajak_id' => $wajibpajak_id,
                'stpenggajiankaryawan_is_all' => true
            ])->first();

            $checkAttendance = SettingAttendanceModel::where([
                'ms_wajibpajak_id' => $wajibpajak_id,
                'attendance_is_all' => true
            ])->first();

            $karyawan = KaryawanModel::create([
                'karyawan_enid' => $request->input('karyawan_enid'),
                'karyawan_nik' => $request->input('karyawan_nik'),
                'karyawan_npwp' => $request->input('karyawan_npwp'),
                'karyawan_name' => $request->input('karyawan_name'),
                'karyawan_birthdate' => ($dt) ? $dt->translatedFormat('Y-m-d') : null,
                'karyawan_birthplace' => $request->input('karyawan_birthplace'),
                'karyawan_contract_begin' => ($dt_cbegin) ? $dt_cbegin->translatedFormat('Y-m-d') : null,
                'karyawan_contract_end' => ($dt_pend) ? $dt_pend->translatedFormat('Y-m-d') : null,
                'karyawan_phone' => $request->input('karyawan_phone'),
                'karyawan_email' => $request->input('karyawan_email'),
                'karyawan_password' => $random_password,
                'karyawan_forgot_password' => $generate_forgot_password,
                'karyawan_citizenship' => $request->input('karyawan_citizenship'),
                'karyawan_gender' => $request->input('karyawan_gender'),
                'karyawan_address' => $request->input('karyawan_address'),
                'karyawan_status' => $request->input('karyawan_status'),
                'karyawan_calculation_method' => $karyawan_calculation_method,
                'karyawan_salary' => ($request->input('karyawan_salary')) ? intval($request->input('karyawan_salary')) : 0,
                'ms_ptkp_id' => $request->input('ptkp_id'),
                'ms_country_id' => ($request->input('karyawan_citizenship') == 'WNI') ? 100 : $request->input('country_id'),
                'ms_bank_id' => $request->input('karyawan_bankid') ? $request->input('karyawan_bankid') : null,
                'ms_wajibpajak_id' => $wajibpajak_id,
                'ms_user_id' => $wajibpajak->ms_user_id,
                'ms_karyawandivisi_id' => $division_id,
                'ms_karyawanjabatan_id' => $position_id,
                'st_attendance_id' => $checkAttendance ? $checkAttendance->attendance_id : null,
                'st_tunjangan_id' => ($request->input('karyawan_allowance')) ? implode(',', $request->input('karyawan_allowance')) : null,
                'st_potongan_id' => ($request->input('karyawan_deduction')) ? implode(',', $request->input('karyawan_deduction')) : null,
                'st_penggajian_id' => $checkPayroll ? $checkPayroll->stpenggajiankaryawan_id : null,
                // 'karyawan_ismanager' => $is_manager,
                'karyawan_isuser' => ($is_user == '1') ? 1 : 0,
                'karyawan_manager_id' => ($manager_id) ? $manager_id : null,
                'karyawan_bankno' => $request->input('karyawan_banknokartu') ? $request->input('karyawan_banknokartu') : null,
                'karyawan_isbpjskes' => $request->input('karyawan_isgetbpjskes'),
                'karyawan_bpjskesdate' => ($dt_bpjskes) ? $dt_bpjskes->translatedFormat('Y-m-d') : null,
                'karyawan_bpjskesno' => $request->input('karyawan_isgetbpjskes') == 1 ? $request->input('karyawan_bpjskesno') : null,
                'karyawan_isbpjstk' => $request->input('karyawan_isgetbpjstk'),
                'karyawan_bpjstkdate' => ($dt_bpjstk) ? $dt_bpjstk->translatedFormat('Y-m-d') : null,
                'karyawan_bpjstkno' => $request->input('karyawan_isgetbpjstk') == 1 ? $request->input('karyawan_bpjstkno') : null,
                'karyawan_bpjskeslainnya' => $karyawan_bpjskeslainnya,
                'karyawan_bpjstklainnya' => $karyawan_bpjstklainnya,
                'karyawan_photo' => $photo_url,
                'karyawan_code_objekpajak' => '21-100-01',
                'karyawan_additionalinfo' => ($tempdata_tambahan) ? json_encode($tempdata_tambahan) : null,
            ]);

            // remove column
            unset($karyawan->karyawan_password);
            unset($karyawan->karyawan_forgot_password);

            // insert masa kerja
            KaryawanMasakerjaModel::create([
                'ms_karyawan_id' => $karyawan->karyawan_id,
                'ms_ptkp_id' => $request->input('ptkp_id'),
                'ms_user_id' => $wajibpajak->ms_user_id,
                'ms_wajibpajak_id' => $wajibpajak_id,
                'karyawanmasakerja_status' => $request->input('karyawan_status'),
                'karyawanmasakerja_calculation_method' => $karyawan_calculation_method,
                'karyawanmasakerja_salary' => ($request->input('karyawan_salary')) ? intval($request->input('karyawan_salary')) : 0,
                'karyawanmasakerja_position' => $request->input('karyawan_position'),
                'karyawanmasakerja_nik' => $request->input('karyawan_nik'),
                'karyawanmasakerja_npwp' => $request->input('karyawan_npwp'),
                'ms_objekpajak_code' => '21-100-01',
                'karyawanmasakerja_contract_begin' => $dt_cbegin->translatedFormat('Y-m-d'),
                'karyawanmasakerja_data' => json_encode($karyawan),
            ]);

            // check if manager already set or not
            if($wajibpajak->manager_json) {
                $manager = json_decode($wajibpajak->manager_json);
                if($manager->karyawan_ismanager != 1) {
                    KaryawanModel::where(['karyawan_id' => $manager->karyawan_id, 'ms_wajibpajak_id' => $wajibpajak_id])
                    ->update(['karyawan_ismanager' => 1]);
                }
            }

            // if($request->input('karyawan_attendance')) {
            //     $checkAttendance = SettingAttendanceModel::where([
            //         'ms_wajibpajak_id' => $wajibpajak_id,
            //         'attendance_is_all' => true
            //     ])
            //     ->where('attendance_id', '<>', $request->input('karyawan_attendance'))->first();

            //     if($checkAttendance) {
            //         $checkAttendance->update([
            //             'attendance_is_all' => false
            //         ]);
            //     }
            // }

            // if($request->input('karyawan_payroll')) {
            //     $checkPayroll = SettingPenggajianKaryawanModel::where([
            //         'ms_wajibpajak_id' => $wajibpajak_id,
            //         'stpenggajiankaryawan_is_all' => true
            //     ])
            //     ->where('stpenggajiankaryawan_id', '<>', $request->input('karyawan_payroll'))->first();

            //     if($checkPayroll) {
            //         $checkPayroll->update([
            //             'stpenggajiankaryawan_is_all' => false
            //         ]);
            //     }
            // }

            if($request->input('karyawan_allowance')) {
                $checkAllowance = SettingTunjanganKaryawanModel::where([
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'sttunjangankaryawan_is_all' => true
                ])->pluck('sttunjangankaryawan_id')->toArray();

                $diffAllowance = array_diff($checkAllowance, $request->input('karyawan_allowance'));
                
                SettingTunjanganKaryawanModel::whereIn('sttunjangankaryawan_id', $diffAllowance)
                ->where([
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])->update([
                   'sttunjangankaryawan_is_all' => false
                ]);

                $bulkAllowance = [];
                foreach ($request->input('karyawan_allowance') as $st_tunjangankaryawan_id) {
                    $bulkAllowance[] = [
                        'st_tunjangankaryawan_id' => $st_tunjangankaryawan_id,
                        'ms_karyawan_id' => $karyawan->karyawan_id,
                        'sttunjangankaryawandet_active' => '1'
                    ];
                }

                SettingTunjanganKaryawanDetailModel::insert($bulkAllowance);
            }

            if($request->input('karyawan_deduction')) {
                $checkDeduction = SettingPotonganKaryawanModel::where([
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'stpotongankaryawan_is_all' => true
                ])->pluck('stpotongankaryawan_id')->toArray();

                $diffDeduction = array_diff($checkDeduction, $request->input('karyawan_deduction'));
                
                SettingPotonganKaryawanModel::whereIn('stpotongankaryawan_id', $diffDeduction)
                ->where([
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])->update([
                   'stpotongankaryawan_is_all' => false
                ]);

                $bulkDeduction = [];
                foreach ($request->input('karyawan_deduction') as $st_potongankaryawan_id) {
                    $bulkDeduction[] = [
                        'st_potongankaryawan_id' => $st_potongankaryawan_id,
                        'ms_karyawan_id' => $karyawan->karyawan_id,
                        'stpotongankaryawandet_active' => '1'
                    ];
                }

                SettingPotonganKaryawanDetailModel::insert($bulkDeduction);
            }
            
            // processed the leave setting
            $stleave = SettingLeaveModel::where([
                'leave_status_active' => true,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])
            ->whereIn('leave_used_for', [1,2,3,4,5])->get();

            if($stleave) {
                $tempdata_leave = $this->stleave_tempdata($stleave, [
                    [
                        'karyawan_id' => $karyawan->karyawan_id,
                        'karyawan_gender' => $request->input('karyawan_gender'),
                        'ms_karyawandivisi_id' => $division_id,
                        'ms_karyawanjabatan_id' => $position_id,
                    ]
                ]);

                if($tempdata_leave) {
                    SettingLeaveDetailModel::insert($tempdata_leave);
                }
            }

            if($is_user) {
                // insert tr_notification
                $notification = NotificationModel::create([
                    'notification_title' => 'Rekkaa - Undangan Dari '.$wajibpajak->wajibpajak_name,
                    'notification_type' => 'EMAIL',
                    'notification_from' => env("MAIL_FROM_ADDRESS"),
                    'notification_to' => $email,
                    'ms_user_id' => $wajibpajak->ms_user_id,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'notification_view' => 'email.email-undangan-akun-karyawan',
                    'notification_data' => json_encode([
                        'entity' => [
                            'name' => $wajibpajak->wajibpajak_name,
                            'user_name' => $request->input('karyawan_name'),
                            'inviter_email' => session()->get('user_data')['user_email'],
                            'forgot_token' => $generate_forgot_password,
                            'total_entity' => $total_entity,
                        ]
                    ])
                ]);

                dispatch(new SendMailJob($notification->notification_id));
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil disimpan',
                'data' => $karyawan
            ]);
            

        } catch(Error $e) {
            // delete file if already upload
            if(isset($uploaded_photo) && $uploaded_photo['success']) {
                if($uploaded_photo['data']) {
                    $appupload_library->deleteFile('images/karyawan/profil/'.$uploaded_photo['data']['filename']);
                }
            }
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [
                    'uploaded_photo' => isset($uploaded_photo) ? $uploaded_photo : null
                ]
            ]);
        } catch(Exception $e) {
            // delete file if already upload
            if(isset($uploaded_photo) && $uploaded_photo['success']) {
                if($uploaded_photo['data']) {
                    $appupload_library->deleteFile('images/karyawan/profil/'.$uploaded_photo['data']['filename']);
                }
            }
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [
                    'uploaded_photo' => isset($uploaded_photo) ? $uploaded_photo : null
                ]
            ]);
        }
    }

    public function update($karyawan_id, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $is_user = $request->input('karyawan_isuser');
        $is_manager = 0;
        $manager_id = intval($request->input('karyawan_manager'));
        $removemanager_id = 0;
        $email = $request->input('karyawan_email');
        $karyawan_tambahan = $request->input('karyawan_tambahan');
        $user_id = session()->get('user_data')['user_id'];
        $dt_cbegin = \Carbon\Carbon::parse($request->input('karyawan_contract_begin'));
        // dd($request->input());

        $karyawan = KaryawanModel::select("*"
        , DB::raw("(SELECT COUNT(currkaryawan.*) FROM ms_karyawan as currkaryawan 
        WHERE currkaryawan.karyawan_email = ms_karyawan.karyawan_email
        AND currkaryawan.karyawan_email IS NOT NULL) as total_karyawan")
        , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_lock='1' AND payroll_status='3' LIMIT 1) payroll_islockpaid")
        , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_lock='1' AND TO_CHAR(payroll_period, 'YYYY') = TO_CHAR(NOW(), 'YYYY') LIMIT 1) payroll_currentyear_exist")
        )->where([
            'karyawan_id' => $karyawan_id, 
            'karyawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        
        if(!$karyawan) {
            abort(404);
        }
        if($karyawan->karyawan_manager_id != $manager_id) {
            $removemanager_id = intval($karyawan->karyawan_manager_id);
        }
        // dd($removemanager_id);
        // dd($karyawan->total_karyawan);
        $rules = [
            'karyawan_enid' => ['required', 
                Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id, $karyawan_id, $user_id) {
                    return $query->where([
                        'ms_user_id' => $user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ])
                    ->where('karyawan_id', '<>', $karyawan_id);
                })
            ],
            'karyawan_nik' => [
                'required',
                Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id, $karyawan_id, $user_id) {
                    return $query->where([
                        'ms_user_id' => $user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ])
                    ->where('karyawan_id', '<>', $karyawan_id);
                })
            ],
            'karyawan_name' => 'required',
            'karyawan_email' => $email ? 'email' : '',
            'karyawan_citizenship' => 'required|in:WNI,WNA',
            'ptkp_id' => 'required',
            'karyawan_gender' => 'required',
            'karyawan_status' => 'required|in:TETAP,KONTRAK,PERCOBAAN',
            'karyawan_contract_begin' => 'required|date_format:d-m-Y',
            'karyawan_calculation_method' =>  'required|in:GROSS,GROSS_UP,NETT',
            'karyawan_salary' => 'required',
            // 'karyawan_payroll' => 'required',
            'karyawan_npwp' => ['required', 
                Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id, $karyawan_id, $user_id) {
                    return $query->where([
                        'ms_user_id' => $user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ])
                    ->where('karyawan_id', '<>', $karyawan_id)
                    ->where('karyawan_npwp', '<>', '00.000.000.0-000.000');
                })
            ],
            // 'karyawan_bankid' => 'required',
            // 'karyawan_banknokartu' => 'required',
        ];
        
        if($email) {
            $rules['karyawan_email'] = ['required','email',
                Rule::unique('ms_karyawan')->where(function ($query) use($wajibpajak_id, $karyawan_id, $user_id) {
                    return $query->where([
                        'ms_user_id' => $user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ])->where('karyawan_id', '<>', $karyawan_id);
                }),
            ];
        }

        // if($request->input('karyawan_npwp')) {
        //     $rules['karyawan_npwp'] = [
        //         Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id, $karyawan_id, $user_id) {
        //             return $query->where([
        //                 'ms_user_id' => $user_id,
        //                 'ms_wajibpajak_id' => $wajibpajak_id
        //             ])->where('karyawan_id', '<>', $karyawan_id);
        //         })
        //     ];
        // }
        if ($request->hasfile('karyawan_photo')) {
            $rules['karyawan_photo'] = 'mimes:jpeg,jpg,png|max:256';
        }
        if ($request->input('karyawan_status') == 'PERCOBAAN' || $request->input('karyawan_status') == 'KONTRAK') {
            $rules['karyawan_contract_end'] = 'required|date_format:d-m-Y';
        }
        if($request->input('karyawan_isgetbpjskes') == 1) {
            $rules['karyawan_bpjskesdate'] = 'required|date_format:d-m-Y';
            // $rules['karyawan_bpjskesno'] = 'required|unique:ms_karyawan,karyawan_bpjskesno,'.$karyawan_id.',karyawan_id';
            $rules['karyawan_bpjskesno'] = ['required',
                Rule::unique('ms_karyawan')->where(function ($query) use($wajibpajak_id, $karyawan_id, $user_id) {
                    return $query->where([
                        'ms_user_id' => $user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ])->where('karyawan_id', '<>', $karyawan_id);
                }),
            ];
        }
        if($request->input('karyawan_isgetbpjstk') == 1) {
            $rules['karyawan_bpjstkdate'] = 'required|date_format:d-m-Y';
            // $rules['karyawan_bpjstkno'] = 'required|unique:ms_karyawan,karyawan_bpjstkno,'.$karyawan_id.',karyawan_id';
            $rules['karyawan_bpjstkno'] = ['required',
                Rule::unique('ms_karyawan')->where(function ($query) use($wajibpajak_id, $karyawan_id, $user_id) {
                    return $query->where([
                        'ms_user_id' => $user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ])->where('karyawan_id', '<>', $karyawan_id);
                }),
            ];
        }
        // dd($rules);

        // check tgl masuk is different
        $deleted_prev_payroll = false;
        if($request->input('karyawan_contract_begin') && $karyawan->karyawan_contract_begin != $dt_cbegin->translatedFormat('Y-m-d')) {
            if($karyawan->payroll_islockpaid > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal kontrak tidak bisa dirubah. Sudah ada transaksi penggajian!'
                ]);
            }
            $deleted_prev_payroll = true;
        } else {
            $rules['karyawan_contract_begin'] = '';
            $request->request->add(['karyawan_contract_begin' => $karyawan->karyawan_contract_begin]);
        }
        // check if ptkp is different
        if($request->input('ptkp_id') && $karyawan->ms_ptkp_id != $request->input('ptkp_id')) {
            if($karyawan->payroll_currentyear_exist > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'PTKP tidak bisa dirubah. Sudah ada transaksi penggajian periode ini!'
                ]);
            }
        } else {
            $rules['ptkp_id'] = '';
            $request->request->add(['ptkp_id' => $karyawan->ms_ptkp_id]);
        }
        // check if calculation method is different
        // if($request->input('karyawan_calculation_method') && $karyawan->karyawan_calculation_method != $request->input('karyawan_calculation_method')) {
        //     if($karyawan->payroll_currentyear_exist > 0) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Metode PPh21 tidak bisa dirubah. Sudah ada transaksi penggajian periode ini!'
        //         ]);
        //     }
        // } else {
        //     $rules['karyawan_calculation_method'] = '';
        //     $request->request->add(['karyawan_calculation_method' => $karyawan->karyawan_calculation_method]);
        // }
        $this->validate($request, $rules, [
            'karyawan_nik.required' => 'NIK wajib diisi!',
            'karyawan_nik.unique' => 'NIK sudah terdaftar! Silahkan gunakan NIK lainnya.',
            'karyawan_npwp.unique' => 'NPWP sudah terdaftar! Silahkan gunakan NPWP lainnya.',
            'karyawan_name.required' => 'Nama wajib diisi!',
            'karyawan_email.required' => 'Email wajib diisi!',
            'karyawan_email.email' => 'Format email tidak sesuai!',
            'karyawan_email.unique' => 'Email sudah digunakan. Silahkan gunakan email lainnya!',
            'karyawan_citizenship.required' => 'Kewarganegaraan wajib diisi!',
            'ptkp_id.required' => 'Status perkawinan wajib diisi!',
            'karyawan_gender.required' => 'Jenis Kelamin wajib diisi!',
            'karyawan_status.required' => 'Status karyawan wajib diisi!',
            'karyawan_contract_begin.required' => 'Tgl. Kontrak wajib diisi!',
            'karyawan_calculation_method.required' => 'Metode perhitungan wajib diisi!',
            'karyawan_salary.required' => 'Gaji pokok wajib diisi!',
            // 'karyawan_payroll.required' => 'Penggajian wajib diisi!',
            // 'karyawan_bankid.required' => 'Nama Bank wajib diisi!',
            // 'karyawan_banknokartu.required' => 'Nomor Kartu Bank wajib diisi!',
            'karyawan_bpjskesno.unique' => 'No. Kartu BPJS Kesehatan sudah digunakan!',
            'karyawan_bpjstkno.unique' => 'No. Kartu BPJS TK sudah digunakan!',
            'karyawan_photo.mimes' => 'Format gambar harus jpg atau png!',
            'karyawan_photo.max' => 'Ukuran gambar maksimal 250 Kb!',
        ]);

        $old_photo = $karyawan->karyawan_photo;
        $old_isuser = $karyawan->karyawan_isuser;
        $old_status = $karyawan->karyawan_status;
        $old_email = $karyawan->karyawan_email;
        $photo_url = null;
        if ($request->hasfile('karyawan_photo')) {
            $file = $request->file('karyawan_photo');
            // dd($file->extension());
            $appupload_library = new AppUploadLibrary();
            $uploaded_photo = $appupload_library->uploadFile($file, 'images/karyawan/profil');

            if($uploaded_photo['success'] == false) {
                return response()->json([
                    'success' => false,
                    'message' => $uploaded_photo['message']
                ]);
            }
            $photo_url = $uploaded_photo['data']['url'];
        }

        $dt = null;
        if($request->input('karyawan_birthdate')) {
            $dt = \Carbon\Carbon::parse($request->input('karyawan_birthdate'));
        }
        
        
        $dt_bpjskes = null;
        $dt_bpjstk = null;
        $dt_pend = null;
        $karyawan_bpjskeslainnya = 0;
        $karyawan_bpjstklainnya = 0;
        $tunjanganbpjs = SettingBpjsKaryawanModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'stbpjskaryawan_active' => 1
        ])->first();
        $stbpjskaryawan_data = ($tunjanganbpjs) ? json_decode($tunjanganbpjs->stbpjskaryawan_value) : [];
        $bpjskes = false;
        $bpjstk = false;
        $stbpjskes_lainnya = false;
        $stbpjstk_lainnya = false;
        foreach ($stbpjskaryawan_data as $stbpjs) {
            if ($stbpjs->type == 'KESEHATAN') {
                $bpjskes = true;
                if($stbpjs->name == 'LAINNYA') {
                    $stbpjskes_lainnya = true;
                }
            }
            if ($stbpjs->type == 'TENAGA_KERJA') {
                $bpjstk = true;
                if($stbpjs->name == 'LAINNYA') {
                    $stbpjstk_lainnya = true;
                }
            }
        }

        if($bpjskes && $request->input('karyawan_isgetbpjskes') == 1) {
            $dt_bpjskes = \Carbon\Carbon::parse($request->input('karyawan_bpjskesdate'));
            if($stbpjskes_lainnya)
                $karyawan_bpjskeslainnya = $request->input('karyawan_bpjskeslainnya');
        }
        if($bpjstk && $request->input('karyawan_isgetbpjstk')) {
            $dt_bpjstk = \Carbon\Carbon::parse($request->input('karyawan_bpjstkdate'));
            if($stbpjstk_lainnya)
                $karyawan_bpjstklainnya = $request->input('karyawan_bpjstklainnya');
        }
        if($request->input('karyawan_status') != 'TETAP') {
            $dt_pend = \Carbon\Carbon::parse($request->input('karyawan_contract_end'));
        }

        $karyawan_calculation_method = $request->input('karyawan_calculation_method');

        $escape_email = DB::connection()->getPdo()->quote($email);
        $wajibpajak = WajibPajakModel::select("*"
        , DB::raw("(SELECT COUNT(currkaryawan.*) FROM ms_karyawan as currkaryawan 
        WHERE currkaryawan.karyawan_email = {$escape_email}
        AND currkaryawan.karyawan_email IS NOT NULL LIMIT 1) as total_karyawan")
        , DB::raw("(SELECT row_to_json(mtable) 
        FROM (
            SELECT manager.karyawan_id, manager.karyawan_ismanager
            FROM ms_karyawan as manager
            WHERE manager.ms_wajibpajak_id = {$wajibpajak_id}
            AND manager.karyawan_id = {$manager_id}
            LIMIT 1
        )
        as mtable) as manager_json")
        , DB::raw("(SELECT COUNT(subkaryawan.*) FROM ms_karyawan as subkaryawan 
        WHERE subkaryawan.ms_wajibpajak_id = {$wajibpajak_id}
        AND subkaryawan.karyawan_manager_id = {$removemanager_id}
        LIMIT 1) as total_subkaryawan")
        )
        ->where(['wajibpajak_id' => $wajibpajak_id])->first();

        // dd($wajibpajak->total_subkaryawan);

        $total_entity = ($karyawan->karyawan_email == $email && intval($wajibpajak->total_karyawan) == 1) ? 0 : intval($wajibpajak->total_karyawan);
        // $karyawan_infofield = ($wajibpajak->karyawan_infofield) ? json_decode($wajibpajak->karyawan_infofield) : null;
        
        $tempdata_tambahan = [];
        $karyawan_infokey = [];
        if($karyawan_tambahan) {
            foreach($karyawan_tambahan as $ktb) {
                array_push($karyawan_infokey, $ktb['nama']);
                if(isset($ktb['nama']) && isset($ktb['keterangan'])) {
                    array_push($tempdata_tambahan, $ktb);
                }
            }
        }

        DB::beginTransaction();
        try {
            KaryawanInfoFieldModel::where([
                'ms_user_id' => $wajibpajak->ms_user_id,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])->update([
                'karyawaninfofield_fields' => ($karyawan_infokey) ? implode(',', $karyawan_infokey) : null
            ]);

            $division_id = $request->input('karyawan_division');
            if($division_id) {
                if(!is_numeric($division_id)) {
                    $division = KaryawanDivisiModel::create([
                        'karyawandivisi_name' => $request->input('karyawan_division'),
                        'ms_user_id' => $wajibpajak->ms_user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id,
                    ]);
                    $division_id = $division->karyawandivisi_id;
                }
            }

            $position_id = $request->input('karyawan_position');
            if($position_id) {
                if(!is_numeric($position_id)) {
                    $position = KaryawanJabatanModel::create([
                        'karyawanjabatan_name' => $request->input('karyawan_position'),
                        'ms_user_id' => $wajibpajak->ms_user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id,
                    ]);
                    $position_id = $position->karyawanjabatan_id;
                }
            }

            $save_data_karyawan = [
                'karyawan_enid' => $request->input('karyawan_enid'),
                'karyawan_nik' => $request->input('karyawan_nik'),
                'karyawan_npwp' => $request->input('karyawan_npwp'),
                'karyawan_name' => $request->input('karyawan_name'),
                'karyawan_birthdate' => ($dt) ? $dt->translatedFormat('Y-m-d') : null,
                'karyawan_birthplace' => $request->input('karyawan_birthplace'),
                'karyawan_contract_begin' => ($dt_cbegin) ? $dt_cbegin->translatedFormat('Y-m-d') : null,
                'karyawan_contract_end' => ($dt_pend) ? $dt_pend->translatedFormat('Y-m-d') : null,
                'karyawan_phone' => $request->input('karyawan_phone'),
                'karyawan_email' => $request->input('karyawan_email'),
                'karyawan_citizenship' => $request->input('karyawan_citizenship'),
                'karyawan_gender' => $request->input('karyawan_gender'),
                'karyawan_address' => $request->input('karyawan_address'),
                'karyawan_status' => $request->input('karyawan_status'),
                'karyawan_calculation_method' => $karyawan_calculation_method,
                'karyawan_salary' => ($request->input('karyawan_salary')) ? intval($request->input('karyawan_salary')) : 0,
                'ms_ptkp_id' => $request->input('ptkp_id'),
                'ms_country_id' => ($request->input('karyawan_citizenship') == 'WNI') ? 100 : $request->input('country_id'),
                'ms_bank_id' => $request->input('karyawan_bankid'),
                'ms_karyawandivisi_id' => $division_id,
                'ms_karyawanjabatan_id' => $position_id,
                // 'st_attendance_id' => $request->input('karyawan_attendance'),
                'st_tunjangan_id' => ($request->input('karyawan_allowance')) ? implode(',', $request->input('karyawan_allowance')) : null,
                'st_potongan_id' => ($request->input('karyawan_deduction')) ? implode(',', $request->input('karyawan_deduction')) : null,
                // 'st_penggajian_id' => $request->input('karyawan_payroll'),
                // 'karyawan_ismanager' => $is_manager,
                'karyawan_isuser' => ($is_user == '1') ? 1 : 0,
                'karyawan_manager_id' => ($manager_id) ? $manager_id : 0,
                'karyawan_bankno' => $request->input('karyawan_banknokartu'),
                'karyawan_isbpjskes' => $request->input('karyawan_isgetbpjskes'),
                'karyawan_bpjskesdate' => ($dt_bpjskes) ? $dt_bpjskes->translatedFormat('Y-m-d') : null,
                'karyawan_bpjskesno' => $request->input('karyawan_isgetbpjskes') == 1 ? $request->input('karyawan_bpjskesno') : null,
                'karyawan_isbpjstk' => $request->input('karyawan_isgetbpjstk'),
                'karyawan_bpjstkdate' => ($dt_bpjstk) ? $dt_bpjstk->translatedFormat('Y-m-d') : null,
                'karyawan_bpjstkno' => $request->input('karyawan_isgetbpjstk') == 1 ? $request->input('karyawan_bpjstkno') : null,
                'karyawan_bpjskeslainnya' => $karyawan_bpjskeslainnya,
                'karyawan_bpjstklainnya' => $karyawan_bpjstklainnya,
                // 'karyawan_photo' => $photo_url,
                'karyawan_additionalinfo' => ($tempdata_tambahan) ? json_encode($tempdata_tambahan) : null,
            ];
            // dd($save_data_karyawan);
            
            if($photo_url) {
                $save_data_karyawan['karyawan_photo'] = $photo_url;
            }
            
            if($is_user == '1') {
                if($old_email != $email || $old_isuser == '0') {
                    $save_data_karyawan['karyawan_password'] = Hash::make(Str::random(6));
                    $save_data_karyawan['karyawan_forgot_password'] = Hash::make('FGP'.$email.uniqid());
                }
            }
            $karyawan->update($save_data_karyawan);

            // remove column
            unset($karyawan->karyawan_password);
            unset($karyawan->karyawan_forgot_password);
             
            // update tr_karyawan_masakerja if status not different
            if($request->input('karyawan_status') == $old_status) {
                // update masa kerja
                KaryawanMasakerjaModel::where([
                    'ms_karyawan_id' => $karyawan_id,
                    'ms_user_id' => $wajibpajak->ms_user_id,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'karyawanmasakerja_active' => 1,
                ])->update([
                    'ms_ptkp_id' => $request->input('ptkp_id'),
                    'karyawanmasakerja_calculation_method' => $karyawan_calculation_method,
                    'karyawanmasakerja_salary' => ($request->input('karyawan_salary')) ? intval($request->input('karyawan_salary')) : 0,
                    'karyawanmasakerja_position' => $request->input('karyawan_position'),
                    'karyawanmasakerja_nik' => $request->input('karyawan_nik'),
                    'karyawanmasakerja_npwp' => $request->input('karyawan_npwp'),
                    'ms_objekpajak_code' => $karyawan->karyawan_code_objekpajak,
                    'karyawanmasakerja_contract_begin' => $dt_cbegin->translatedFormat('Y-m-d'),
                    'karyawanmasakerja_data' => json_encode($karyawan),
                ]);
            } else {
                // insert masa kerja
                KaryawanMasakerjaModel::create([
                    'ms_karyawan_id' => $karyawan->karyawan_id,
                    'ms_ptkp_id' => $request->input('ptkp_id'),
                    'ms_user_id' => $wajibpajak->ms_user_id,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'karyawanmasakerja_status' => $request->input('karyawan_status'),
                    'karyawanmasakerja_calculation_method' => $karyawan_calculation_method,
                    'karyawanmasakerja_salary' => ($request->input('karyawan_salary')) ? intval($request->input('karyawan_salary')) : 0,
                    'karyawanmasakerja_position' => $request->input('karyawan_position'),
                    'karyawanmasakerja_nik' => $request->input('karyawan_nik'),
                    'karyawanmasakerja_npwp' => $request->input('karyawan_npwp'),
                    'ms_objekpajak_code' => $karyawan->karyawan_code_objekpajak,
                    'karyawanmasakerja_contract_begin' => $dt_cbegin->translatedFormat('Y-m-d'),
                    'karyawanmasakerja_data' => json_encode($karyawan),
                ]);
            }

            // check if manager already set or not
            if($wajibpajak->manager_json) {
                $manager = json_decode($wajibpajak->manager_json);
                if($manager->karyawan_ismanager != 1) {
                    KaryawanModel::where(['karyawan_id' => $manager->karyawan_id, 'ms_wajibpajak_id' => $wajibpajak_id])
                    ->update(['karyawan_ismanager' => 1]);
                }
            }
            // check if manager has subordinate or not
            if($removemanager_id) {
                if($wajibpajak->total_subkaryawan <= 1) {
                    KaryawanModel::where(['karyawan_id' => $removemanager_id, 'ms_wajibpajak_id' => $wajibpajak_id])
                    ->update(['karyawan_ismanager' => 0]);
                }
            }

            // if($request->input('karyawan_attendance')) {
            //     $checkAttendance = SettingAttendanceModel::where([
            //         'ms_wajibpajak_id' => $wajibpajak_id,
            //         'attendance_is_all' => true
            //     ])
            //     ->where('attendance_id', '<>', $request->input('karyawan_attendance'))->first();

            //     if($checkAttendance) {
            //         $checkAttendance->update([
            //             'attendance_is_all' => false
            //         ]);
            //     }
            // }

            // if($request->input('karyawan_payroll')) {
            //     $checkPayroll = SettingPenggajianKaryawanModel::where([
            //         'ms_wajibpajak_id' => $wajibpajak_id,
            //         'stpenggajiankaryawan_is_all' => true
            //     ])
            //     ->where('stpenggajiankaryawan_id', '<>', $request->input('karyawan_payroll'))->first();

            //     if($checkPayroll) {
            //         $checkPayroll->update([
            //             'stpenggajiankaryawan_is_all' => false
            //         ]);
            //     }
            // }

            if($request->input('karyawan_allowance')) {
                $checkAllowance = SettingTunjanganKaryawanModel::where([
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'sttunjangankaryawan_is_all' => true
                ])->pluck('sttunjangankaryawan_id')->toArray();

                $diffAllowance = array_diff($checkAllowance, $request->input('karyawan_allowance'));
                
                SettingTunjanganKaryawanModel::whereIn('sttunjangankaryawan_id', $diffAllowance)
                ->where([
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])
                ->update([
                   'sttunjangankaryawan_is_all' => false
                ]);

                $checkDetailAllowance = SettingTunjanganKaryawanDetailModel::where([
                    'ms_karyawan_id' => $karyawan->karyawan_id,
                ])->pluck('st_tunjangankaryawan_id')->toArray();
                
                $diffDetailAllowance = array_diff($checkDetailAllowance, $request->input('karyawan_allowance'));
                
                SettingTunjanganKaryawanDetailModel::whereIn('st_tunjangankaryawan_id', $diffDetailAllowance)
                ->where([
                    'ms_karyawan_id' => $karyawan->karyawan_id,
                ])
                ->update([
                    'sttunjangankaryawandet_active' => '0'
                ]);

                $bulkAllowance = [];

                foreach ($request->input('karyawan_allowance') as $st_tunjangankaryawan_id) {
                    $bulkAllowance[] = [
                        'st_tunjangankaryawan_id' => $st_tunjangankaryawan_id,
                        'ms_karyawan_id' => $karyawan->karyawan_id,
                        'sttunjangankaryawandet_active' => '1'
                    ];
                }
                
                // Loop through each record and perform update or insert
                foreach ($bulkAllowance as $allowance) {
                    SettingTunjanganKaryawanDetailModel::updateOrInsert(
                        [
                            'st_tunjangankaryawan_id' => $allowance['st_tunjangankaryawan_id'],
                            'ms_karyawan_id' => $allowance['ms_karyawan_id'],
                        ],
                        $allowance
                    );
                }
            }

            if($request->input('karyawan_deduction')) {
                $checkDeduction = SettingPotonganKaryawanModel::where([
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'stpotongankaryawan_is_all' => true
                ])->pluck('stpotongankaryawan_id')->toArray();

                $diffDeduction = array_diff($checkDeduction, $request->input('karyawan_deduction'));
                
                SettingPotonganKaryawanModel::whereIn('stpotongankaryawan_id', $diffDeduction)
                ->where([
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])
                ->update([
                   'stpotongankaryawan_is_all' => false
                ]);

                $checkDetailDeduction = SettingPotonganKaryawanDetailModel::where([
                    'ms_karyawan_id' => $karyawan->karyawan_id
                ])->pluck('st_potongankaryawan_id')->toArray();
                
                $diffDetailDeduction = array_diff($checkDetailDeduction, $request->input('karyawan_deduction'));
                
                SettingPotonganKaryawanDetailModel::whereIn('st_potongankaryawan_id', $diffDetailDeduction)
                ->where([
                    'ms_karyawan_id' => $karyawan->karyawan_id,
                ])
                ->update([
                    'stpotongankaryawandet_active' => '0'
                ]);

                $bulkDeduction = [];

                foreach ($request->input('karyawan_deduction') as $st_potongankaryawan_id) {
                    $bulkDeduction[] = [
                        'st_potongankaryawan_id' => $st_potongankaryawan_id,
                        'ms_karyawan_id' => $karyawan->karyawan_id,
                        'stpotongankaryawandet_active' => '1'
                    ];
                }

                 // Loop through each record and perform update or insert
                foreach ($bulkDeduction as $deduction) {
                    SettingPotonganKaryawanDetailModel::updateOrInsert(
                        [
                            'st_potongankaryawan_id' => $deduction['st_potongankaryawan_id'],
                            'ms_karyawan_id' => $deduction['ms_karyawan_id'],
                        ],
                        $deduction
                    );
                }
            }

            // processed the leave setting not all employees
            $stleave = SettingLeaveModel::where([
                'leave_status_active' => true,
                'ms_wajibpajak_id' => $wajibpajak_id,
            ])
            ->whereIn('leave_used_for', [1,2,3,4,5])->get();
            if($stleave) {
                $tempdata_leave = $this->stleave_tempdata($stleave, [
                    [
                        'karyawan_id' => $karyawan->karyawan_id,
                        'karyawan_gender' => $save_data_karyawan['karyawan_gender'],
                        'ms_karyawandivisi_id' => $save_data_karyawan['ms_karyawandivisi_id'],
                        'ms_karyawanjabatan_id' => $save_data_karyawan['ms_karyawanjabatan_id'],
                    ]
                ]);
                // check detail leave
                $stleavedetail = SettingLeaveDetailModel::where([
                    'ms_karyawan_id' => $karyawan->karyawan_id,
                ])->get();
                
                $stleaveids = [];
                foreach($tempdata_leave as $tdleave) {
                    array_push($stleaveids, $tdleave['st_leave_id']);
                    SettingLeaveDetailModel::updateOrInsert(
                        [
                            'st_leave_id' => $tdleave['st_leave_id'],
                            'ms_karyawan_id' => $tdleave['ms_karyawan_id'],
                        ],
                        $tdleave
                    );
                }
                $deletedstleaveids = []; 
                foreach($stleavedetail as $lvdet) {
                    if(!in_array($lvdet->st_leave_id, $stleaveids)) {
                        array_push($deletedstleaveids, $lvdet->st_leave_id);
                    }
                }
                if($deletedstleaveids) {
                    SettingLeaveDetailModel::whereIn('st_leave_id', $deletedstleaveids)
                    ->where(['ms_karyawan_id' => $karyawan->karyawan_id])
                    ->update(['leavedetail_active' => false]);
                }
            }

            if($is_user == '1') {
                if($old_email != $email || $old_isuser == '0') {
                    // insert tr_notification
                    $notification = NotificationModel::create([
                        'notification_title' => 'Rekkaa - Undangan Dari '.$wajibpajak->wajibpajak_name,
                        'notification_type' => 'EMAIL',
                        'notification_from' => env("MAIL_FROM_ADDRESS"),
                        'notification_to' => $email,
                        'ms_user_id' => $wajibpajak->ms_user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id,
                        'notification_view' => 'email.email-undangan-akun-karyawan',
                        'notification_data' => json_encode([
                            'entity' => [
                                'name' => $wajibpajak->wajibpajak_name,
                                'user_name' => $request->input('karyawan_name'),
                                'inviter_email' => session()->get('user_data')['user_email'],
                                'forgot_token' => $save_data_karyawan['karyawan_forgot_password'],
                                'total_entity' => $total_entity,
                            ]
                        ])
                    ]);

                    dispatch(new SendMailJob($notification->notification_id));
                }
            }

            if($deleted_prev_payroll) {
                $payroll_dtparse = Carbon::parse($save_data_karyawan['karyawan_contract_begin']);
                $payrolls = PayrollModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_karyawan_id' => $karyawan_id
                // , 'payroll_lock' => 0
                ])
                ->where('payroll_status', '<=', 2)
                ->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') < ?)", [$payroll_dtparse->format('Y-m')])->get();
                // dd($payrolls);
                $payroll_uuids = [];
                foreach($payrolls as $py) {
                    array_push($payroll_uuids, $py['payroll_uuid']);
                }
                if(count($payroll_uuids) > 0) {
                    PPh21Model::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_karyawan_id' => $karyawan_id])
                    ->whereRaw("(TO_CHAR(pph21_period, 'YYYY-MM') < ?)", [$payroll_dtparse->format('Y-m')])
                    ->whereIn('tr_payroll_uuid', $payroll_uuids)->delete();

                    PayrollModel::whereIn('payroll_uuid', $payroll_uuids)->delete();
                }
            }

            DB::commit();

            // delete old photo if exist
            if($photo_url && $old_photo) {
                $appupload_library->deleteFile($old_photo, true);
            }
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil diperbarui',
                'data' => $karyawan
            ]);
            

        } catch(Error $e) {
            // delete file if already upload
            if(isset($uploaded_photo) && $uploaded_photo['success']) {
                if($uploaded_photo['data']) {
                    $appupload_library->deleteFile('images/karyawan/profil/'.$uploaded_photo['data']['filename']);
                }
            }
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [
                    'uploaded_photo' => isset($uploaded_photo) ? $uploaded_photo : null
                ]
            ]);
        } catch(Exception $e) {
            // delete file if already upload
            if(isset($uploaded_photo) && $uploaded_photo['success']) {
                if($uploaded_photo['data']) {
                    $appupload_library->deleteFile('images/karyawan/profil/'.$uploaded_photo['data']['filename']);
                }
            }
            
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => [
                    'uploaded_photo' => isset($uploaded_photo) ? $uploaded_photo : null
                ]
            ]);
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
        $order_columns = ['karyawan_id','karyawan_nik','karyawan_npwp','karyawan_name','karyawan_address','karyawan_phone','karyawan_status'];
        $order_col = $order_columns[0];//(isset($request->input('order')[0]) && isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : null;
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }
        
        $where = [
            'karyawan_active' => 1,
            // 'karyawan_contract_end' => null,
            // 'karyawan_contract_now' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];
        $list = KaryawanModel::select('ms_karyawan.*')
        ->when($search, function($q, $search) {
            return $q->where(DB::raw('LOWER(karyawan_nik)'), 'like', '%'.strtolower($search).'%')
            ->orWhere(DB::raw('LOWER(karyawan_npwp)'), 'like', '%'.strtolower($search).'%')
            ->orWhere(DB::raw('LOWER(karyawan_name)'), 'like', '%'.strtolower($search).'%');
        })
        ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
        ->where($where)
        ->take($limit)->skip($offset)->orderBy($order_col, $order_type)->get();
        if($list) {
            foreach($list as &$ls) {
                unset($ls->karyawan_forgot_password);
                unset($ls->karyawan_password);
            }
        }
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = KaryawanModel::where($where)->whereNotIn('karyawan_status', ['NONKARYAWAN'])->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function delete($karyawanId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $karyawan = KaryawanModel::where([
            'karyawan_id' => $karyawanId, 
            'karyawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])
        ->first();
        if(!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan!'
            ]); 
        }

        $validator = Validator::make($request->all(), [
            'nonaktif_tgl' => 'required',
            'nonaktif_alasan' => 'required|in:RESIGN,LAINNYA,KONTRAK_HABIS',
        ], [
            'nonaktif_tgl.required' => 'Tanggal wajib diisi!',
            'nonaktif_alasan.required' => 'Alasan wajib diisi!',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return response()->json([
                'success' => false,
                'message' => $error
            ]);
        }
        
        $dt_cend = \Carbon\Carbon::parse($request->input('nonaktif_tgl'));
        $dt_cend_date = $dt_cend->translatedFormat('Y-m-d');
        DB::beginTransaction();
        try {
            KaryawanModel::where(['karyawan_id' => $karyawan->karyawan_id])
            ->update([
                'karyawan_active' => 0,
                'karyawan_contract_end' => $dt_cend_date,
                'karyawan_end_type' => $request->input('nonaktif_alasan'),
                'karyawan_end_reason' => $request->input('nonaktif_keterangan'),
            ]);

            // update masa kerja
            KaryawanMasakerjaModel::where([
                'ms_karyawan_id' => $karyawan->karyawan_id,
                'karyawanmasakerja_active' => 1,
            ])->update([
                'karyawanmasakerja_contract_end' => $dt_cend_date,
                'karyawanmasakerja_end_type' => $request->input('nonaktif_alasan'),
                'karyawanmasakerja_end_reason' => $request->input('nonaktif_keterangan'),
                'karyawanmasakerja_active' => 0,
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dinonaktifkan',
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // public function resign($karyawanId, Request $request)
    // {
    //     $karyawan = KaryawanModel::where([
    //         'karyawan_id' => $karyawanId, 
    //         'karyawan_active' => 1,
    //         'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
    //     ])->first();
    //     if(!$karyawan) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Karyawan tidak ditemukan!'
    //         ]); 
    //     }
    //     // dd(session()->get('user_data')['user_id']);
    //     DB::beginTransaction();
    //     try {
    //         $karyawan->update([
    //             'karyawan_active' => 1,
    //             'karyawan_contract_end' => date('Y-m-d')
    //         ]);
            
    //         DB::commit();
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Karyawan berhasil diupdate',
    //             'data' => $karyawan
    //         ]);

    //     } catch(Error $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'success' => false,
    //             'message' => $e->getMessage()
    //         ]);
    //     }
    // }

    /**
     * Display a select of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        $q = $request->get('q');
        $is_manager = $request->get('is_manager');
        $karyawan_id = $request->get('karyawan_id');
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $where = [
            'karyawan_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];
        // if($is_manager == '1') {
        //     $where['karyawan_ismanager'] = 1;
        //     $data = KaryawanModel::select('karyawan_id', 'karyawan_name', 'karyawan_nik', 'karyawan_npwp')
        //     ->when($q, function ($query, $q) {
        //         return $query->where('karyawan_name', 'ilike', '%'.$q.'%');
        //     })
        //     ->where($where)
            
        //     ->limit($limit)->get();
        // } else {
            $data = KaryawanModel::select('karyawan_id', 'karyawan_name', 'karyawan_nik', 'karyawan_npwp')
            ->when($q, function ($query, $q) {
                return $query->where('karyawan_name', 'ilike', '%'.$q.'%');
            })
            ->when($karyawan_id, function($query, $karyawan_id) {
                return $query->whereNotIn('karyawan_id', [$karyawan_id]);
            })
            ->where($where)
            ->whereNotIn('karyawan_status', ['NONKARYAWAN'])
            ->limit($limit)->get();
        // }
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    public function lockKalkulasi(Request $request)
    {
        // dd($request->all());
        $lock_karyawan = $request->input('lock_karyawan_id');
        $lock_metode = $request->input('lock_metode_id');
        $lock_status = $request->input('lock_status_id');
        $lock_periode = $request->input('lock_periode_format');

        $bulan = null;
        $tahun = null;
        if($lock_periode) {
            $bulan = Carbon::createFromFormat('d-m-Y', '01-'.$lock_periode)->format('n');
            $tahun = Carbon::createFromFormat('d-m-Y', '01-'.$lock_periode)->format('Y');
        }
        // dd($bulan);

        $where = [
            'karyawankalkulasi_active' => 1,
            'karyawankalkulasi_lock' => false,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];
        // find kalkulasi
        $kalkulasi = KaryawanKalkulasiModel::select('ms_karyawan_kalkulasi.*', 'karyawan_name')
        ->join('ms_karyawan', 'ms_karyawan_id', '=', 'karyawan_id')
        ->when($bulan, function($q, $bulan) {
            return $q->where('karyawankalkulasi_month', '=', $bulan);
        })
        ->when($tahun, function($q, $tahun) {
            return $q->where('karyawankalkulasi_year', '=', $tahun);
        })
        ->when($lock_karyawan, function($q, $lock_karyawan) {
            // if(is_array($lock_karyawan))
                return $q->whereIn('ms_karyawan_id', $lock_karyawan);
            // return true;
        })
        ->when($lock_metode, function($q, $lock_metode) {
            // if(is_array($lock_metode))
                return $q->whereIn('karyawankalkulasi_method', $lock_metode);
            // return true;
        })
        ->when($lock_status, function($q, $lock_status) {
            // if(is_array($lock_status))
                return $q->whereIn('karyawan_status', $lock_status);
            // return true;
        })
        ->where($where)->get();
        // dd(count($kalkulasi));
        if(count($kalkulasi) < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Kalkulasi tidak ditemukan',
            ]);
        }
        $kalkulasi_ids = [];
        foreach($kalkulasi as $kal) {
            array_push($kalkulasi_ids, $kal->karyawankalkulasi_id);
        }
        
        DB::beginTransaction();
        try {
            KaryawanKalkulasiModel::
            whereIn('karyawankalkulasi_id', $kalkulasi_ids)
            ->update([
                'karyawankalkulasi_lock' => true,
                'karyawankalkulasi_lock_at' => date('Y-m-d H:i:s'),
                // 'karyawankalkulasi_method' => $kalkulasi[0]->karyawan_calculation_method
            ]);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kalkulasi berhasil di lock',
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function importTemplate(Request $request)
    {
        try {
            // Get the file path from the 'public' directory
            $excelFilePath = public_path('assets/import/Template_Import_Karyawan.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $spreadsheet = $reader->load($excelFilePath);

            $sheetSpv = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Referensi Data Supervisor');
            $spreadsheet->addSheet($sheetSpv, 2);

            $supervisor = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                ->where('karyawan_active', 1)
                ->where('karyawan_status', '!=', 'NONKARYAWAN')
                ->orderBy('karyawan_enid', 'ASC')
                ->with(['jabatan'])
                ->get();

            // Get the specified sheet by title
            $htmlString = view('user.master.karyawan.import.profil.import-supervisor', ['title' => 'Referensi Data Supervisor', 'supervisor' => $supervisor])->render();
            $dom1 = new DOMDocument();
            $dom1->loadHTML($htmlString);
            $table1 = $dom1->getElementsByTagName('table')->item(0);

            $rowIndex1 = 1;
            foreach ($table1->getElementsByTagName('tr') as $row) {
                $cellIndex = 1;
                foreach ($row->getElementsByTagName('th') as $header) {
                    // Extract the text content without HTML tags
                    $headerText = strip_tags($header->nodeValue);
                    
                    // Set the cell value
                    $sheetSpv->setCellValueByColumnAndRow($cellIndex, $rowIndex1, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheetSpv->getStyleByColumnAndRow($cellIndex, $rowIndex1)->getFont()->setBold(true);
                    
                    $cellIndex++;
                }
            
                // Skip the first row (headers row) when iterating through data rows
                if ($rowIndex1 === 1) {
                    $rowIndex1++;
                    continue;
                }
            
                foreach ($row->getElementsByTagName('td') as $cell) {
                    // Extract the text content without HTML tags
                    $cellValue = strip_tags($cell->nodeValue);
                    
                    // Set the cell value
                    $sheetSpv->setCellValueByColumnAndRow($cellIndex, $rowIndex1, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex1++;
            }

            $boldFontStyle = [
                'font' => ['bold' => true],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];

            $sheetTemplate = $spreadsheet->createSheet();
            $sheetTemplate->setTitle('Impor Profil Karyawan');
            $sheetTemplate->setCellValue('A1', 'Profil Karyawan');
            $sheetTemplate->mergeCells('A1:N1');
            $sheetTemplate->getStyle('A1')->applyFromArray($boldFontStyle);
            $sheetTemplate->setCellValue('O1', 'Informasi Kontrak');
            $sheetTemplate->mergeCells('O1:Z1');
            $sheetTemplate->getStyle('O1')->applyFromArray($boldFontStyle);
            // Center align cell A1
            $sheetTemplate->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Center align cell O1
            $sheetTemplate->getStyle('O1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheetTemplate->setCellValue('A2', 'Nama Karyawan*');
            $sheetTemplate->setCellValue('B2', 'Id Karyawan*');
            $sheetTemplate->setCellValue('C2', 'NIK*');
            $sheetTemplate->setCellValue('D2', 'Nomor Tlp');
            $sheetTemplate->setCellValue('E2', 'NPWP*');
            $sheetTemplate->setCellValue('F2', 'Email');
            $sheetTemplate->setCellValue('G2', 'Tempat Lahir');
            $sheetTemplate->setCellValue('H2', 'Tanggal Lahir');
            $sheetTemplate->setCellValue('I2', 'Jenis Kelamin*');
            $sheetTemplate->setCellValue('J2', 'Alamat');
            $sheetTemplate->setCellValue('K2', 'Status Perkawinan*');
            $sheetTemplate->setCellValue('L2', 'Tambahkan Sebagai User');
            $sheetTemplate->setCellValue('M2', 'Status Karyawan*');
            $sheetTemplate->setCellValue('N2', 'Tanggal Masuk*');
            $sheetTemplate->setCellValue('O2', 'Tanggal Berakhir Kontrak');
            $sheetTemplate->setCellValue('P2', 'Divisi');
            $sheetTemplate->setCellValue('Q2', 'Jabatan');
            $sheetTemplate->setCellValue('R2', 'Id Supervisor');
            $sheetTemplate->setCellValue('S2', 'Gaji Pokok*');
            $sheetTemplate->setCellValue('T2', 'Metode Pph 21*');
            $sheetTemplate->setCellValue('U2', 'Kode Bank');
            $sheetTemplate->setCellValue('V2', 'Nomor Rekening');
            $sheetTemplate->setCellValue('W2', 'No. BPJS Tenaga Kerja');
            $sheetTemplate->setCellValue('X2', 'Tgl. Berlaku BPJS TK');
            $sheetTemplate->setCellValue('Y2', 'No. BPJS Kesehatan');
            $sheetTemplate->setCellValue('Z2', 'Tgl. Berlaku BPJS Kesehatan');
            // $sheetTemplate->getStyle('A2:Z2')->applyFromArray($boldFontStyle);

            for ($col = 'A'; $col <= 'Z'; $col++) {
                if($col === 'AA') {
                    break;
                }
                $sheetTemplate->getStyle($col . '2')->applyFromArray($boldFontStyle);
                $sheetTemplate->getStyle($col . ':' . $col)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            }

            $startRow = 3;

            $listGender = '"L,P"';
            $genderValidation = $sheetTemplate->getCell('I' . $startRow)->getDataValidation();
            $genderValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $genderValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $genderValidation->setShowDropDown(true);
            $genderValidation->setFormula1($listGender);

            $listTrueFalse = '"Ya,Tidak"';
            $userValidation = $sheetTemplate->getCell('L' . $startRow)->getDataValidation();
            $userValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $userValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $userValidation->setShowDropDown(true);
            $userValidation->setFormula1($listTrueFalse);

            $listStatus = '"PERCOBAAN,KONTRAK,TETAP"';
            $statusValidation = $sheetTemplate->getCell('M' . $startRow)->getDataValidation();
            $statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $statusValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $statusValidation->setShowDropDown(true);
            $statusValidation->setFormula1($listStatus);

            $listMethod = '"GROSS,NETT,GROSS_UP"';
            $methodValidation = $sheetTemplate->getCell('T' . $startRow)->getDataValidation();
            $methodValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $methodValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $methodValidation->setShowDropDown(true);
            $methodValidation->setFormula1($listMethod);

            for ($row = $startRow + 1; $row <= 52; $row++) {
                $cellI = $sheetTemplate->getCell('I' . $row);
                $cellI->setDataValidation(clone $genderValidation);
                
                $cellL = $sheetTemplate->getCell('L' . $row);
                $cellL->setDataValidation(clone $userValidation);
                
                $cellM = $sheetTemplate->getCell('M' . $row);
                $cellM->setDataValidation(clone $statusValidation);
                
                $cellT = $sheetTemplate->getCell('T' . $row);
                $cellT->setDataValidation(clone $methodValidation);
            }

            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);
                
                // Set all columns in the sheet to auto width
                if($sheetName !== 'Panduan Pengguna') {
                    foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }
            
            $writer = new Xls($spreadsheet);
            $unixtime = time();
            $filename = 'Templat_Impor_Karyawan_' . $unixtime . '.xls';
            $location = public_path('assets/export/');
            ob_start();
            $writer->save($location.$filename);
            // $xlsData = ob_get_contents();
            ob_end_clean();

            return url('/assets/export/'.$filename);
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function import(Request $request)
    {
        if (!$request->hasfile('file')) {
            $res["success"] = false;
            $res["message"] = 'Data excel wajib diisi!';
            return response()->json($res, 500);
        }
        $quota = $request->get('quota');
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $user_id = session()->get('user_data')['user_id'];

        // Get the uploaded file
        $file = $request->file('file');

        if(!in_array($file->extension(), ['xls','xlsx','csv'])) {
            $res["success"] = false;
            $res["message"] = 'Format harus xls, xlsx atau csv!';
            return response()->json($res, 500);
        }

        $filename = rand().'.xlsx';
        $file->move(public_path('uploads/'), $filename);
        DB::beginTransaction();
        try {
            $totalComplete = 0;
            $totalFail = 0;
            $createHistory = HistoryImportModel::create([
                'ms_wajibpajak_id' => $wajibpajak_id,
                'ms_user_id' => $user_id,
                'historyimport_type' => "EMPLOYEE",
                'historyimport_date' => date('Y-m-d H:i:s'),
                'historyimport_file_name' => $filename,
                'historyimport_originfile_name' => $file->getClientOriginalName(),
                'historyimport_status' => 'PROCESSED'
            ]);

            dispatch(new ImportJob($createHistory->historyimport_id, $createHistory->historyimport_type, ['quota' => $quota]));
            // ->delay(Carbon::now()->addSeconds(60));
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Berhasil import data karyawan.',
            ], 200);
        } catch (\Exception $e) {
            unlink(public_path('uploads/'). $filename); // remove file
            Log::error('Error occurred: ' . $e->getMessage());
            DB::rollback();
            // something went wrong
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function importKaryawan($historyid, $otherdata)
    {
        $getHistory = HistoryImportModel::where(['historyimport_id' => $historyid])->first();

        $wajibpajak_id = $getHistory->ms_wajibpajak_id;
        $wajibpajak = $getHistory->wajibpajak;
        $user_id = $getHistory->ms_user_id;
        $user = $getHistory->user;
        $filename = $getHistory->historyimport_file_name;
    
        // $quota = $request->get('quota');

        $path = public_path('uploads/'.$filename);
        $inputFileType = IOFactory::identify($path);
        $reader = IOFactory::createReader($inputFileType);
        $worksheetData = $reader->listWorksheetInfo($path);

        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
    
        // Get the Sheet D
        $sheet = $spreadsheet->getSheetByName('Impor Profil Karyawan');
        $maxRows = $sheet->getHighestRow();
        // dd($maxRows);
        $quota = $otherdata['quota'];
        // dd($maxRows, $quota);
        // if($maxRows > $quota['maxquota']) {
        //     return response()->json([
        //         'success' => false,
        //         'noquota' => true,
        //         'message' => 'Kuota sudah habis! kuota maksimal adalah '.$quota['maxquota']
        //     ]);
        // }

        $chunkFilter = new ChunkReadFilter();
        $reader->setReadFilter($chunkFilter);

        $chunkSize = 100; // read as chunk
        $startRow = 2; // mulai baris ke 3;

        $result = [];

        for ($startRow; $startRow <= $maxRows; $startRow += $chunkSize) {
            if($quota && $startRow > $quota['maxquota']) { // exceded quota
                break;
            }
            $chunkFilter = new ChunkReadFilter($startRow, $chunkSize);
            // // Tell the Read Filter, the limits on which rows we want to read this iteration
            $chunkFilter->setRows($startRow, $chunkSize);
            // Load only the rows that match our filter from $inputFileName to a PhpSpreadsheet Object
            $spreadsheet_chunk = $reader->load($path);
            $nb = $startRow;
            $max_chunk = $startRow + $chunkSize;

            for($nb; $nb<=$max_chunk; $nb++) {
                $nbi = $nb + 1;
                $columnA = $sheet->getCell("A" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $dataB = $sheet->getCell("B" . $nbi)->getValue();

                // if($columnA === null) {
                //     if($dataB === null) {
                //         break;
                //     } 
                //     // else {
                //     //     $spreadsheet_chunk->__destruct();
                //     //     $spreadsheet_chunk = null;
                //     //     unset($spreadsheet_chunk);
                        
                //     //     return response()->json([
                //     //         'success' => false,
                //     //         'message' => "Mohon lengkapi Nama Karyawan yang masih kosong"
                //     //     ]);
                //     // }
                // }

                $columnB = $dataB;
                $columnC = $sheet->getCell("C" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnD = $sheet->getCell("D" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnE = $sheet->getCell("E" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnF = $sheet->getCell("F" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnG = $sheet->getCell("G" . $nbi)->getValue();
                $columnH = $sheet->getCell("H" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnI = $sheet->getCell("I" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnJ = $sheet->getCell("J" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnK = $sheet->getCell("K" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnL = $sheet->getCell("L" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnM = $sheet->getCell("M" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnN = convertExcelDate($sheet->getCell("N" . $nbi)); // Use $sheet instead of $worksheetData
                $columnO = convertExcelDate($sheet->getCell("O" . $nbi)); // Use $sheet instead of $worksheetData
                $columnP = $sheet->getCell("P" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnQ = $sheet->getCell("Q" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnR = $sheet->getCell("R" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnS = $sheet->getCell("S" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnT = $sheet->getCell("T" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnU = $sheet->getCell("U" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnV = $sheet->getCell("V" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnW = $sheet->getCell("W" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnX = convertExcelDate($sheet->getCell("X" . $nbi)); // Use $sheet instead of $worksheetData
                $columnY = $sheet->getCell("Y" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnZ = convertExcelDate($sheet->getCell("Z" . $nbi)); // Use $sheet instead of $worksheetData
                if(!$columnA && !$columnB && !$columnC) {
                    continue;
                }
                $result[] = [
                    "karyawan_name" => $columnA,
                    "karyawan_enid" => $columnB,
                    "karyawan_nik" => $columnC,
                    "karyawan_phone" => $columnD,
                    "karyawan_npwp" => $columnE,
                    "karyawan_email" => $columnF,
                    "karyawan_birthplace" => $columnG,
                    "karyawan_birthdate" => $columnH,
                    "karyawan_gender" => $columnI,
                    "karyawan_address" => $columnJ,
                    "karyawan_ptkp" => $columnK,
                    "karyawan_create_user" => $columnL,
                    "karyawan_contract_status" => $columnM,
                    "karyawan_start_contract" => $columnN,
                    "karyawan_end_contract" => $columnO,
                    "karyawan_divisi" => $columnP,
                    "karyawan_jabatan" => $columnQ,
                    "karyawan_manager_id" => $columnR,
                    "karyawan_salary" => $columnS,
                    "karyawan_ppn_method" => $columnT,
                    "karyawan_code_bank" => $columnU,
                    "karyawan_bank_account" => $columnV,
                    "karyawan_bpjstkno" => $columnW,
                    "karyawan_bpjstkdate" => $columnX,
                    "karyawan_bpjskesno" => $columnY,
                    "karyawan_bpjskesdate" => $columnZ,
                    'karyawan_isbpjstk' => false,
                    'karyawan_isbpjskes' => false,
                    "row" => $nb + 1
                ];
            }

            $spreadsheet_chunk->__destruct();
            $spreadsheet_chunk = null;
            unset($spreadsheet_chunk);
        }
        // then release the memory
        $spreadsheet->__destruct();
        $spreadsheet = null;
        unset($spreadsheet);
        $reader = null;
        unset($reader);

        
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Berhasil import data non karyawan.',
        //     'result' => $result
        // ], 200);

        // function convertDateStringToYYYYMMDD($dateString) {
        //     if ($dateString === null || $dateString === '') {
        //         return null; // Return null for empty input
        //     } else {
        //         // Split the date string into day, month, and year
        //         list($day, $month, $year) = explode('-', $dateString);
        
        //         // Create a new date in the "YYYY-MM-DD" format
        //         $newDateString = "{$year}-{$month}-{$day}";
        
        //         return $newDateString;
        //     }
        // }

        // function capitalizeFirstLetterOfWords($string) {
        //     // Convert the string to lowercase
        //     $lowercaseString = strtolower($string);
            
        //     // Use ucwords() to capitalize the first letter of each word
        //     $capitalizedString = ucwords($lowercaseString);
            
        //     return $capitalizedString;
        // }

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Berhasil import data karyawan.',
        //     'result' => $result
        // ], 200);
        // $user_id = session()->get('user_data')['user_id'];
        // $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $checkAnnouncements = SettingAnnouncementModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'announcement_active' => true,
            'announcement_is_all' => true,
        ])
        ->where('announcement_start_date', '>=', date('Y-m-d'))
        ->get();
        $announcementdatas = [];
        if(count($checkAnnouncements) > 0) {
            foreach($checkAnnouncements as $announ) {
                $announcementdatas[] = [
                    'announcement_id' => $announ->announcement_id,
                    'announcement_karyawan' => json_decode($announ->announcement_karyawan),
                ];
            }
        }
        // dd(json_decode($checkAnnouncements[0]->announcement_karyawan));

        $karyawan_bpjskeslainnya = 0;
        $karyawan_bpjstklainnya = 0;
        $tunjanganbpjs = SettingBpjsKaryawanModel::where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'stbpjskaryawan_active' => 1
        ])->first();
        $stbpjskaryawan_data = ($tunjanganbpjs) ? json_decode($tunjanganbpjs->stbpjskaryawan_value) : [];
        $bpjskes = false;
        $bpjstk = false;
        $stbpjskes_lainnya = false;
        $stbpjstk_lainnya = false;
        foreach ($stbpjskaryawan_data as $stbpjs) {
            if ($stbpjs->type == 'KESEHATAN') {
                $bpjskes = true;
                if($stbpjs->name == 'LAINNYA') {
                    $stbpjskes_lainnya = true;
                    $karyawan_bpjskeslainnya = $stbpjs->value;
                }
            }
            if ($stbpjs->type == 'TENAGA_KERJA') {
                $bpjstk = true;
                if($stbpjs->name == 'LAINNYA') {
                    $stbpjstk_lainnya = true;
                    $karyawan_bpjstklainnya = $stbpjs->value;
                }
            }
        }
        
        DB::beginTransaction();
        try {
            $totalComplete = 0;
            $totalFail = 0;
            

            if(count($result) > 0) {
                $karyawan_exist_enids = [];
                $karyawan_exist_nik = [];
                for($x = 0; $x < count($result); $x++) { // check karyawan nik or karyawan npwp
                    array_push($karyawan_exist_enids, $result[$x]['karyawan_enid']);
                    array_push($karyawan_exist_nik, $result[$x]['karyawan_nik']);
                }
                // $checkKaryawans = KaryawanModel::whereIn('karyawan_enid', $karyawan_exist_enids)->where('ms_wajibpajak_id', $wajibpajak_id)->first();
                //     if($checkKaryawans) {
                $infoctrl = new InfoController();
                $request = new \Illuminate\Http\Request();
                $request->setMethod('POST');
                //     foreach($checkKaryawans as $ckry) {
                //         $totalFail = $totalFail + 1;
                //         $result[$x]['status'] = 'Gagal';
                //         $result[$x]['remark'] = 'Id Karyawan '.$ckry->karyawan_enid.' sudah terdaftar atas nama '.$$ckry->karyawan_name;
                //         continue;
                //         // $request->input('karyawan_id');
                //         // $request->request->add(['karyawan_id' => $ckry->karyawan_id]);
                //         // $request->request->add(['karyawan_enid' => $ckry->karyawan_id]);
                //         // $infoctrl->getkaryawanbykode()
                //     }
                // }

                $checkPtkp = PtkpModel::where(['ptkp_active' => 1])->get();
                $checkBank = BankModel::where(['bank_active' => 1])->get();
                $tunjanganId = SettingTunjanganKaryawanModel::where('ms_wajibpajak_id', $wajibpajak_id)
                    ->where('sttunjangankaryawan_is_all', true)
                    ->where('sttunjangankaryawan_active', 1)
                    ->pluck('sttunjangankaryawan_id')
                    ->toArray();
                $potonganId = SettingPotonganKaryawanModel::where('ms_wajibpajak_id', $wajibpajak_id)
                    ->where('stpotongankaryawan_is_all', true)
                    ->where('stpotongankaryawan_active', 1)
                    ->pluck('stpotongankaryawan_id')          
                    ->toArray();;
                $penggajian = SettingPenggajianKaryawanModel::where('ms_wajibpajak_id', $wajibpajak_id)
                    ->where('stpenggajiankaryawan_is_all', true)
                    ->where('stpenggajiankaryawan_active', 1)
                    ->first();
                $penggajianId = $penggajian ? $penggajian->stpenggajiankaryawan_id : null;
                $leaveId = SettingLeaveModel::where('ms_wajibpajak_id', $wajibpajak_id)
                    ->where('leave_is_all', true)
                    ->where('leave_status_active', true)
                    ->pluck('leave_id')
                    ->toArray();
                $attendance = SettingAttendanceModel::where('ms_wajibpajak_id', $wajibpajak_id)
                    ->where('attendance_is_all', true)
                    ->where('attendance_status_active', true)
                    ->first();
                $attendanceId = $attendance ? $attendance->attendance_id : null;

                $karyawanCreated = [];
                $karyawanCreatedForSetting = [];
                // dd(count($result));
                for($x = 0; $x < count($result); $x++) {
                    $checkKaryawan = KaryawanModel::where('karyawan_enid', $result[$x]['karyawan_enid'])->where('ms_wajibpajak_id', $wajibpajak_id)->first();
    
                    // dd($checkKaryawan);
                    if($checkKaryawan) {
                        $request->request->add(['karyawan_id' => $checkKaryawan->karyawan_id]);
                        $request->request->add(['karyawan_nik' => $result[$x]['karyawan_nik']]);
                        $checkNik = $infoctrl->getkaryawanbynik($request, $user_id, $wajibpajak_id);
                        // dd($checkNik);
                        if(!$checkNik->getData()->success) {
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = $checkNik->getData()->message;
                            continue;
                        }
                        $request->request->add(['karyawan_npwp' => $result[$x]['karyawan_npwp']]);
                        $checkNpwp = $infoctrl->getkaryawanbynpwp($request, $user_id, $wajibpajak_id);
                        if(!$checkNpwp->getData()->success) {
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = $checkNpwp->getData()->message;
                            continue;
                        }
                    } else {
                        $request->request->add(['karyawan_nik' => $result[$x]['karyawan_nik']]);
                        $checkNik = $infoctrl->getkaryawanbynik($request, $user_id, $wajibpajak_id);
                        // dd($checkNik);
                        if(!$checkNik->getData()->success) {
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = $checkNik->getData()->message;
                            continue;
                        }
                        $request->request->add(['karyawan_npwp' => $result[$x]['karyawan_npwp']]);
                        $checkNpwp = $infoctrl->getkaryawanbynpwp($request, $user_id, $wajibpajak_id);
                        if(!$checkNpwp->getData()->success) {
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = $checkNpwp->getData()->message;
                            continue;
                        }
                    }
                    // if($checkKaryawan) {
                        
                    //     $totalFail = $totalFail + 1;
                    //     $result[$x]['status'] = 'Gagal';
                    //     $result[$x]['remark'] = 'Id Karyawan sudah terdaftar';
                    //     continue;
                    // }

                    if (preg_match('/\d/', $result[$x]['karyawan_name'])) {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Nama hanya boleh menggunakan Alphabet';
                        continue;
                    }
                    
                    if ($result[$x]['karyawan_enid'] === null || $result[$x]['karyawan_enid'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Karyawan Id';
                        continue;
                    }
                    
                    if ($result[$x]['karyawan_nik'] === null || $result[$x]['karyawan_nik'] === '' || $result[$x]['karyawan_nik'] === '' ||  preg_match('/[^0-9]/', $result[$x]['karyawan_nik']) || !preg_match('/^\d{16}$/', $result[$x]['karyawan_nik'])) {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan NIK dengan benar';
                        continue;
                    }
                    
                    if ($result[$x]['karyawan_npwp'] !== null && $result[$x]['karyawan_npwp'] !== '') {
                        if(!preg_match('/^\d{2}\.\d{3}\.\d{3}\.\d{1}-\d{3}\.\d{3}$/', $result[$x]['karyawan_npwp'])) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan NPWP dengan benar';
                            continue;
                        }
                    } else {
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan nomor NPWP';
                        continue;
                    }
                    
                    if ($result[$x]['karyawan_email'] !== null && $result[$x]['karyawan_email'] !== '' && !filter_var($result[$x]['karyawan_email'], FILTER_VALIDATE_EMAIL)) {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Email dengan benar';
                        continue;
                    }
                    
                    if ($result[$x]['karyawan_gender'] !== 'L' && $result[$x]['karyawan_gender'] !== 'P') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Jenis Kelamin hanya dengan L / P';
                        continue;
                    }
                    
                    $isUser = false;

                    if (strtolower($result[$x]['karyawan_create_user']) !== 'tidak' && $result[$x]['karyawan_create_user'] !== '' && $result[$x]['karyawan_create_user'] !== null && !filter_var($result[$x]['karyawan_email'], FILTER_VALIDATE_EMAIL)) {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Email yang valid untuk pembuatan User Karyawan';
                        continue;
                    } else if (strtolower($result[$x]['karyawan_create_user']) === 'ya' || strtolower($result[$x]['karyawan_create_user']) === 'iya'){
                        $isUser = true;
                    }
                    
                    if ($result[$x]['karyawan_contract_status'] !== 'PERCOBAAN' && $result[$x]['karyawan_contract_status'] !== 'TETAP' && $result[$x]['karyawan_contract_status'] !== 'KONTRAK') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Status Karyawan dengan KONTRAK / PERCOBAAN / TETAP';
                        continue;
                    }
                    
                    if (formatDate($result[$x]['karyawan_start_contract'], '/^\d{2}-\d{2}-\d{4}$/') === false) {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan format Tanggal Masuk sesuai format sistem REKKAA';
                        continue;
                    }
                    
                    if($result[$x]['karyawan_contract_status'] === 'PERCOBAAN' || $result[$x]['karyawan_contract_status'] === 'KONTRAK') {
                        if (formatDate($result[$x]['karyawan_end_contract'], '/^\d{2}-\d{2}-\d{4}$/') === false) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan format Tanggal Berakhir Kontrak sesuai format sistem REKKAA untuk karyawan PERCOBAAN / KONTRAK';
                            continue;
                        }
                    }

                    if($result[$x]['karyawan_birthdate'] !== '' && $result[$x]['karyawan_birthdate'] !== null && $result[$x]['karyawan_birthdate'] !== '-') {
                        if (formatDate($result[$x]['karyawan_birthdate'], '/^\d{2}-\d{2}-\d{4}$/') === false) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan format Tanggal Lahir sesuai format sistem REKKAA';
                            continue;
                        }
                    }
                    
                    if($result[$x]['karyawan_salary'] === null || $result[$x]['karyawan_salary'] === '' || preg_match('/[^0-9]/', $result[$x]['karyawan_salary'])) {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon hanya masukan Angka untuk Gaji Karyawan';
                        continue;
                    }
                    
                    if($result[$x]['karyawan_ptkp'] === null || $result[$x]['karyawan_ptkp'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Status Perkawinan sesuai sheet Referensi Status Perkawinan';
                        continue;
                    }
                    
                    if($result[$x]['karyawan_ppn_method'] !== 'GROSS' && $result[$x]['karyawan_ppn_method'] !== 'GROSS_UP' && $result[$x]['karyawan_ppn_method'] !== 'NETT') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon hanya masukan GROSS / NETT / GROSS_UP untuk Metode Pph 21';
                        continue;
                    }

                    if($result[$x]['karyawan_bpjstkno'] !== null && $result[$x]['karyawan_bpjstkno'] !== '') {
                        if(($result[$x]['karyawan_bpjstkdate']) === false) {
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan format Tgl. Berlaku BPJS TK sesuai format sistem REKKAA';
                            continue;
                        } else {
                            $result[$x]['karyawan_isbpjstk'] = true;
                        }
                    } else {
                        $result[$x]['karyawan_isbpjstk'] = false;
                    }

                    if($result[$x]['karyawan_bpjskesno'] !== null && $result[$x]['karyawan_bpjskesno'] !== '') {
                        if($result[$x]['karyawan_bpjskesdate'] === null || $result[$x]['karyawan_bpjskesdate'] === '') {
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan format Tgl. Berlaku BPJS Kesehatan sesuai format sistem REKKAA';
                            continue;
                        } else {
                            $result[$x]['karyawan_isbpjskes'] = true;
                        }
                    } else {
                        $result[$x]['karyawan_isbpjskes'] = false;
                    }
                    
                    if($result[$x]['karyawan_ptkp'] !== null && $result[$x]['karyawan_ptkp'] !== '') {
                        $found = false;

                        foreach ($checkPtkp as $item) {
                            if (isset($item['ptkp_id']) && $item['ptkp_id'] == preg_replace('/[^0-9]/', '', $result[$x]['karyawan_ptkp'])) {
                                $found = true;
                                break; // Exit the loop when a match is found.
                            }
                        }
    
                        if($found === false) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan Status Perkawinan sesuai sheet Referensi Status Perkawinan';
                            continue;
                        }
                    }

                    if($result[$x]['karyawan_code_bank'] !== null && $result[$x]['karyawan_code_bank'] !== '') {
                        $found = false;

                        foreach ($checkBank as $item) {
                            if (isset($item['bank_code']) && $item['bank_code'] == $result[$x]['karyawan_code_bank']) {
                                $found = true;
                                $result[$x]['karyawan_code_bank'] = $item['bank_id'];
                                break; // Exit the loop when a match is found.
                            }
                        }
    
                        if($found === false) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan Kode Bank sesuai sheet Referensi Kode Bank';
                            continue;
                        }
                    } 

                    $manager_id = null;
                    $useManager = false;
                    $managerUpdate = false;
                    if($result[$x]['karyawan_manager_id'] !== '' && $result[$x]['karyawan_manager_id'] !== null) {           
                        $found = false;

                        $checkManager = KaryawanModel::where('karyawan_enid', $result[$x]['karyawan_manager_id'])->where('ms_wajibpajak_id', $wajibpajak_id)->first();

                        if(!$checkManager) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Supervisor Id tidak terdaftar di sistem REKKAA';
                            continue;
                        } else {
                            $manager_id = $checkManager->karyawan_manager_id;
                            $useManager = true;
                            
                            if($checkManager->karyawan_is_manager === '0') {
                                $managerUpdate = true;
                            }
                        }

                        if($useManager === true && $managerUpdate === true) {
                            $checkManager->update([
                                'karyawan_is_manager' => '1'
                            ]);
                        }
                    }
                    
                    $random_password = null;
                    $generate_forgot_password = null;
                    if($isUser === true) {
                        $random_password = Hash::make(Str::random(6));
                        $generate_forgot_password = Hash::make('FGP'.$result[$x]['karyawan_email'].uniqid());
                    }

                    $jabatan_id = null;
                    if($result[$x]['karyawan_jabatan'] !== null && $result[$x]['karyawan_jabatan'] !== '' && $result[$x]['karyawan_jabatan'] !== ' ') {
                        $jabatan = capitalizeFirstLetterOfWords($result[$x]['karyawan_jabatan']);
                        $checkJabatan = KaryawanJabatanModel::where('ms_wajibpajak_id', $wajibpajak_id)->where('karyawanjabatan_active', '1')->where('karyawanjabatan_name', $jabatan)->first();

                        if(!$checkJabatan) {
                            $createJabatan = KaryawanJabatanModel::create([
                                'karyawanjabatan_name' => $jabatan,
                                'ms_user_id' => $user_id,
                                'ms_wajibpajak_id' => $wajibpajak_id,
                            ]);
                            $jabatan_id = $createJabatan->karyawanjabatan_id;
                        } else {
                            $jabatan_id = $checkJabatan->karyawanjabatan_id;
                        }
                    }

                    $divisi_id = null;
                    if($result[$x]['karyawan_divisi'] !== null && $result[$x]['karyawan_divisi'] !== '' && $result[$x]['karyawan_divisi'] !== ' ') {
                        $divisi = capitalizeFirstLetterOfWords($result[$x]['karyawan_divisi']);
                        $checkDivisi = KaryawanDivisiModel::where('ms_wajibpajak_id', $wajibpajak_id)->where('karyawandivisi_active', '1')->where('karyawandivisi_name', $divisi)->first();
                        // dd($checkDivisi);
                        if(!$checkDivisi) {
                            $createDivisi = KaryawanDivisiModel::create([
                                'karyawandivisi_name' => $divisi,
                                'ms_user_id' => $user_id,
                                'ms_wajibpajak_id' => $wajibpajak_id,
                            ]);
                            $divisi_id = $createDivisi->karyawandivisi_id;
                        } else {
                            $divisi_id = $checkDivisi->karyawandivisi_id;
                        }
                    }
                    // dd($divisi_id);
                    // dd($checkKaryawan);
                    if($checkKaryawan) { //update
                        // $totalFail = $totalFail + 1;
                        // $result[$x]['status'] = 'Gagal';
                        // $result[$x]['remark'] = 'Id Karyawan sudah terdaftar';
                        // continue;
                        $karyawan_updated_data = [
                            'karyawan_nik' => $result[$x]['karyawan_nik'],
                            'karyawan_npwp' => $result[$x]['karyawan_npwp'],
                            'karyawan_name' => capitalizeFirstLetterOfWords($result[$x]['karyawan_name']),
                            'karyawan_birthdate' => convertDateStringToYYYYMMDD($result[$x]['karyawan_birthdate']),
                            'karyawan_phone' => $result[$x]['karyawan_phone'],
                            'karyawan_email' => $result[$x]['karyawan_email'],
                            // 'karyawan_citizenship' => 'WNI',
                            'karyawan_gender' => $result[$x]['karyawan_gender'],
                            'karyawan_address' => $result[$x]['karyawan_address'],
                            // 'karyawan_active' => '1',
                            // 'ms_wajibpajak_id' => $wajibpajak_id,
                            // 'ms_user_id' => $user_id,
                            'karyawan_manager_id' => $manager_id,
                            'karyawan_status' => $result[$x]['karyawan_contract_status'],
                            'karyawan_calculation_method' => $result[$x]['karyawan_ppn_method'],
                            // 'karyawan_contract_begin' => convertDateStringToYYYYMMDD($result[$x]['karyawan_start_contract']),
                            'karyawan_salary' => $result[$x]['karyawan_salary'],
                            // 'ms_ptkp_id' => preg_replace('/[^0-9]/', '', $result[$x]['karyawan_ptkp']),
                            'karyawan_enid' => $result[$x]['karyawan_enid'],
                            'ms_bank_id' => $result[$x]['karyawan_code_bank'],
                            'ms_karyawandivisi_id' => $divisi_id,
                            'ms_karyawanjabatan_id' => $jabatan_id,
                            'karyawan_bankno' => $result[$x]['karyawan_bank_account'],
                            'karyawan_birthplace' => capitalizeFirstLetterOfWords($result[$x]['karyawan_birthplace']),
                            'karyawan_password' => $random_password,
                            'karyawan_forgot_password' => $generate_forgot_password,
                            // 'karyawan_contract_end' => $result[$x]['karyawan_contract_status'] === 'PERCOBAAN' ? convertDateStringToYYYYMMDD($result[$x]['karyawan_end_contract']) : null, //belom
                            'karyawan_isuser' => $isUser === true ? '1' : '0',
                            'karyawan_contract_end' => convertDateStringToYYYYMMDD($result[$x]['karyawan_end_contract']), //belom
                            'karyawan_bpjstkno' => $result[$x]['karyawan_isbpjstk'] ? $result[$x]['karyawan_bpjstkno'] : null,
                            'karyawan_bpjstkdate' => $result[$x]['karyawan_isbpjstk'] ? convertDateStringToYYYYMMDD($result[$x]['karyawan_bpjstkdate']) : null,
                            'karyawan_isbpjstk' => $result[$x]['karyawan_isbpjstk'],
                            'karyawan_bpjskesno' => $result[$x]['karyawan_isbpjskes'] ? $result[$x]['karyawan_bpjskesno'] : null,
                            'karyawan_bpjskesdate' => $result[$x]['karyawan_isbpjskes'] ? convertDateStringToYYYYMMDD($result[$x]['karyawan_bpjskesdate']) : null,
                            'karyawan_isbpjskes' => $result[$x]['karyawan_isbpjskes'],
                            // 'st_tunjangan_id' => $tunjanganId === '' ? null : $tunjanganId,
                            'st_penggajian_id' => $penggajianId,
                            // 'st_potongan_id' => $potonganId === '' ? null : $potonganId,
                            // 'st_leave_id' => empty($leaveId) ? null : json_encode($leaveId),
                            'st_attendance_id' => $attendanceId,
                            'karyawan_bpjskeslainnya' => $karyawan_bpjskeslainnya,
                            'karyawan_bpjstklainnya' => $karyawan_bpjstklainnya,
                            'karyawan_code_objekpajak' => '21-100-01'
                        ];
                        $contract_begin = convertDateStringToYYYYMMDD($result[$x]['karyawan_start_contract']);
                        if($contract_begin != $checkKaryawan->karyawan_contract_begin) {
                            if(!$checkKaryawan->payrollExist) {
                                $karyawan_updated_data['karyawan_contract_begin'] = convertDateStringToYYYYMMDD($result[$x]['karyawan_start_contract']);
                                $payroll_dtparse = Carbon::parse($karyawan_updated_data['karyawan_contract_begin']);
                                // if($deleted_prev_payroll) {
                                //     $payroll_dtparse = Carbon::parse($save_data_karyawan['karyawan_contract_begin']);
                                    $payrolls = PayrollModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_karyawan_id' => $checkKaryawan->karyawan_id
                                    // , 'payroll_lock' => 0
                                    ])
                                    ->where('payroll_status', '<=', 2)
                                    ->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') < ?)", [$payroll_dtparse->format('Y-m')])->get();
                                    // dd($payrolls);
                                    $payroll_uuids = [];
                                    foreach($payrolls as $py) {
                                        array_push($payroll_uuids, $py['payroll_uuid']);
                                    }
                                    if(count($payroll_uuids) > 0) {
                                        PPh21Model::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_karyawan_id' => $checkKaryawan->karyawan_id])
                                        ->whereRaw("(TO_CHAR(pph21_period, 'YYYY-MM') < ?)", [$payroll_dtparse->format('Y-m')])
                                        ->whereIn('tr_payroll_uuid', $payroll_uuids)->delete();
                    
                                        PayrollModel::whereIn('payroll_uuid', $payroll_uuids)->delete();
                                    }
                                // }
                            } else {
                                $totalFail = $totalFail + 1;
                                $result[$x]['status'] = 'Gagal';
                                $result[$x]['remark'] = 'Tanggal masuk tidak bisa dirubah. Karyawan sudah memiliki transaksi.';
                                continue;
                            }
                        }
                        $ptkp_id = preg_replace('/[^0-9]/', '', $result[$x]['karyawan_ptkp']);
                        if($ptkp_id != $checkKaryawan->ms_ptkp_id) {
                            if(!$checkKaryawan->payrollExistCurrentYear) {
                                $karyawan_updated_data['ms_ptkp_id'] = $ptkp_id;
                            } else {
                                $totalFail = $totalFail + 1;
                                $result[$x]['status'] = 'Gagal';
                                $result[$x]['remark'] = 'PTKP tidak bisa dirubah. Karyawan sudah memiliki transaksi.';
                                continue;
                            }
                        }
                        
                        KaryawanModel::where(['karyawan_id' => $checkKaryawan->karyawan_id])->update($karyawan_updated_data);
                    } else { //create
                        $createKaryawan = KaryawanModel::create([
                            'karyawan_nik' => $result[$x]['karyawan_nik'],
                            'karyawan_npwp' => $result[$x]['karyawan_npwp'],
                            'karyawan_name' => capitalizeFirstLetterOfWords($result[$x]['karyawan_name']),
                            'karyawan_birthdate' => convertDateStringToYYYYMMDD($result[$x]['karyawan_birthdate']),
                            'karyawan_phone' => $result[$x]['karyawan_phone'],
                            'karyawan_email' => $result[$x]['karyawan_email'],
                            'karyawan_citizenship' => 'WNI',
                            'karyawan_gender' => $result[$x]['karyawan_gender'],
                            'karyawan_address' => $result[$x]['karyawan_address'],
                            'karyawan_active' => '1',
                            'ms_wajibpajak_id' => $wajibpajak_id,
                            'ms_user_id' => $user_id,
                            'karyawan_manager_id' => $manager_id,
                            'karyawan_status' => $result[$x]['karyawan_contract_status'],
                            'karyawan_calculation_method' => $result[$x]['karyawan_ppn_method'],
                            'karyawan_contract_begin' => convertDateStringToYYYYMMDD($result[$x]['karyawan_start_contract']),
                            'karyawan_salary' => $result[$x]['karyawan_salary'],
                            'ms_ptkp_id' => preg_replace('/[^0-9]/', '', $result[$x]['karyawan_ptkp']),
                            'karyawan_enid' => $result[$x]['karyawan_enid'],
                            'ms_bank_id' => $result[$x]['karyawan_code_bank'],
                            'ms_karyawandivisi_id' => $divisi_id,
                            'ms_karyawanjabatan_id' => $jabatan_id,
                            'karyawan_bankno' => $result[$x]['karyawan_bank_account'],
                            'karyawan_birthplace' => capitalizeFirstLetterOfWords($result[$x]['karyawan_birthplace']),
                            'karyawan_password' => $random_password,
                            'karyawan_forgot_password' => $generate_forgot_password,
                            // 'karyawan_contract_end' => $result[$x]['karyawan_contract_status'] === 'PERCOBAAN' ? convertDateStringToYYYYMMDD($result[$x]['karyawan_end_contract']) : null, //belom
                            'karyawan_isuser' => $isUser === true ? '1' : '0',
                            'karyawan_contract_end' => convertDateStringToYYYYMMDD($result[$x]['karyawan_end_contract']), //belom
                            'karyawan_bpjstkno' => $result[$x]['karyawan_isbpjstk'] ? $result[$x]['karyawan_bpjstkno'] : null,
                            'karyawan_bpjstkdate' => $result[$x]['karyawan_isbpjstk'] ? convertDateStringToYYYYMMDD($result[$x]['karyawan_bpjstkdate']) : null,
                            'karyawan_isbpjstk' => $result[$x]['karyawan_isbpjstk'],
                            'karyawan_bpjskesno' => $result[$x]['karyawan_isbpjskes'] ? $result[$x]['karyawan_bpjskesno'] : null,
                            'karyawan_bpjskesdate' => $result[$x]['karyawan_isbpjskes'] ? convertDateStringToYYYYMMDD($result[$x]['karyawan_bpjskesdate']) : null,
                            'karyawan_isbpjskes' => $result[$x]['karyawan_isbpjskes'],
                            // 'st_tunjangan_id' => $tunjanganId === '' ? null : $tunjanganId,
                            'st_penggajian_id' => $penggajianId,
                            // 'st_potongan_id' => $potonganId === '' ? null : $potonganId,
                            // 'st_leave_id' => empty($leaveId) ? null : json_encode($leaveId),
                            'st_attendance_id' => $attendanceId,
                            'karyawan_bpjskeslainnya' => $karyawan_bpjskeslainnya,
                            'karyawan_bpjstklainnya' => $karyawan_bpjstklainnya,
                            'karyawan_code_objekpajak' => '21-100-01'
                        ]);

                        $karyawanCreated[] = $createKaryawan->karyawan_id;
                        $karyawanCreatedForSetting[] = [
                            'karyawan_id' => $createKaryawan->karyawan_id,
                            'karyawan_gender' => $result[$x]['karyawan_gender'],
                            'ms_karyawandivisi_id' => $divisi_id,
                            'ms_karyawanjabatan_id' => $jabatan_id,
                        ];

                        KaryawanMasakerjaModel::create([
                            'ms_karyawan_id' => $createKaryawan->karyawan_id,
                            'ms_ptkp_id' => $createKaryawan->ms_ptkp_id,
                            'ms_user_id' => $user_id,
                            'ms_wajibpajak_id' => $wajibpajak_id,
                            'karyawanmasakerja_status' => $createKaryawan->karyawan_status,
                            'karyawanmasakerja_calculation_method' => $createKaryawan->karyawan_calculation_method,
                            'karyawanmasakerja_salary' => $createKaryawan->karyawan_salary,
                            'karyawanmasakerja_position' => capitalizeFirstLetterOfWords($result[$x]['karyawan_jabatan']),
                            'karyawanmasakerja_nik' => $createKaryawan->karyawan_nik,
                            'karyawanmasakerja_npwp' => $createKaryawan->karyawan_npwp,
                            'ms_objekpajak_code' => '21-100-01',
                            'karyawanmasakerja_contract_begin' => $createKaryawan->karyawan_contract_begin,
                            'karyawanmasakerja_data' => json_encode($createKaryawan),
                            'karyawanmasakerja_contract_end' => $createKaryawan->karyawan_contract_end,
                        ]);
                    }

                    if($isUser === true) {
                        $countKaryawan = KaryawanModel::where('karyawan_email', $result[$x]['karyawan_email'])->count();

                        NotificationModel::create([
                            'notification_title' => 'Rekkaa - Undangan Dari '.$wajibpajak->wajibpajak_name,
                            'notification_type' => 'EMAIL',
                            'notification_from' => env("MAIL_FROM_ADDRESS"),
                            'notification_to' => $result[$x]['karyawan_email'],
                            'ms_user_id' => $user_id,
                            'ms_wajibpajak_id' => $wajibpajak_id,
                            'notification_view' => 'email.email-undangan-akun-karyawan',
                            'notification_data' => json_encode([
                                'entity' => [
                                    'name' => $wajibpajak->wajibpajak_name,
                                    'user_name' => capitalizeFirstLetterOfWords($result[$x]['karyawan_name']),
                                    'inviter_email' => $user->user_email,
                                    'forgot_token' => $generate_forgot_password,
                                    'total_entity' => $countKaryawan - 1,
                                ]
                            ])
                        ]);
                    }

    
                    $totalComplete = $totalComplete + 1;
                    $result[$x]['status'] = 'Sukses';
                    $result[$x]['remark'] = '-';
                }

                $leaveDetailsData = [];
                $potonganDetailsData = [];
                $tunjanganDetailsData = [];
    
                if($karyawanCreated) {
                    foreach($karyawanCreated as $karyawanId) {
                        foreach ($leaveId as $leave) {
                            $leaveDetailsData[] = [
                                'st_leave_id' => $leave,
                                'ms_karyawan_id' => $karyawanId,
                                'leavedetail_active' => true,
                            ];
                        }
        
                        foreach ($potonganId as $potongan) {
                            $potonganDetailsData[] = [
                                'st_potongankaryawan_id' => $potongan,
                                'ms_karyawan_id' => $karyawanId,
                                'stpotongankaryawandet_active' => true,
                            ];
                        }
        
                        foreach ($tunjanganId as $tunjangan) {
                            $tunjanganDetailsData[] = [
                                'st_tunjangankaryawan_id' => $tunjangan,
                                'ms_karyawan_id' => $karyawanId,
                                'sttunjangankaryawandet_active' => true,
                            ];
                        }
                    }
                }
    
                if(!empty($leaveDetailsData)) {
                    SettingLeaveDetailModel::insert($leaveDetailsData);
                }
    
                if(!empty($potonganDetailsData)) {
                    SettingPotonganKaryawanDetailModel::insert($potonganDetailsData);
                }
                
                if(!empty($tunjanafganDetailsData)) {
                    SettingTunjanganKaryawanDetailModel::insert($tunjanganDetailsData);
                }

                // update announcement if exist
                if(count($announcementdatas) > 0) {
                    foreach($announcementdatas as $andata) {
                        SettingAnnouncementModel::
                        where(['announcement_id' => $andata['announcement_id']])
                        ->update([
                            'announcement_karyawan' => array_merge($andata['announcement_karyawan'], $karyawanCreated),
                        ]);
                    }
                }
            }

            if($karyawanCreatedForSetting) {
                // processed the allowance setting not all employees
                $sttunjangan = SettingTunjanganKaryawanModel::where([
                    'sttunjangankaryawan_active' => 1,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])
                ->whereIn('sttunjangankaryawan_used_for', [2,3,4,5])->get();
                if($sttunjangan) {
                    $tempdata_tunjangan = $this->sttunjangan_tempdata($sttunjangan, $karyawanCreatedForSetting);
                    if($tempdata_tunjangan) {
                        SettingTunjanganKaryawanDetailModel::insert($tempdata_tunjangan);
                    }
                }

                // processed the deduction setting not all employees
                $stpotongan = SettingPotonganKaryawanModel::where([
                    'stpotongankaryawan_active' => 1,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])
                ->whereIn('stpotongankaryawan_used_for', [2,3,4,5])->get();
                if($stpotongan) {
                    $tempdata_potongan = $this->stpotongan_tempdata($stpotongan, $karyawanCreatedForSetting);
                    if($tempdata_potongan) {
                        SettingPotonganKaryawanDetailModel::insert($tempdata_potongan);
                    }
                }

                // processed the leave setting not all employees
                $stleave = SettingLeaveModel::where([
                    'leave_status_active' => true,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])
                ->whereIn('leave_used_for', [2,3,4,5])->get();
                if($stleave) {
                    $tempdata_leave = $this->stleave_tempdata($stleave, $karyawanCreatedForSetting);
                    if($tempdata_leave) {
                        SettingLeaveDetailModel::insert($tempdata_leave);
                    }
                }
            }

            // $createHistory = HistoryImportModel::create([
            //     'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
            //     'ms_user_id' => session()->get('user_data')['user_id'],
            //     'historyimport_type' => "EMPLOYEE",
            //     'historyimport_date' => date('Y-m-d H:i:s'),
            //     'historyimport_detail_import' => $totalComplete . ' Sukses, ' . $totalFail . ' Gagal', 
            //     'historyimport_content' => json_encode($result),
            //     'historyimport_file_name' => $originalFileName
            // ]);

            // end
            HistoryImportModel::where(['historyimport_id' => $getHistory->historyimport_id])
            ->update([
                'historyimport_status' => 'COMPLETED', 
                'historyimport_detail_import' => $totalComplete . ' Sukses, ' . $totalFail . ' Gagal', 
                'historyimport_content' => json_encode($result),
            ]);
            
            DB::commit();
            unlink(public_path('uploads/'). $filename); // remove file
            // return response()->json([
            //     'success' => true,
            //     'message' => 'Berhasil import data karyawan.',
            //     'result' => $result,
            //     'totalsuccess' => $totalComplete,
            //     'totalfail'=> $totalFail,
            //     'totaldata' => $totalComplete + $totalFail,
            //     'history' => $createHistory->historyimport_id,
            // ], 200);
        } catch (\Exception $e) {
            // unlink(public_path('uploads/'). $filename); // remove file
            Log::error('Error occurred: ' . $e->getMessage());
            DB::rollback();
            // something went wrong
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function generateHistory(Request $request) 
    {
        try {
            // Get the file path from the 'public' directory
            $excelFilePath = public_path('assets/import/Template_Import_Karyawan.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $unixtime = time();
            $title = 'Riwayat_Impor_Karyawan_'.$unixtime;

            $spreadsheet = $reader->load($excelFilePath);

            $sheetSpv = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Referensi Data Supervisor');
            $spreadsheet->addSheet($sheetSpv, 2);

            $supervisor = KaryawanModel::where('ms_wajibpajak_id', session()->get('wajibpajak_current')['wajibpajak_id'])
                ->where('karyawan_active', 1)
                ->where('karyawan_status', '!=', 'NONKARYAWAN')
                ->orderBy('karyawan_enid', 'ASC')
                ->with(['jabatan'])
                ->get();

            // return $supervisor;

            // Get the specified sheet by title
            $htmlString = view('user.master.karyawan.import.profil.import-supervisor', ['title' => 'Referensi Data Supervisor', 'supervisor' => $supervisor])->render();
            $dom1 = new DOMDocument();
            $dom1->loadHTML($htmlString);
            $table1 = $dom1->getElementsByTagName('table')->item(0);

            $rowIndex1 = 1;
            foreach ($table1->getElementsByTagName('tr') as $row) {
                $cellIndex = 1;
                foreach ($row->getElementsByTagName('th') as $header) {
                    // Extract the text content without HTML tags
                    $headerText = strip_tags($header->nodeValue);
                    
                    // Set the cell value
                    $sheetSpv->setCellValueByColumnAndRow($cellIndex, $rowIndex1, $headerText);
                    
                    // Apply bold font style to the cell
                    $sheetSpv->getStyleByColumnAndRow($cellIndex, $rowIndex1)->getFont()->setBold(true);
                    
                    $cellIndex++;
                }
            
                // Skip the first row (headers row) when iterating through data rows
                if ($rowIndex1 === 1) {
                    $rowIndex1++;
                    continue;
                }
            
                foreach ($row->getElementsByTagName('td') as $cell) {
                    // Extract the text content without HTML tags
                    $cellValue = strip_tags($cell->nodeValue);
                    
                    // Set the cell value
                    $sheetSpv->setCellValueByColumnAndRow($cellIndex, $rowIndex1, $cellValue);
                    
                    $cellIndex++;
                }
                $rowIndex1++;
            }

            $boldFontStyle = [
                'font' => ['bold' => true],
                'borders' => [
                    'top' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ];

            $sheetImpor = $spreadsheet->createSheet();
            $sheetImpor->setTitle('Impor Profil Karyawan');
            $sheetImpor->setCellValue('A1', 'Profil Karyawan');
            $sheetImpor->mergeCells('A1:N1');
            $sheetImpor->getStyle('A1:N1')->applyFromArray($boldFontStyle);
            $sheetImpor->setCellValue('O1', 'Informasi Kontrak');
            $sheetImpor->mergeCells('O1:Z1');
            $sheetImpor->getStyle('O1:Z1')->applyFromArray($boldFontStyle);
            $sheetImpor->setCellValue('AA1', 'Informasi Impor');
            $sheetImpor->mergeCells('AA1:AB1');
            $sheetImpor->getStyle('AA1:AB1')->applyFromArray($boldFontStyle);
            // Center align cell A1
            $sheetImpor->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            // Center align cell O1
            $sheetImpor->getStyle('O1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            $sheetImpor->getStyle('AA1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheetImpor->setCellValue('A2', 'Nama Karyawan*');
            $sheetImpor->setCellValue('B2', 'Id Karyawan*');
            $sheetImpor->setCellValue('C2', 'NIK*');
            $sheetImpor->setCellValue('D2', 'Nomor Tlp');
            $sheetImpor->setCellValue('E2', 'NPWP*');
            $sheetImpor->setCellValue('F2', 'Email');
            $sheetImpor->setCellValue('G2', 'Tempat Lahir');
            $sheetImpor->setCellValue('H2', 'Tanggal Lahir');
            $sheetImpor->setCellValue('I2', 'Jenis Kelamin*');
            $sheetImpor->setCellValue('J2', 'Alamat');
            $sheetImpor->setCellValue('K2', 'Status Perkawinan*');
            $sheetImpor->setCellValue('L2', 'Tambahkan Sebagai User');
            $sheetImpor->setCellValue('M2', 'Status Karyawan*');
            $sheetImpor->setCellValue('N2', 'Tanggal Masuk*');
            $sheetImpor->setCellValue('O2', 'Tanggal Berakhir Kontrak');
            $sheetImpor->setCellValue('P2', 'Divisi');
            $sheetImpor->setCellValue('Q2', 'Jabatan');
            $sheetImpor->setCellValue('R2', 'Id Supervisor');
            $sheetImpor->setCellValue('S2', 'Gaji Pokok*');
            $sheetImpor->setCellValue('T2', 'Metode Pph 21*');
            $sheetImpor->setCellValue('U2', 'Kode Bank');
            $sheetImpor->setCellValue('V2', 'Nomor Rekening');
            $sheetImpor->setCellValue('W2', 'No. BPJS Tenaga Kerja');
            $sheetImpor->setCellValue('X2', 'Tgl. Berlaku BPJS TK');
            $sheetImpor->setCellValue('Y2', 'No. BPJS Kesehatan');
            $sheetImpor->setCellValue('Z2', 'Tgl. Berlaku BPJS Kesehatan');
            $sheetImpor->setCellValue('AA2', 'Status');
            $sheetImpor->setCellValue('AB2', 'Catatan');
            // $sheetImpor->getStyle('A2:AB2')->applyFromArray($boldFontStyle);

            $id = $request->input('id');

            $getHistory = HistoryImportModel::find($id);

            $content = json_decode($getHistory['historyimport_content']);

            $rowIndex = 3;  

            foreach($content as $item) {
                $listImpor = [
                    'A' => isset($item->karyawan_name) ? $item->karyawan_name : '',
                    'B' => isset($item->karyawan_enid) ? $item->karyawan_enid : '',
                    'C' => isset($item->karyawan_nik) ? $item->karyawan_nik : '',
                    'D' => isset($item->karyawan_phone) ? $item->karyawan_phone : '',
                    'E' => isset($item->karyawan_npwp) ? $item->karyawan_npwp : '',
                    'F' => isset($item->karyawan_email) ? $item->karyawan_email : '',
                    'G' => isset($item->karyawan_birthplace) ? $item->karyawan_birthplace : '',
                    'H' => isset($item->karyawan_birthdate) ? $item->karyawan_birthdate : '',
                    'I' => isset($item->karyawan_gender) ? $item->karyawan_gender : '',
                    'J' => isset($item->karyawan_address) ? $item->karyawan_address : '',
                    'K' => isset($item->karyawan_ptkp) ? $item->karyawan_ptkp : '',
                    'L' => isset($item->karyawan_create_user) ? $item->karyawan_create_user : '',
                    'M' => isset($item->karyawan_contract_status) ? $item->karyawan_contract_status : '',
                    'N' => isset($item->karyawan_start_contract) ? $item->karyawan_start_contract : '',
                    'O' => isset($item->karyawan_end_contract) ? $item->karyawan_end_contract : '',
                    "P" => isset($item->karyawan_divisi) ? $item->karyawan_divisi : '',
                    "Q" => isset($item->karyawan_jabatan) ? $item->karyawan_jabatan : '',
                    "R" => isset($item->karyawan_manager_id) ? $item->karyawan_manager_id : '',
                    "S" => isset($item->karyawan_salary) ? $item->karyawan_salary : '',
                    "T" => isset($item->karyawan_ppn_method) ? $item->karyawan_ppn_method : '',
                    "U" => isset($item->karyawan_code_bank) ? $item->karyawan_code_bank : '',
                    "V" => isset($item->karyawan_bank_account) ? $item->karyawan_bank_account : '',
                    "W" => isset($item->karyawan_bpjstkno) ? $item->karyawan_bpjstkno : '',
                    "X" => isset($item->karyawan_bpjstkdate) ? $item->karyawan_bpjstkdate : '',
                    "Y" => isset($item->karyawan_bpjskesno) ? $item->karyawan_bpjskesno : '',
                    "Z" => isset($item->karyawan_bpjskesdate) ? $item->karyawan_bpjskesdate : '',
                    'AA' => isset($item->status) ? $item->status : 'Sukses',
                    'AB' => isset($item->remark) ? $item->remark : '-'
                ];

                foreach ($listImpor as $column => $value) {
                    $sheetImpor->setCellValueExplicit($column . $rowIndex, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                }

                // Increment the row index for the next row of data
                $rowIndex++;
            }

            $startRow = 3;

            $listGender = '"L,P"';
            $genderValidation = $sheetImpor->getCell('I' . $startRow)->getDataValidation();
            $genderValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $genderValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $genderValidation->setShowDropDown(true);
            $genderValidation->setFormula1($listGender);

            // $listPtkp = '"RK-PT-ID-1,RK-PT-ID-2,RK-PT-ID-3,RK-PT-ID-4,RK-PT-ID-5,RK-PT-ID-6,RK-PT-ID-7,RK-PT-ID-8,RK-PT-ID-9,RK-PT-ID-10,RK-PT-ID-11,RK-PT-ID-12"';
            // $ptkpValidation = $sheetImpor->getCell('K' . $startRow)->getDataValidation();
            // $ptkpValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            // $ptkpValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            // $ptkpValidation->setShowDropDown(true);
            // $ptkpValidation->setFormula1($listPtkp);

            $listTrueFalse = '"Ya,Tidak"';
            $userValidation = $sheetImpor->getCell('L' . $startRow)->getDataValidation();
            $userValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $userValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $userValidation->setShowDropDown(true);
            $userValidation->setFormula1($listTrueFalse);

            $listStatus = '"PERCOBAAN,KONTRAK,TETAP"';
            $statusValidation = $sheetImpor->getCell('M' . $startRow)->getDataValidation();
            $statusValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $statusValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $statusValidation->setShowDropDown(true);
            $statusValidation->setFormula1($listStatus);

            $listMethod = '"GROSS,NETT,GROSS_UP"';
            $methodValidation = $sheetImpor->getCell('T' . $startRow)->getDataValidation();
            $methodValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $methodValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $methodValidation->setShowDropDown(true);
            $methodValidation->setFormula1($listMethod);

            // $listKaryawan = array(); // Create an array to store the values

            // foreach ($supervisor as $karyawan) {
            //     $listKaryawan[] = $karyawan->karyawan_enid; // Add each value to the array
            // }
            
            // $listKaryawanString = '"' . implode(',', $listKaryawan) . '"'; // Combine the values with commas and enclose in double quotes

            // $spvValidation = $sheetImpor->getCell('R' . $startRow)->getDataValidation();
            // $spvValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            // $spvValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            // $spvValidation->setShowDropDown(true);
            // $spvValidation->setFormula1($listKaryawanString);

            // $listBank = '"002,003,008,009,427,011,013,014,016,019,020,022,023,026,028,030,031,032,033,034,036,037,039,040,041,042,045,046,047,048,050,052,053,054,057,058,059,060,061,067,068,069,076,087,088,089,093,095,130,131,132,133,134,135,145,146,147,151,152,153,157,159,161,162,164,166,167,200,212,213,405,422,426,427,441,451,459,466,472,484,485,490,491,494,498,501,503,506,513,517,520,521,525,526,531,523,535,536,542,547,548,553,555,566,558,559,562,564,567,945,946,947,948,949,950,425,688,789,911"';
            // $bankValidation = $sheetImpor->getCell('U' . $startRow)->getDataValidation();
            // $bankValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            // $bankValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            // $bankValidation->setShowDropDown(true);
            // $bankValidation->setFormula1($listBank);

            for ($row = $startRow + 1; $row <= 52; $row++) {
                $cellI = $sheetImpor->getCell('I' . $row);
                $cellI->setDataValidation(clone $genderValidation);
                
                // $cellK = $sheetImpor->getCell('K' . $row);
                // $cellK->setDataValidation(clone $ptkpValidation);
                
                $cellL = $sheetImpor->getCell('L' . $row);
                $cellL->setDataValidation(clone $userValidation);
                
                $cellM = $sheetImpor->getCell('M' . $row);
                $cellM->setDataValidation(clone $statusValidation);
                
                $cellT = $sheetImpor->getCell('T' . $row);
                $cellT->setDataValidation(clone $methodValidation);
                
                // $cellU = $sheetImpor->getCell('U' . $row);
                // $cellU->setDataValidation(clone $bankValidation);
                
                // $cellR = $sheetImpor->getCell('R' . $row);
                // $cellR->setDataValidation(clone $spvValidation);
            }

            for ($col = 'A'; $col <= 'Z'; $col++) {
                if($col === 'AC') {
                    break;
                }
                $sheetImpor->getStyle($col . '2')->applyFromArray($boldFontStyle);
                $sheetImpor->getStyle($col . ':' . $col)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            }


            foreach ($spreadsheet->getSheetNames() as $sheetName) {
                $sheet = $spreadsheet->getSheetByName($sheetName);

                if($sheetName === 'Impor Profil Karyawan') {
                    foreach (range('A', 'AB') as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                    foreach (range('B', 'Z') as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                } else if($sheetName !== 'Panduan Pengguna') {
                    foreach (range('A', $sheet->getHighestDataColumn()) as $column) {
                        $sheet->getColumnDimension($column)->setAutoSize(true);
                    }
                }
            }
            
            $writer = new Xls($spreadsheet);

            $filename = $title.'.xls';
            $location = public_path('assets/export/');
            ob_start();
            $writer->save($location.$filename);
            // $xlsData = ob_get_contents();
            ob_end_clean();

            return url('/assets/export/'.$filename);
        } catch (Error $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function importHistory(Request $request)
    {
        $page = ($request->input('page')) ? intval($request->input('page')) : 1;
        $perPage = ($request->input('per_page')) ? intval($request->input('per_page')) : 10;
        
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        
        // Get the start_date and end_date inputs
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        
        $dataHistory = HistoryImportModel::where('historyimport_type', 'EMPLOYEE')
            ->where('tr_history_import.ms_wajibpajak_id', $wajibpajak_id)
            ->orderBy('historyimport_date', 'DESC')
            ->leftjoin('mr_user_wajib_pajak', 'tr_history_import.ms_user_id', '=', 'mr_user_wajib_pajak.ms_user_id')
            ->select('tr_history_import.historyimport_date', 'tr_history_import.ms_user_id', 'tr_history_import.historyimport_status'
            , 'tr_history_import.historyimport_id', 'tr_history_import.historyimport_file_name', 'tr_history_import.historyimport_detail_import'
            , 'tr_history_import.historyimport_originfile_name', 'mr_user_wajib_pajak.userwajibpajak_name');
        
        // Apply date filtering if start_date and end_date are provided
        if ($start_date && $end_date) {
            $dataHistory->whereBetween('tr_history_import.historyimport_date', [$start_date, $end_date]);
        }
        
        $dataHistory = $dataHistory->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data Setting Kehadiran.',
            'data' => $dataHistory->items(),
            'draw' => $request->input('draw'),
            'recordsFiltered' => $dataHistory->total(),
            'recordsTotal' => $dataHistory->total(),
        ], 200);
    }

    public function profile($karyawanId, Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $karyawan = KaryawanModel::
        select("*"
        , DB::raw("(SELECT json_agg(ttable) 
        FROM (
            SELECT std.st_tunjangankaryawan_id, stk.sttunjangankaryawan_name, stk.sttunjangankaryawan_used_for
            FROM st_tunjangan_karyawan_detail as std
            JOIN st_tunjangan_karyawan as stk ON std.st_tunjangankaryawan_id = stk.sttunjangankaryawan_id
            WHERE std.ms_karyawan_id = ms_karyawan.karyawan_id
                AND std.sttunjangankaryawandet_active = '1'
                AND stk.sttunjangankaryawan_active = '1'
        ) as ttable) as tunjangan_json")
        , DB::raw("(SELECT json_agg(ttable) 
        FROM (
            SELECT spd.st_potongankaryawan_id, spk.stpotongankaryawan_name, spk.stpotongankaryawan_used_for
            FROM st_potongan_karyawan_detail as spd
            JOIN st_potongan_karyawan as spk ON spd.st_potongankaryawan_id = spk.stpotongankaryawan_id
            WHERE spd.ms_karyawan_id = ms_karyawan.karyawan_id
                AND spd.stpotongankaryawandet_active = '1'
                AND spk.stpotongankaryawan_active = '1'
        ) as ttable) as potongan_json")
        , DB::raw("(SELECT row_to_json(ptable) 
        FROM (
            SELECT st_bpjs_karyawan.*
            FROM st_bpjs_karyawan
            WHERE ms_wajibpajak_id = {$wajibpajak_id}
            AND stbpjskaryawan_active = '1'
            ORDER BY stbpjskaryawan_id ASC
        )
        as ptable) as setting_bpjspegawai_json"
        ))
        ->with(['bank', 'penggajian', 'ptkp', 'manager', 'divisi', 'jabatan', 'attendance', 'country'])
        ->where([
            'karyawan_id' => $karyawanId, 
            'karyawan_active' => 1,
            // 'karyawan_contract_end' => null,
            'ms_wajibpajak_id' => $wajibpajak_id,
            // 'ms_user_id' => $user_id,
        ])->first();
        if(!$karyawan) {
            abort(404);
        }
        // dd($karyawan->tunjangan_json);
        
        // $setting = DB::select(DB::raw("SELECT (SELECT json_agg(ptable) 
        // FROM (
        //     SELECT st_tunjangan_karyawan.*
        //     FROM st_tunjangan_karyawan
        //     WHERE ms_wajibpajak_id = {$wajibpajak_id}
        //     AND sttunjangankaryawan_active = '1'
        //     ORDER BY sttunjangankaryawan_id ASC
        // )
        // as ptable) as setting_tunjangankaryawan_json
        // , (SELECT row_to_json(ptable) 
        // FROM (
        //     SELECT st_bpjs_karyawan.*
        //     FROM st_bpjs_karyawan
        //     WHERE ms_wajibpajak_id = {$wajibpajak_id}
        //     AND stbpjskaryawan_active = '1'
        //     ORDER BY stbpjskaryawan_id ASC
        // )
        // as ptable) as setting_bpjspegawai_json
        // "));
        // $karyawan_infofield = KaryawanInfoFieldModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_user_id' => $user_id])->first();
        $stbpjskaryawan_data = ($karyawan->setting_bpjspegawai_json) ? json_decode($karyawan->setting_bpjspegawai_json) : null;
        $data = [
            'title' => 'Profile Karyawan',
            'content' => 'user.master.karyawan.profile',
            'karyawan' => $karyawan,
            // 'karyawan_infofield' => $karyawan_infofield,
            // 'sttunjangankaryawan_data' => ($setting[0]->setting_tunjangankaryawan_json) ? json_decode($setting[0]->setting_tunjangankaryawan_json) : null,
            'stbpjskaryawan_data' => ($stbpjskaryawan_data) ? json_decode($stbpjskaryawan_data->stbpjskaryawan_value) : null,
        ];
        // dd($data);
        if($request->ajax()) {
            return view('user.master.karyawan.profile', $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

     /**
     * Display a listing of the non active resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function nonactive(Request $request)
    {   
        $data = [
            'title' => 'Data Karyawan',
            'content' => 'user.master.karyawan.nonaktif.index',
        ];
        // dd($calculate_pph21 / 12);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    

    public function sttunjangan_tempdata($sttunjangan, $karyawanCreatedForSetting)
    {
        $tjkaryawan_female = [];
        $tjkaryawan_male = [];
        $tjkaryawan_divisi = [];
        $tjkaryawan_jabatan = [];
        foreach($sttunjangan as $sttj) {
            if($sttj->sttunjangankaryawan_used_for == 2) { // perempuan
                
                foreach($karyawanCreatedForSetting as $krst) {
                    if($krst['karyawan_gender'] == 'P') {
                        $tjkaryawan_female[] = [
                            'st_tunjangankaryawan_id' => $sttj->sttunjangankaryawan_id,
                            'ms_karyawan_id' => $krst['karyawan_id'],
                            'sttunjangankaryawandet_active' => true,
                        ];
                    }
                }
            } else if($sttj->sttunjangankaryawan_used_for == 3) { // laki-laki
                
                foreach($karyawanCreatedForSetting as $krst) {
                    if($krst['karyawan_gender'] == 'L') {
                        $tjkaryawan_male[] = [
                            'st_tunjangankaryawan_id' => $sttj->sttunjangankaryawan_id,
                            'ms_karyawan_id' => $krst['karyawan_id'],
                            'sttunjangankaryawandet_active' => true,
                        ];
                    }
                }
            } else if($sttj->sttunjangankaryawan_used_for == 4) { // divisi
                $arr_divjab_ids = explode(',', $sttj->sttunjangankaryawan_division_jabatan);
                foreach($karyawanCreatedForSetting as $krst) {
                    if(in_array($krst['ms_karyawandivisi_id'], $arr_divjab_ids)) {
                        $tjkaryawan_divisi[] = [
                            'st_tunjangankaryawan_id' => $sttj->sttunjangankaryawan_id,
                            'ms_karyawan_id' => $krst['karyawan_id'],
                            'sttunjangankaryawandet_active' => true,
                        ];
                    }
                }
            } else if($sttj->sttunjangankaryawan_used_for == 5) { // jabatan
                $arr_divjab_ids = explode(',', $sttj->sttunjangankaryawan_division_jabatan);
                foreach($karyawanCreatedForSetting as $krst) {
                    if(in_array($krst['ms_karyawanjabatan_id'], $arr_divjab_ids)) {
                        $tjkaryawan_jabatan[] = [
                            'st_tunjangankaryawan_id' => $sttj->sttunjangankaryawan_id,
                            'ms_karyawan_id' => $krst['karyawan_id'],
                            'sttunjangankaryawandet_active' => true,
                        ];
                    }
                }
            } else if($sttj->sttunjangankaryawan_used_for == 1) { // all
                foreach($karyawanCreatedForSetting as $krst) {
                    $tjkaryawan_jabatan[] = [
                        'st_tunjangankaryawan_id' => $sttj->sttunjangankaryawan_id,
                        'ms_karyawan_id' => $krst['karyawan_id'],
                        'sttunjangankaryawandet_active' => true,
                    ];
                }
            }
        }

        $tempdata_tunjangan = array_merge($tjkaryawan_female, $tjkaryawan_male, $tjkaryawan_divisi, $tjkaryawan_jabatan);
        return $tempdata_tunjangan;
    }

    public function stpotongan_tempdata($stpotongan, $karyawanCreatedForSetting)
    {
        $ptkaryawan_female = [];
        $ptkaryawan_male = [];
        $ptkaryawan_divisi = [];
        $ptkaryawan_jabatan = [];
        foreach($stpotongan as $sttj) {
            if($sttj->stpotongankaryawan_used_for == 2) { // perempuan
                
                foreach($karyawanCreatedForSetting as $krst) {
                    if($krst['karyawan_gender'] == 'P') {
                        $ptkaryawan_female[] = [
                            'st_potongankaryawan_id' => $sttj->stpotongankaryawan_id,
                            'ms_karyawan_id' => $krst['karyawan_id'],
                            'stpotongankaryawandet_active' => true,
                        ];
                    }
                }
            } else if($sttj->stpotongankaryawan_used_for == 3) { // laki-laki
                
                foreach($karyawanCreatedForSetting as $krst) {
                    if($krst['karyawan_gender'] == 'L') {
                        $ptkaryawan_male[] = [
                            'st_potongankaryawan_id' => $sttj->stpotongankaryawan_id,
                            'ms_karyawan_id' => $krst['karyawan_id'],
                            'stpotongankaryawandet_active' => true,
                        ];
                    }
                }
            } else if($sttj->stpotongankaryawan_used_for == 4) { // divisi
                $arr_divjab_ids = explode(',', $sttj->stpotongankaryawan_division_jabatan);
                foreach($karyawanCreatedForSetting as $krst) {
                    if(in_array($krst['ms_karyawandivisi_id'], $arr_divjab_ids)) {
                        $ptkaryawan_divisi[] = [
                            'st_potongankaryawan_id' => $sttj->stpotongankaryawan_id,
                            'ms_karyawan_id' => $krst['karyawan_id'],
                            'stpotongankaryawandet_active' => true,
                        ];
                    }
                }
            } else if($sttj->stpotongankaryawan_used_for == 5) { // jabatan
                $arr_divjab_ids = explode(',', $sttj->stpotongankaryawan_division_jabatan);
                foreach($karyawanCreatedForSetting as $krst) {
                    if(in_array($krst['ms_karyawanjabatan_id'], $arr_divjab_ids)) {
                        $ptkaryawan_jabatan[] = [
                            'st_potongankaryawan_id' => $sttj->stpotongankaryawan_id,
                            'ms_karyawan_id' => $krst['karyawan_id'],
                            'stpotongankaryawandet_active' => true,
                        ];
                    }
                }
            } else if($sttj->stpotongankaryawan_used_for == 1) { // all
                foreach($karyawanCreatedForSetting as $krst) {
                    $ptkaryawan_jabatan[] = [
                        'st_potongankaryawan_id' => $sttj->stpotongankaryawan_id,
                        'ms_karyawan_id' => $krst['karyawan_id'],
                        'stpotongankaryawandet_active' => true,
                    ];
                }
            }
        }

        $tempdata_potongan = array_merge($ptkaryawan_female, $ptkaryawan_male, $ptkaryawan_divisi, $ptkaryawan_jabatan);
        return $tempdata_potongan;
    }

    public function stleave_tempdata($stleave, $karyawanCreatedForSetting)
    {
        $lvkaryawan_female = [];
        $lvkaryawan_male = [];
        $lvkaryawan_divisi = [];
        $lvkaryawan_jabatan = [];
        foreach($stleave as $sttj) {
            if($sttj->leave_used_for == 2) { // perempuan
                
                foreach($karyawanCreatedForSetting as $krst) {
                    if($krst['karyawan_gender'] == 'P') {
                        $lvkaryawan_female[] = [
                            'st_leave_id' => $sttj->leave_id,
                            'ms_karyawan_id' => $krst['karyawan_id'],
                            'leavedetail_active' => true,
                        ];
                    }
                }
            } else if($sttj->leave_used_for == 3) { // laki-laki
                
                foreach($karyawanCreatedForSetting as $krst) {
                    if($krst['karyawan_gender'] == 'L') {
                        $lvkaryawan_male[] = [
                            'st_leave_id' => $sttj->leave_id,
                            'ms_karyawan_id' => $krst['karyawan_id'],
                            'leavedetail_active' => true,
                        ];
                    }
                }
            } else if($sttj->leave_used_for == 4) { // divisi
                $arr_divjab_ids = explode(',', $sttj->leave_division_jabatan);
                foreach($karyawanCreatedForSetting as $krst) {
                    if(in_array($krst['ms_karyawandivisi_id'], $arr_divjab_ids)) {
                        $lvkaryawan_divisi[] = [
                            'st_leave_id' => $sttj->leave_id,
                            'ms_karyawan_id' => $krst['karyawan_id'],
                            'leavedetail_active' => true,
                        ];
                    }
                }
            } else if($sttj->leave_used_for == 5) { // jabatan
                $arr_divjab_ids = explode(',', $sttj->leave_division_jabatan);
                foreach($karyawanCreatedForSetting as $krst) {
                    if(in_array($krst['ms_karyawanjabatan_id'], $arr_divjab_ids)) {
                        $lvkaryawan_jabatan[] = [
                            'st_leave_id' => $sttj->leave_id,
                            'ms_karyawan_id' => $krst['karyawan_id'],
                            'leavedetail_active' => true,
                        ];
                    }
                }
            } else if($sttj->leave_used_for == 1) { // all
                foreach($karyawanCreatedForSetting as $krst) {
                    $lvkaryawan_jabatan[] = [
                        'st_leave_id' => $sttj->leave_id,
                        'ms_karyawan_id' => $krst['karyawan_id'],
                        'leavedetail_active' => true,
                    ];
                }
            }
        }

        $tempdata_leave = array_merge($lvkaryawan_female, $lvkaryawan_male, $lvkaryawan_divisi, $lvkaryawan_jabatan);
        return $tempdata_leave;
    }
}

class ChunkReadFilter implements IReadFilter
{
    private $startRow = 0;

    private $endRow = 0;

    /**
     * Set the list of rows that we want to read.
     *
     * @param mixed $startRow
     * @param mixed $chunkSize
     */
    public function setRows($startRow, $chunkSize)
    {
        $this->startRow = $startRow;
        $this->endRow = $startRow + $chunkSize;
    }

    public function readCell($column, $row, $worksheetName = '')
    {
        //  Only read the heading row, and the rows that are configured in            $this->_startRow and $this->_endRow
        if (($row == 1) || ($row >= $this->startRow && $row <   $this->endRow)) {
            return true;
        }

        return false;
    }
}