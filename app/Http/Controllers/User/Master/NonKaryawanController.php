<?php
namespace App\Http\Controllers\User\Master;

use App\Http\Controllers\Controller;
use App\Jobs\ImportJob;
use App\Model\Master\KaryawanKalkulasiModel;
use App\Model\Master\KaryawanMasakerjaModel;
use App\Model\Master\KaryawanModel;
use App\Libraries\AppUploadLibrary;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use Carbon\Carbon;
use DateTime;
use DOMDocument;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Helper\Html;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use App\Model\Transaction\HistoryImportModel;
use App\Model\Transaction\NotificationModel;
use App\Model\Master\PtkpModel;
use App\Model\Master\BankModel;
use App\Model\Master\KaryawanDivisiModel;
use App\Model\Master\KaryawanJabatanModel;
use App\Model\Master\ObjekPajakModel;
use App\Model\Master\KaryawanInfoFieldModel;
use App\Model\Master\WajibPajakModel;

class NonKaryawanController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Data Non Karyawan',
            'content' => 'user.master.non-karyawan.index',
        ];

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
        $setting = DB::select(DB::raw("SELECT (SELECT json_agg(ptable) 
        FROM (
            SELECT st_tunjangan_karyawan.*
            FROM st_tunjangan_karyawan
            WHERE ms_wajibpajak_id = {$wajibpajak_id}
            AND sttunjangankaryawan_active = '1'
            ORDER BY sttunjangankaryawan_id ASC
        )
        as ptable) as setting_tunjangankaryawan_json
        , (SELECT row_to_json(ptable) 
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
            'title' => 'Non Karyawan Baru',
            'content' => 'user.master.non-karyawan.create',
            'sttunjangankaryawan_data' => ($setting[0]->setting_tunjangankaryawan_json) ? json_decode($setting[0]->setting_tunjangankaryawan_json) : null,
            'stbpjskaryawan_data' => ($stbpjskaryawan_data) ? json_decode($stbpjskaryawan_data->stbpjskaryawan_value) : null,
            'karyawan_infofield' => $karyawan_infofield,
        ];

        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function edit($karyawanId, Request $request)
    {
        // dd(session()->get('wajibpajak_current')['wajibpajak_id']);
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $karyawan = KaryawanModel::
        select("*"
        , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_active='1' AND payroll_lock='1' LIMIT 1) payroll_islock")
        , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_active='1' AND payroll_status IN ('0','1','2') LIMIT 1) payroll_isexist"))
        ->with(['bank', 'ptkp', 'jabatan'])
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
        // dd($karyawan);
        
        $data = [
            'title' => 'Edit Karyawan',
            'content' => 'user.master.non-karyawan.edit',
            'karyawan' => $karyawan
        ];
        // dd($karyawan->manager);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function store(Request $request)
    {
        // echo json_encode($request->input('bpjs_tk'));
        // return json_encode($request->input('bpjs_tk'));
        $email = $request->input('karyawan_email');
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $is_user = $request->input('karyawan_isuser');
        
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
            'karyawan_npwp' => ['required',
                Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id) {
                    return $query->where([
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ])->where('karyawan_npwp', '<>', '00.000.000.0-000.000');;
                })
            ],
            'karyawan_name' => 'required',
            'jenis_transaksi' => 'required',
            'karyawan_email' => $email ? 'email' : '',
            'karyawan_citizenship' => 'required|in:WNI,WNA',
            'ptkp_id' => 'required',
            'karyawan_gender' => 'required',
            'karyawan_contract_begin' => 'required|date_format:d-m-Y',
            // 'karyawan_bankid' => 'required',
            // 'karyawan_banknokartu' => 'required',
            'karyawan_ismultiple' => 'required',
            'karyawan_calculation_method' =>  'required|in:GROSS,GROSS_UP,NETT',
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
        
        $this->validate($request, $rules, [
            'karyawan_enid.required' => 'ID Karyawan wajib diisi!',
            'karyawan_enid.unique' => 'ID Karyawan sudah terdaftar!',
            'karyawan_ismultiple.required' => 'Status Karyawan wajib dipilih!',
            'karyawan_npwp.required' => 'NPWP wajib diisi!',
            'karyawan_nik.required' => 'NIK wajib diisi!',
            'jenis_transaksi.required' => 'Objek Pajak wajib diisi!',
            'karyawan_nik.unique' => 'NIK sudah terdaftar! Silahkan gunakan NIK lainnya.',
            'karyawan_npwp.unique' => 'NPWP sudah terdaftar! Silahkan gunakan NPWP lainnya.',
            'karyawan_name.required' => 'Nama wajib diisi!',
            'karyawan_email.required' => 'Email wajib diisi!',
            'karyawan_email.email' => 'Format email tidak sesuai!',
            'karyawan_email.unique' => 'Email sudah digunakan. Silahkan gunakan email lainnya!',
            'karyawan_citizenship.required' => 'Kewarganegaraan wajib diisi!',
            'ptkp_id.required' => 'Status perkawinan wajib diisi!',
            'karyawan_gender.required' => 'Jenis Kelamin wajib diisi!',
            'karyawan_contract_begin.required' => 'Tgl. kontrak wajib diisi!',
            // 'karyawan_bankid.required' => 'Nama Bank wajib diisi!',
            // 'karyawan_banknokartu.required' => 'Nomor Kartu Bank wajib diisi!',
            'karyawan_photo.mimes' => 'Format gambar harus jpg atau png!',
            'karyawan_photo.max' => 'Ukuran gambar maksimal 250 Kb!',
            'karyawan_calculation_method.required' => 'Metode perhitungan wajib diisi!',
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
        
        $dt_cend = null;
        if($request->input('karyawan_contract_end')) {
            $dt_cend = \Carbon\Carbon::parse($request->input('karyawan_contract_end'));
        }

        
        // $manager_id = intval($request->input('karyawan_manager'));

        $wajibpajak = WajibPajakModel::select("*"
        , DB::raw("(SELECT row_to_json(mtable) 
        FROM (
            SELECT manager.karyawan_id, manager.karyawan_ismanager
            FROM ms_karyawan as manager
            WHERE manager.ms_wajibpajak_id = {$wajibpajak_id}
            LIMIT 1
        )
        as mtable) as manager_json")
        )->where(['wajibpajak_id' => $wajibpajak_id])->first();

        $total_entity = intval($wajibpajak->total_karyawan);

        DB::beginTransaction();
        try {
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

            $karyawan = KaryawanModel::create([
                'karyawan_enid' => $request->input('karyawan_enid'),
                'karyawan_nik' => $request->input('karyawan_nik'),
                'karyawan_npwp' => $request->input('karyawan_npwp'),
                'karyawan_name' => $request->input('karyawan_name'),
                'karyawan_birthdate' => ($dt) ? $dt->translatedFormat('Y-m-d') : null,
                'karyawan_birthplace' => $request->input('karyawan_birthplace'),
                'karyawan_contract_begin' => ($dt_cbegin) ? $dt_cbegin->translatedFormat('Y-m-d') : null,
                'karyawan_contract_end' => ($dt_cend) ? $dt_cend->translatedFormat('Y-m-d') : null,
                'karyawan_phone' => $request->input('karyawan_phone'),
                'karyawan_email' => $request->input('karyawan_email'),
                'karyawan_password' => $random_password,
                'karyawan_forgot_password' => $generate_forgot_password,
                'karyawan_citizenship' => $request->input('karyawan_citizenship'),
                'karyawan_gender' => $request->input('karyawan_gender'),
                'karyawan_address' => $request->input('karyawan_address'),
                'karyawan_status' => 'NONKARYAWAN',
                'karyawan_ismultiple' => ($request->input('karyawan_ismultiple') == '1') ? 1 : 0,
                'karyawan_calculation_method' => $request->input('karyawan_calculation_method'),
                'ms_ptkp_id' => $request->input('ptkp_id'),
                'ms_country_id' => ($request->input('karyawan_citizenship') == 'WNI') ? 100 : $request->input('country_id'),
                'ms_bank_id' => $request->input('karyawan_bankid'),
                'ms_wajibpajak_id' => $wajibpajak_id,
                'ms_user_id' => $wajibpajak->ms_user_id,
                'ms_karyawanjabatan_id' => $position_id,
                'karyawan_isuser' => ($is_user == '1') ? 1 : 0,
                'karyawan_bankno' => $request->input('karyawan_banknokartu'),
                'karyawan_photo' => $photo_url,
                'karyawan_code_objekpajak' =>  $request->input('jenis_transaksi'),
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
                'karyawanmasakerja_status' => 'NONKARYAWAN',
                'karyawanmasakerja_calculation_method' => $request->input('karyawan_calculation_method'),
                'karyawanmasakerja_position' => $request->input('karyawan_position'),
                'karyawanmasakerja_nik' => $request->input('karyawan_nik'),
                'karyawanmasakerja_npwp' => $request->input('karyawan_npwp'),
                'ms_objekpajak_code' => $request->input('jenis_transaksi'),
                'karyawanmasakerja_contract_begin' => $dt_cbegin->translatedFormat('Y-m-d'),
                'karyawanmasakerja_contract_end' => ($dt_cend) ? $dt_cend->translatedFormat('Y-m-d') : null,
                'karyawanmasakerja_data' => json_encode($karyawan),
            ]);

            if($wajibpajak->manager_json) {
                $manager = json_decode($wajibpajak->manager_json);
                if($manager->karyawan_ismanager != 1) {
                    KaryawanModel::where(['karyawan_id' => $manager->karyawan_id, 'ms_wajibpajak_id' => $wajibpajak_id])
                    ->update(['karyawan_ismanager' => 1]);
                }
            }

            if($is_user) {
                // insert tr_notification
                NotificationModel::create([
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
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data non karyawan berhasil disimpan',
                'data' => $karyawan
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($karyawanId, Request $request)
    {   
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $is_user = $request->input('karyawan_isuser');
        $email = $request->input('karyawan_email');
        $user_id = session()->get('user_data')['user_id'];

        $karyawan = KaryawanModel::select("*"
        , DB::raw("(SELECT COUNT(currkaryawan.*) FROM ms_karyawan as currkaryawan 
        WHERE currkaryawan.karyawan_email = ms_karyawan.karyawan_email
        AND currkaryawan.karyawan_email IS NOT NULL) as total_karyawan")
        , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_active='1' AND payroll_lock='1' LIMIT 1) payroll_islock")
        , DB::raw("(SELECT payroll_id FROM tr_payroll WHERE tr_payroll.ms_karyawan_id = karyawan_id AND payroll_active='1' AND payroll_status IN ('0','1','2') LIMIT 1) payroll_isexist")
        )->where([
            'karyawan_id' => $karyawanId, 
            'karyawan_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        
        if(!$karyawan) {
            abort(404);
        }
        
        $rules = [
            'karyawan_enid' => ['required', 
                Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id, $karyawanId, $user_id) {
                    return $query->where([
                        'ms_user_id' => $user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ])
                    ->where('karyawan_id', '<>', $karyawanId);
                })
            ],
            'karyawan_nik' => [
                'required',
                Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id, $karyawanId, $user_id) {
                    return $query->where([
                        'ms_user_id' => $user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ])
                    ->where('karyawan_id', '<>', $karyawanId);
                })
            ],
            'karyawan_npwp' => ['required', 
                Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id, $karyawanId, $user_id) {
                    return $query->where([
                        'ms_user_id' => $user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ])
                    ->where('karyawan_id', '<>', $karyawanId)
                    ->where('karyawan_npwp', '<>', '00.000.000.0-000.000');
                })
            ],
            'karyawan_name' => 'required',
            'karyawan_email' => $email ? 'email' : '',
            'karyawan_citizenship' => 'required|in:WNI,WNA',
            'ptkp_id' => 'required',
            'jenis_transaksi' => 'required',
            'karyawan_gender' => 'required',
            'karyawan_contract_begin' => 'required|date_format:d-m-Y',
            'karyawan_calculation_method' =>  'required|in:GROSS,GROSS_UP,NETT',
            // 'karyawan_bankid' => 'required',
            // 'karyawan_banknokartu' => 'required',
        ];
        
        if($email) {
            $rules['karyawan_email'] = ['required','email',
                Rule::unique('ms_karyawan')->where(function ($query) use($wajibpajak_id, $karyawanId, $user_id) {
                    return $query->where([
                        'ms_user_id' => $user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ])->where('karyawan_id', '<>', $karyawanId);
                }),
            ];
        }

        // if($request->input('karyawan_npwp')) {
        //     $rules['karyawan_npwp'] = [
        //         Rule::unique('ms_karyawan')->where(function ($query) use ($wajibpajak_id, $karyawanId) {
        //             return $query->where([
        //                 'ms_wajibpajak_id' => $wajibpajak_id
        //             ])->where('karyawan_id', '<>', $karyawanId);
        //         })
        //     ];
        // }
        
        
        $dt_cbegin = \Carbon\Carbon::parse($request->input('karyawan_contract_begin'));
        // check tgl masuk is different
        // if($karyawan->karyawan_contract_begin != $dt_cbegin->translatedFormat('Y-m-d')) {
        //     if($karyawan->payroll_isexist > 0) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => 'Tanggal kontrak tidak bisa dirubah. Sudah ada transaksi penggajian periode ini!'
        //         ]);
        //     }
        // } else {
        //     $rules['karyawan_contract_begin'] = '';
        // }
        // check if ptkp is different
        if($request->input('ptkp_id') && $karyawan->ms_ptkp_id != $request->input('ptkp_id')) {
            if($karyawan->payroll_isexist > 0) {
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
        if($request->input('karyawan_calculation_method') && $karyawan->karyawan_calculation_method != $request->input('karyawan_calculation_method')) {
            if($karyawan->payroll_isexist > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Metode PPh21 tidak bisa dirubah. Sudah ada transaksi penggajian periode ini!'
                ]);
            }
        } else {
            $rules['karyawan_calculation_method'] = '';
            $request->request->add(['karyawan_calculation_method' => $karyawan->karyawan_calculation_method]);
        }

        $this->validate($request, $rules, [
            'karyawan_enid.required' => 'ID Karyawan wajib diisi!',
            'karyawan_enid.unique' => 'ID Karyawan sudah terdaftar!',
            'jenis_transaksi.required' => 'Objek Pajak wajib diisi!',
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
            'karyawan_contract_begin.required' => 'Tgl. kontrak wajib diisi!',
            // 'karyawan_bankid.required' => 'Nama Bank wajib diisi!',
            // 'karyawan_banknokartu.required' => 'Nomor Kartu Bank wajib diisi!',
            'karyawan_photo.mimes' => 'Format gambar harus jpg atau png!',
            'karyawan_photo.max' => 'Ukuran gambar maksimal 250 Kb!',
            'karyawan_calculation_method.required' => 'Metode perhitungan wajib diisi!',
        ]);

        $old_photo = $karyawan->karyawan_photo;
        $old_isuser = $karyawan->karyawan_isuser;
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
        
        
        // $dt_cbegin = \Carbon\Carbon::parse($request->input('karyawan_contract_begin'));
        $dt_cend = null;
        if($request->input('karyawan_contract_end')) {
            $dt_cend = \Carbon\Carbon::parse($request->input('karyawan_contract_end'));
        }

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
            LIMIT 1
        )
        as mtable) as manager_json")
        , DB::raw("(SELECT COUNT(subkaryawan.*) FROM ms_karyawan as subkaryawan 
        WHERE subkaryawan.ms_wajibpajak_id = {$wajibpajak_id}
        LIMIT 1) as total_subkaryawan")
        )
        ->where(['wajibpajak_id' => $wajibpajak_id])->first();

        $total_entity = ($karyawan->karyawan_email == $email && intval($wajibpajak->total_karyawan) == 1) ? 0 : intval($wajibpajak->total_karyawan);
        
        DB::beginTransaction();
        try {
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
                'karyawan_birthdate' => ($dt !== null) ? $dt->translatedFormat('Y-m-d') : null,
                'karyawan_birthplace' => $request->input('karyawan_birthplace'),
                'karyawan_contract_begin' => ($dt_cbegin) ? $dt_cbegin->translatedFormat('Y-m-d') : null,
                'karyawan_contract_end' => ($dt_cend !== null) ? $dt_cend->translatedFormat('Y-m-d') : null,
                'karyawan_phone' => $request->input('karyawan_phone'),
                'karyawan_email' => $request->input('karyawan_email'),
                'karyawan_citizenship' => $request->input('karyawan_citizenship'),
                'karyawan_gender' => $request->input('karyawan_gender'),
                'karyawan_address' => $request->input('karyawan_address'),
                'karyawan_ismultiple' => $request->input('karyawan_ismultiple'),
                'ms_ptkp_id' => $request->input('ptkp_id'),
                'ms_country_id' => ($request->input('karyawan_citizenship') == 'WNI') ? 100 : $request->input('country_id'),
                'ms_bank_id' => $request->input('karyawan_bankid'),
                'ms_karyawanjabatan_id' => $position_id,
                'karyawan_isuser' => ($is_user == '1') ? 1 : 0,
                'karyawan_bankno' => $request->input('karyawan_banknokartu'),
                'karyawan_calculation_method' => $request->input('karyawan_calculation_method'),
                'karyawan_code_objekpajak' => $request->input('jenis_transaksi'),
            ];

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

            unset($karyawan->karyawan_password);
            unset($karyawan->karyawan_forgot_password);
            // update masa kerja
            KaryawanMasakerjaModel::where([
                'ms_karyawan_id' => $karyawanId,
                'ms_user_id' => $wajibpajak->ms_user_id,
                'ms_wajibpajak_id' => $wajibpajak_id,
                'karyawanmasakerja_active' => 1,
            ])->update([
                'ms_ptkp_id' => $request->input('ptkp_id'),
                'karyawanmasakerja_position' => $request->input('karyawan_position'),
                'karyawanmasakerja_calculation_method' => $request->input('karyawan_calculation_method'),
                'karyawanmasakerja_nik' => $request->input('karyawan_nik'),
                'karyawanmasakerja_npwp' => $request->input('karyawan_npwp'),
                'ms_objekpajak_code' => $request->input('jenis_transaksi'),
                'karyawanmasakerja_contract_begin' => $dt_cbegin->translatedFormat('Y-m-d'),
                'karyawanmasakerja_contract_end' => ($dt_cend !== null) ? $dt_cend->translatedFormat('Y-m-d') : null,
                'karyawanmasakerja_data' => json_encode($karyawan),
            ]);

            if($is_user == '1') {
                if($old_email != $email || $old_isuser == '0') {
                    // insert tr_notification
                    NotificationModel::create([
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
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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
        $order_columns = ['karyawan_nik','karyawan_npwp','karyawan_name','karyawan_phone','karyawan_code_objekpajak','karyawan_contract_begin','karyawan_contract_end'];
        $order_col = $order_columns[0];
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
            'karyawan_status' => 'NONKARYAWAN'
        ];
        $list = KaryawanModel::with(['masakerja' => function($q)
            {
                $q->whereIn('karyawanmasakerja_status', ['NONKARYAWAN']);
            
            }
        ])->select('ms_karyawan.*')
        ->when($search, function($q, $search) {
            return $q->where(DB::raw('LOWER(karyawan_nik)'), 'like', '%'.strtolower($search).'%')
            ->orWhere(DB::raw('LOWER(karyawan_npwp)'), 'like', '%'.strtolower($search).'%')
            ->orWhere(DB::raw('LOWER(karyawan_name)'), 'like', '%'.strtolower($search).'%');
        })
        ->where($where)
        ->take($limit)->skip($offset)->orderBy($order_col, $order_type)->get();

        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = KaryawanModel::where($where)->count();
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

        $this->validate($request, [
            'nonaktif_tgl' => 'required',
            'nonaktif_alasan' => 'required|in:RESIGN,LAINNYA',
        ], [
            'nonaktif_tgl.required' => 'Tanggal wajib diisi!',
            'nonaktif_alasan.required' => 'Alasan wajib diisi!',
        ]);

        
        $dt_cend = \Carbon\Carbon::parse($request->input('nonaktif_tgl'));
        $dt_cend_date = $dt_cend->translatedFormat('Y-m-d');
        DB::beginTransaction();
        try {
            KaryawanModel::where(['karyawan_id' => $karyawan->karyawan_id])
            ->update([
                'karyawan_active' => 0,
                'karyawan_contract_end' => $dt_cend->translatedFormat('Y-m-d'),
                'karyawan_end_type' => $request->input('nonaktif_alasan'),
                'karyawan_end_reason' => $request->input('nonaktif_keterangan'),
            ]);

            // update masa kerja
            KaryawanMasakerjaModel::where([
                'ms_karyawan_id' => $karyawan->karyawan_id,
                'karyawanmasakerja_contract_end' => null,
                'karyawanmasakerja_active' => 1,
            ])->update([
                'karyawanmasakerja_contract_end' => $dt_cend->translatedFormat('Y-m-d'),
                'karyawanmasakerja_end_type' => $request->input('nonaktif_alasan'),
                'karyawanmasakerja_end_reason' => $request->input('nonaktif_keterangan'),
                'karyawanmasakerja_active' => 0,
            ]);
            
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dinonaktifkan',
                // 'data' => $karyawanmasakerja
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
        $q = $request->get('q');
        $is_manager = $request->get('is_manager');
        $karyawan_id = $request->get('karyawan_id');
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $where = [
            'karyawan_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];
        
        $data = KaryawanModel::select('karyawan_id', 'karyawan_name', 'karyawan_nik', 'karyawan_npwp')
        ->when($q, function ($query, $q) {
            return $query->where('karyawan_name', 'ilike', '%'.$q.'%');
        })
        ->when($karyawan_id, function($query, $karyawan_id) {
            return $query->whereNotIn('karyawan_id', [$karyawan_id]);
        })
        ->where($where)
        ->whereIn('karyawan_status', ['NONKARYAWAN'])
        ->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    public function profile($karyawanId, Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $karyawan = KaryawanModel::
        select("*"
        , DB::raw("(SELECT json_agg(ttable) 
        FROM (
            SELECT std.st_tunjangankaryawan_id, stk.sttunjangankaryawan_name
            FROM st_tunjangan_karyawan_detail as std
            JOIN st_tunjangan_karyawan as stk ON std.st_tunjangankaryawan_id = stk.sttunjangankaryawan_id
            WHERE std.ms_karyawan_id = ms_karyawan.karyawan_id
                AND std.sttunjangankaryawandet_active = '1'
                AND stk.sttunjangankaryawan_active = '1'
        ) as ttable) as tunjangan_json")
        , DB::raw("(SELECT json_agg(ttable) 
        FROM (
            SELECT spd.st_potongankaryawan_id, spk.stpotongankaryawan_name
            FROM st_potongan_karyawan_detail as spd
            JOIN st_potongan_karyawan as spk ON spd.st_potongankaryawan_id = spk.stpotongankaryawan_id
            WHERE spd.ms_karyawan_id = ms_karyawan.karyawan_id
                AND spd.stpotongankaryawandet_active = '1'
                AND spk.stpotongankaryawan_active = '1'
        ) as ttable) as potongan_json"))
        ->with(['bank', 'penggajian', 'ptkp', 'manager', 'divisi', 'jabatan', 'attendance', 'country'])
        ->where([
            'karyawan_id' => $karyawanId, 
            'karyawan_active' => 1,
            'karyawan_status' => 'NONKARYAWAN',
            // 'karyawan_contract_end' => null,
            'ms_wajibpajak_id' => $wajibpajak_id,
            // 'ms_user_id' => $user_id,
        ])->first();
        if(!$karyawan) {
            abort(404);
        }

        $karyawan_infofield = KaryawanInfoFieldModel::where(['ms_wajibpajak_id' => $wajibpajak_id, 'ms_user_id' => $user_id])->first();
        // $stbpjskaryawan_data = ($setting[0]->setting_bpjspegawai_json) ? json_decode($setting[0]->setting_bpjspegawai_json) : null;
        $data = [
            'title' => 'Profile Karyawan',
            'content' => 'user.master.non-karyawan.profile',
            'karyawan' => $karyawan,
            'karyawan_infofield' => $karyawan_infofield,
            // 'sttunjangankaryawan_data' => ($setting[0]->setting_tunjangankaryawan_json) ? json_decode($setting[0]->setting_tunjangankaryawan_json) : null,
            // 'stbpjskaryawan_data' => ($stbpjskaryawan_data) ? json_decode($stbpjskaryawan_data->stbpjskaryawan_value) : null,
        ];
        // return response()->json($data);
        if($request->ajax()) {
            return view($data['content'], $data)->render();
        } else {
            return view('user.index', $data);
        }
    }

    public function importTemplate(Request $request)
    {
        try {
            // Get the file path from the 'public' directory
            $excelFilePath = public_path('assets/import/Template_Import_Non_Karyawan.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $spreadsheet = $reader->load($excelFilePath);

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
            $sheetTemplate->setTitle('Impor Profil Non Karyawan');
            $sheetTemplate->setCellValue('A1', 'Profil Karyawan');
            $sheetTemplate->mergeCells('A1:K1');
            $sheetTemplate->getStyle('A1')->applyFromArray($boldFontStyle);
            $sheetTemplate->setCellValue('L1', 'Informasi Kontrak');
            $sheetTemplate->mergeCells('L1:S1');
            $sheetTemplate->getStyle('L1')->applyFromArray($boldFontStyle);
            // Center align cell A1
            $sheetTemplate->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Center align cell O1
            $sheetTemplate->getStyle('L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

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
            $sheetTemplate->setCellValue('L2', 'Tanggal Masuk*');
            $sheetTemplate->setCellValue('M2', 'Tanggal Berakhir Kontrak');
            $sheetTemplate->setCellValue('N2', 'Jabatan');
            $sheetTemplate->setCellValue('O2', 'Kode Objek Pajak*');
            $sheetTemplate->setCellValue('P2', 'Metode Pph 21*');
            $sheetTemplate->setCellValue('Q2', 'Sumber Penghasilan*');
            $sheetTemplate->setCellValue('R2', 'Kode Bank');
            $sheetTemplate->setCellValue('S2', 'Nomor Rekening');
            $sheetTemplate->getStyle('A2:S2')->applyFromArray($boldFontStyle);

            // for ($col = 'A'; $col <= 'S'; $col++) {
            //     $sheetTemplate->getStyle($col . '2')->applyFromArray($boldFontStyle);
            //     $sheetTemplate->getStyle($col . ':' . $col)
            //         ->getNumberFormat()
            //         ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
            // }

            $startRow = 3;

            $listGender = '"L,P"';
            $genderValidation = $sheetTemplate->getCell('I' . $startRow)->getDataValidation();
            $genderValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $genderValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $genderValidation->setShowDropDown(true);
            $genderValidation->setFormula1($listGender);

            $listMethod = '"GROSS,NETT,GROSS_UP"';
            $methodValidation = $sheetTemplate->getCell('P' . $startRow)->getDataValidation();
            $methodValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $methodValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $methodValidation->setShowDropDown(true);
            $methodValidation->setFormula1($listMethod);

            $listSumber = '"Satu Pemberi Kerja,Beberapa Pemberi Kerja"';
            $sumberValidation = $sheetTemplate->getCell('Q' . $startRow)->getDataValidation();
            $sumberValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $sumberValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $sumberValidation->setShowDropDown(true);
            $sumberValidation->setFormula1($listSumber);

            for ($row = $startRow + 1; $row <= 52; $row++) {
                $cellI = $sheetTemplate->getCell('I' . $row);
                $cellI->setDataValidation(clone $genderValidation);

                $cellP = $sheetTemplate->getCell('P' . $row);
                $cellP->setDataValidation(clone $methodValidation);

                $cellQ = $sheetTemplate->getCell('Q' . $row);
                $cellQ->setDataValidation(clone $sumberValidation);
            }

            for ($col = 'A'; $col <= 'S'; $col++) {
                $sheetTemplate->getStyle($col . ':' . $col)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
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
            $filename = 'Templat_Impor_Non_Karyawan_' . $unixtime . '.xls';
            $location = public_path('assets/export/');
            ob_start();
            $writer->save($location.$filename);
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
                'historyimport_type' => "NONEMPLOYEE",
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
                'message' => 'Berhasil import data non karyawan.',
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
        $user_id = $getHistory->ms_user_id;
        $filename = $getHistory->historyimport_file_name;
    
        // $quota = $request->get('quota');

        $path = public_path('uploads/'.$filename);
        $inputFileType = IOFactory::identify($path);
        $reader = IOFactory::createReader($inputFileType);
        $worksheetData = $reader->listWorksheetInfo($path);

        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
    
        // Get the Sheet D
        $sheet = $spreadsheet->getSheetByName('Impor Profil Non Karyawan');
        $maxRows = $sheet->getHighestRow();
        // dd($sheet);
        $quota = $otherdata['quota'];
        // if($quota && $maxRows > $quota['maxquota']) {
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
        
        // function formatDate($data) {
        //     if (is_string($data) && preg_match('/^\d{2}-\d{2}-\d{4}$/', $data)) {
        //         // Date is in the format "DD Month YYYY"
        //         // Keep the date as is
        //         $dateString = true;
        //     }  else {
        //         // Handle cases where the date format is not as expected
        //         $dateString = false; // Set to an empty string or handle the error as needed
        //     }
        
        //     return $dateString;
        // }

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

                if($columnA === null) {
                    if($dataB === null) {
                        break;
                    } 
                    // else {
                    //     $spreadsheet_chunk->__destruct();
                    //     $spreadsheet_chunk = null;
                    //     unset($spreadsheet_chunk);
                        
                        // return response()->json([
                        //     'success' => false,
                        //     'message' => "Mohon lengkapi Nama Karyawan yang masih kosong"
                        // ]);
                    // }
                }

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
                $columnN = $sheet->getCell("N" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnO = $sheet->getCell("O" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnP = $sheet->getCell("P" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnQ = $sheet->getCell("Q" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnR = $sheet->getCell("R" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
                $columnS = $sheet->getCell("S" . $nbi)->getValue(); // Use $sheet instead of $worksheetData
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
                    "karyawan_start_contract" => $columnL,
                    "karyawan_end_contract" => $columnM,
                    "karyawan_jabatan" => $columnN,
                    "karyawan_code_objekpajak" => $columnO,
                    "karyawan_ppn_method" => $columnP,
                    "karyawan_ismultiple" => $columnQ,
                    "karyawan_code_bank" => $columnR,
                    "karyawan_bank_account" => $columnS,
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
        //     'message' => 'Berhasil import data karyawan.',
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

        DB::beginTransaction();
        try {
            $totalComplete = 0;
            $totalFail = 0;
            // $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
            // $user_id = session()->get('user_data')['user_id'];

            if(count($result) > 0) {
                $checkPtkp = PtkpModel::where(['ptkp_active' => 1])->get();
                $checkBank = BankModel::where(['bank_active' => 1])->get();
                $checkObjekPajak = ObjekPajakModel::where('objekpajak_active', '1')
                    ->where('objekpajak_nonemployee', '1')
                    ->get();

                for($x = 0; $x < count($result); $x++) {
                    $checkKaryawan = KaryawanModel::where('karyawan_enid', $result[$x]['karyawan_enid'])->where('ms_wajibpajak_id', $wajibpajak_id)->first();
    
                    if($checkKaryawan) {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Id Karyawan sudah terdaftar';
                        continue;
                    }

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

                    if($result[$x]['karyawan_ppn_method'] !== 'GROSS' && $result[$x]['karyawan_ppn_method'] !== 'GROSS_UP' && $result[$x]['karyawan_ppn_method'] !== 'NETT') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon hanya masukan GROSS / NETT / GROSS_UP untuk Metode Pph 21';
                        continue;
                    }

                    if($result[$x]['karyawan_ismultiple'] !== 'Beberapa Pemberi Kerja' && $result[$x]['karyawan_ismultiple'] !== 'Satu Pemberi Kerja') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon hanya masukan Beberapa Pemberi Kerja / Satu Pemberi Kerja untuk Sumber Penghasilan';
                        continue;
                    }
                    
                    if (formatDate($result[$x]['karyawan_start_contract'], '/^\d{2}-\d{2}-\d{4}$/') === false) {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan format Tanggal Masuk sesuai format sistem REKKAA';
                        continue;
                    }

                    if ($result[$x]['karyawan_birthdate'] !== null && $result[$x]['karyawan_birthdate'] !== '' && formatDate($result[$x]['karyawan_birthdate'], '/^\d{2}-\d{2}-\d{4}$/') === false) {
                            
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan format Tanggal Berakhir Kontrak sesuai format sistem REKKAA';
                        continue;
                    }

                    $endContract = null;
                    
                    if ($result[$x]['karyawan_end_contract'] !== null && $result[$x]['karyawan_end_contract'] !== '' && formatDate($result[$x]['karyawan_end_contract'], '/^\d{2}-\d{2}-\d{4}$/') === false) {
                            
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan format Tanggal Berakhir Kontrak sesuai format sistem REKKAA';
                        continue;
                    }

                    if ($result[$x]['karyawan_end_contract'] !== null && $result[$x]['karyawan_end_contract'] !== '' && formatDate($result[$x]['karyawan_end_contract'], '/^\d{2}-\d{2}-\d{4}$/') !== false) {
                        $endContract = convertDateStringToYYYYMMDD($result[$x]['karyawan_end_contract']);
                    }
                    
                    if($result[$x]['karyawan_ptkp'] === null || $result[$x]['karyawan_ptkp'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Status Perkawinan sesuai sheet Referensi PTKP';
                        continue;
                    }
                    
                    if($result[$x]['karyawan_code_objekpajak'] === null || $result[$x]['karyawan_code_objekpajak'] === '') {
                        
                        $totalFail = $totalFail + 1;
                        $result[$x]['status'] = 'Gagal';
                        $result[$x]['remark'] = 'Mohon masukan Kode Objek Pajak sesuai sheet Referensi Objek Pajak';
                        continue;
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
                            $result[$x]['remark'] = 'Mohon masukan Status Perkawinan sesuai sheet Referensi PTKP';
                            continue;
                        }
                    }

                    if($result[$x]['karyawan_code_objekpajak'] !== null && $result[$x]['karyawan_code_objekpajak'] !== '') {
                        $found = false;

                        foreach ($checkObjekPajak as $item) {
                            if (isset($item['objekpajak_code']) && $item['objekpajak_code'] == $result[$x]['karyawan_code_objekpajak']) {
                                $found = true;
                                break; // Exit the loop when a match is found.
                            }
                        }
    
                        if($found === false) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan Kode Objek Pajak sesuai sheet Referensi Objek Pajak';
                            continue;
                        }
                    }
                    
                    if($result[$x]['karyawan_code_bank'] !== null && $result[$x]['karyawan_code_bank'] !== '') {
                        $found = false;

                        foreach ($checkBank as $item) {
                            if (isset($item['bank_id']) && $item['bank_id'] == $result[$x]['karyawan_code_bank']) {
                                $found = true;
                                break; // Exit the loop when a match is found.
                            }
                        }
    
                        if($found === false) {
                            
                            $totalFail = $totalFail + 1;
                            $result[$x]['status'] = 'Gagal';
                            $result[$x]['remark'] = 'Mohon masukan Kode Bank sesuai sheet Referensi Bank';
                            continue;
                        }
                    } 

                    $random_password = null;
                    $generate_forgot_password = null;

                    $jabatan_id = null;
                    if($result[$x]['karyawan_jabatan'] !== null && $result[$x]['karyawan_jabatan'] === '' && $result[$x]['karyawan_jabatan'] === ' ') {
                        $jabatan = capitalizeFirstLetterOfWords($result[$x]['karyawan_jabatan']);
                        $checkJabatan = KaryawanJabatanModel::where('ms_wajibpajak_id', $wajibpajak_id)->where('karyawanjabatan_active', '1')->where('karyawanjabatan_name', $jabatan)->first();

                        if(!$checkJabatan) {
                            $createJabatan = KaryawanJabatanModel::create([
                                'karyawanjabatan_name' => $jabatan,
                                'ms_user_id' => $user_id,
                                'ms_wajibpajak_id' => $wajibpajak_id,
                            ]);
                            $jabatan_id = $createJabatan->karyawanjabatan_id;
                        }
                    }

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
                        'karyawan_status' => 'NONKARYAWAN',
                        'karyawan_calculation_method' => $result[$x]['karyawan_ppn_method'],
                        'karyawan_contract_begin' => convertDateStringToYYYYMMDD($result[$x]['karyawan_start_contract']),
                        'ms_ptkp_id' => preg_replace('/[^0-9]/', '', $result[$x]['karyawan_ptkp']),
                        'karyawan_enid' => $result[$x]['karyawan_enid'],
                        'ms_bank_id' => $result[$x]['karyawan_code_bank'],
                        'ms_karyawanjabatan_id' => $jabatan_id,
                        'karyawan_bankno' => $result[$x]['karyawan_bank_account'],
                        'karyawan_birthplace' => capitalizeFirstLetterOfWords($result[$x]['karyawan_birthplace']),
                        'karyawan_password' => $random_password,
                        'karyawan_forgot_password' => $generate_forgot_password,
                        'karyawan_isuser' => '0',
                        'karyawan_contract_end' => $endContract,
                        'karyawan_code_objekpajak' => $result[$x]['karyawan_code_objekpajak'],
                        'karyawan_ismultiple' => $result[$x]['karyawan_ismultiple'] === 'Beberapa Pemberi Kerja' ? '1' : '0'
                    ]);

                    KaryawanMasakerjaModel::create([
                        'ms_karyawan_id' => $createKaryawan->karyawan_id,
                        'ms_ptkp_id' => $createKaryawan->ms_ptkp_id,
                        'ms_user_id' => $user_id,
                        'ms_wajibpajak_id' => $wajibpajak_id,
                        'karyawanmasakerja_status' => $createKaryawan->karyawan_status,
                        'karyawanmasakerja_calculation_method' => $result[$x]['karyawan_ppn_method'],
                        'karyawanmasakerja_position' => capitalizeFirstLetterOfWords($result[$x]['karyawan_jabatan']),
                        'karyawanmasakerja_nik' => $createKaryawan->karyawan_nik,
                        'karyawanmasakerja_npwp' => $createKaryawan->karyawan_npwp,
                        'ms_objekpajak_code' => $result[$x]['karyawan_code_objekpajak'],
                        'karyawanmasakerja_contract_begin' => $createKaryawan->karyawan_contract_begin,
                        'karyawanmasakerja_contract_end' => $createKaryawan->karyawan_contract_end,
                        'karyawanmasakerja_data' => json_encode($createKaryawan),
                    ]);

                    // if($isUser === true) {
                    //     $countKaryawan = KaryawanModel::where('karyawan_email', $result[$x]['karyawan_email'])->count();

                    //     NotificationModel::create([
                    //         'notification_title' => 'Rekkaa - Undangan Dari '.session()->get('wajibpajak_current')['wajibpajak_name'],
                    //         'notification_type' => 'EMAIL',
                    //         'notification_from' => env("MAIL_FROM_ADDRESS"),
                    //         'notification_to' => $result[$x]['karyawan_email'],
                    //         'ms_user_id' => $user_id,
                    //         'ms_wajibpajak_id' => $wajibpajak_id,
                    //         'notification_view' => 'email.email-undangan-akun-karyawan',
                    //         'notification_data' => json_encode([
                    //             'entity' => [
                    //                 'name' => session()->get('wajibpajak_current')['wajibpajak_name'],
                    //                 'user_name' => capitalizeFirstLetterOfWords($result[$x]['karyawan_name']),
                    //                 'inviter_email' => session()->get('user_data')['user_email'],
                    //                 'forgot_token' => $generate_forgot_password,
                    //                 'total_entity' => $countKaryawan - 1,
                    //             ]
                    //         ])
                    //     ]);
                    // }

    
                    $totalComplete = $totalComplete + 1;
                    $result[$x]['status'] = 'Sukses';
                    $result[$x]['remark'] = '-';
                }
            }

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
            //     'message' => 'Berhasil import data non karyawan.',
            //     'result' => $result,
            //     'totalsuccess' => $totalComplete,
            //     'totalfail'=> $totalFail,
            //     'totaldata' => $totalComplete + $totalFail,
            //     // 'history' => $createHistory->historyimport_id
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
            $excelFilePath = public_path('assets/import/Template_Import_Non_Karyawan.xlsx'); // Update the file name and path as needed
            $reader = IOFactory::createReader('Xlsx');

            $unixtime = time();
            $title = 'Riwayat_Impor_Non_Karyawan_'.$unixtime;

            $spreadsheet = $reader->load($excelFilePath);

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
            $sheetImpor->setTitle('Impor Profil Non Karyawan');
            $sheetImpor->setCellValue('A1', 'Profil Karyawan');
            $sheetImpor->mergeCells('A1:K1');
            $sheetImpor->getStyle('A1')->applyFromArray($boldFontStyle);
            $sheetImpor->setCellValue('L1', 'Informasi Kontrak');
            $sheetImpor->mergeCells('L1:S1');
            $sheetImpor->getStyle('L1')->applyFromArray($boldFontStyle);
            // Center align cell A1
            $sheetImpor->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            // Center align cell O1
            $sheetImpor->getStyle('L1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

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
            $sheetImpor->setCellValue('L2', 'Tanggal Masuk*');
            $sheetImpor->setCellValue('M2', 'Tanggal Berakhir Kontrak');
            $sheetImpor->setCellValue('N2', 'Jabatan');
            $sheetImpor->setCellValue('O2', 'Kode Objek Pajak*');
            $sheetImpor->setCellValue('P2', 'Metode Pph 21*');
            $sheetImpor->setCellValue('Q2', 'Sumber Penghasilan*');
            $sheetImpor->setCellValue('R2', 'Kode Bank');
            $sheetImpor->setCellValue('S2', 'Nomor Rekening');
            $sheetImpor->setCellValue('T2', 'Status');
            $sheetImpor->setCellValue('U2', 'Catatan');
            $sheetImpor->getStyle('A2:U2')->applyFromArray($boldFontStyle);

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
                    'L' => isset($item->karyawan_start_contract) ? $item->karyawan_start_contract : '',
                    'M' => isset($item->karyawan_end_contract) ? $item->karyawan_end_contract : '',
                    "N" => isset($item->karyawan_jabatan) ? $item->karyawan_jabatan : '',
                    "O" => isset($item->karyawan_code_objekpajak) ? $item->karyawan_code_objekpajak : '',
                    "P" => isset($item->karyawan_ppn_method) ? $item->karyawan_ppn_method : '',
                    "Q" => isset($item->karyawan_ismultiple) ? $item->karyawan_ismultiple : '',
                    "R" => isset($item->karyawan_code_bank) ? $item->karyawan_code_bank : '',
                    "S" => isset($item->karyawan_bank_account) ? $item->karyawan_bank_account : '',
                    'T' => isset($item->status) ? $item->status : 'Sukses',
                    'U' => isset($item->remark) ? $item->remark : '-'
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

            $listMethod = '"GROSS,NETT,GROSS_UP"';
            $methodValidation = $sheetImpor->getCell('P' . $startRow)->getDataValidation();
            $methodValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $methodValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $methodValidation->setShowDropDown(true);
            $methodValidation->setFormula1($listMethod);

            $listSumber = '"Beberapa Pemberi Kerja,Satu Pemberi Kerja"';
            $methodValidation = $sheetImpor->getCell('Q' . $startRow)->getDataValidation();
            $methodValidation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST);
            $methodValidation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION);
            $methodValidation->setShowDropDown(true);
            $methodValidation->setFormula1($listMethod);

            for ($row = $startRow + 1; $row <= 52; $row++) {
                $cellI = $sheetImpor->getCell('I' . $row);
                $cellI->setDataValidation(clone $genderValidation);

                $cellP = $sheetImpor->getCell('P' . $row);
                $cellP->setDataValidation(clone $methodValidation);

                $cellQ = $sheetImpor->getCell('Q' . $row);
                $cellQ->setDataValidation(clone $methodValidation);
            }

            for ($col = 'A'; $col <= 'U'; $col++) {
                $sheetImpor->getStyle($col . ':' . $col)
                    ->getNumberFormat()
                    ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
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
        
        $dataHistory = HistoryImportModel::where('historyimport_type', 'NONEMPLOYEE')
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