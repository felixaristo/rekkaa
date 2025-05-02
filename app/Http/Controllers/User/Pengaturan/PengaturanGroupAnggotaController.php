<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Master\KaryawanModel;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\Model\Master\UserGroupAccessModel;
use App\Model\Master\UserGroupMemberModel;
use App\Model\Master\UserGroupModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use App\Model\Transaction\NotificationModel;
use App\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PengaturanGroupAnggotaController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = [
            'title' => 'Kelola Pengguna',
            'content' => 'user.pengaturan.group-member.index',
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
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
        $search = (isset($request->input('search')['value']) && $request->input('search')['value']) ? $request->input('search')['value'] : null;

        $where = [
            // 'muwp.ms_user_id' => $user_id, 
            'wajibpajak_id' => $wajibpajak_id,
        ];

        $list = WajibPajakModel::select('ms_wajib_pajak.*', 'mug.usergroup_id', 'mug.usergroup_name', 'tugm.usergroupmember_members'
        , 'muwp.userwajibpajak_name', 'muwp.userwajibpajak_phone'
        , 'mu.user_id', 'mu.user_email', 'mk.karyawan_name')
        ->join('ms_user_group AS mug', 'mug.ms_wajibpajak_id', '=', 'ms_wajib_pajak.wajibpajak_id')
        ->join('tr_user_group_member AS tugm', 'tugm.ms_usergroup_id', '=', 'mug.usergroup_id')
        ->join("ms_user AS mu", "mu.user_id", '=', DB::raw("any(string_to_array(tugm.usergroupmember_members, ',')::bigint[])"))
        ->join('mr_user_wajib_pajak AS muwp', function($q) {
            $q->on('muwp.ms_user_id', '=', 'mu.user_id');
            $q->on('muwp.ms_wajibpajak_id', '=', 'wajibpajak_id');
        })
        ->leftJoin('ms_karyawan AS mk', 'mk.ms_user_id', '=', 'mu.user_id')
        ->when($search, function($q, $search) {
            return $q->where(DB::raw('LOWER(user_email)'), 'like', '%'.strtolower($search).'%');
        })
        ->where($where)
        ->take($limit)->skip($offset)->get();
        // dd($list);
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = WajibPajakModel::join('ms_user_group AS mug', 'mug.ms_wajibpajak_id', '=', 'ms_wajib_pajak.wajibpajak_id')
        ->join('tr_user_group_member AS tugm', 'tugm.ms_usergroup_id', '=', 'mug.usergroup_id')
        ->join("ms_user AS mu", "mu.user_id", '=', DB::raw("any(string_to_array(tugm.usergroupmember_members, ',')::bigint[])"))
        ->leftJoin('ms_karyawan AS mk', 'mk.ms_user_id', '=', 'mu.user_id')
        ->where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {
        // $usergroup_member = $request->input('usergroup_member');
        // dd($request->input());
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $email = $request->input('user_email');
        $this->validate($request, [
            'usergroup_id' => 'required',
            'user_email' => 'required|email',
            'user_name' => 'required',
            'user_hp' => 'required',
        ], [
            'usergroup_id.required' => 'Group wajib diisi!',
            'user_email.required' => 'Email wajib diisi!',
            'user_email.email' => 'Format email tidak sesuai!',
            'user_name.required' => 'Nama wajib diisi!',
            'user_hp.required' => 'Hp wajib diisi!',
        ]);

        // find user exist
        $usergroup_id = intval($request->input('usergroup_id'));
        $user = User::
        select('user_id', 'user_email'
        , DB::raw("(SELECT row_to_json(ugmtable) 
            FROM (
                SELECT * FROM tr_user_group_member ugm
                JOIN ms_user_group ug ON ug.usergroup_id = ugm.ms_usergroup_id
                    WHERE 
                    ug.ms_wajibpajak_id = $wajibpajak_id
                    AND user_id = ANY(string_to_array(ugm.usergroupmember_members, ',')::bigint[])
                ORDER BY user_id ASC
            )
            as ugmtable) as ugm_json"))
        ->where(['user_email' => $email])->first();
        // dd($user);
        if($user) {
            $ugm = ($user->ugm_json) ? json_decode($user->ugm_json) : null;
            if($ugm) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah pernah didaftarkan. Silahkan edit data sebelumnya',
                ]);
            }
        }

        $current_user = User::with([
            'wajibpajak' => function($q) use ($wajibpajak_id) {
                $q->where(['wajibpajak_id' => $wajibpajak_id]);
            },
            'userwajibpajak' => function($q) use ($wajibpajak_id) {
                $q->where(['ms_wajibpajak_id' => $wajibpajak_id]);
            }
        ])->where([
            'user_id' => $user_id
        ])->first();

        // find usergroup
        $usergroup_member = UserGroupMemberModel::where([
            'ms_usergroup_id' => $request->input('usergroup_id'),
            'usergroupmember_active' => 1
        ])->first();
        
        DB::beginTransaction();
        try {
            if($user) { // if user exist
                MrUserWajibPajakModel::where([
                    'ms_user_id' => $user_id,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                ])->update([
                    'userwajibpajak_name' => $request->input('user_name'),
                    'userwajibpajak_phone' => $request->input('user_hp'),
                ]);
                $user_id = $user->user_id;

            } else { // if not exist, then create
                $random_password = Str::random(6);
                $generate_token = Hash::make('VRA'.$request->input('user_email').uniqid());
                $generate_forgottoken = Hash::make('FGP'.$request->input('user_email').uniqid());

                $user = User::create([
                    // 'user_name' => $request->input('user_name'),
                    'user_email' => $request->input('user_email'),
                    // 'user_phone' => $request->input('user_hp'),
                    'user_password' => Hash::make($random_password),
                    'user_verified_token' => $generate_token,
                    'user_forgot_token' => $generate_forgottoken,
                ]);
                $user_id = $user->user_id;
                $userwajibpajak = MrUserWajibPajakModel::create([
                    'ms_user_id' => $user->user_id,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'userwajibpajak_name' => $request->input('user_name'),
                    'userwajibpajak_phone' => $request->input('user_hp'),
                    'userwajibpajak_owner' => 0,
                ]);
            }
    
            if($usergroup_member) { // update current user
                $explode_member = [];
                if($usergroup_member->usergroupmember_members) {
                    $explode_member = explode(',', $usergroup_member->usergroupmember_members);
                }
                array_push($explode_member, $user_id);
                $membersgroup = implode(',', $explode_member);
                $save_data = [
                    'ms_usergroup_id' => $usergroup_id,
                    'usergroupmember_members' => $membersgroup,
                ];
    
                $usergroup = UserGroupMemberModel::
                where('ms_usergroup_id', $usergroup_member->ms_usergroup_id)
                ->update($save_data);

            } else {
                $membersgroup = $user_id;

                $save_data = [
                    'ms_usergroup_id' => $usergroup_id,
                    'usergroupmember_members' => $membersgroup,
                ];
    
                $usergroup = UserGroupMemberModel::create($save_data);
            }

            // insert tr_notification
            NotificationModel::create([
                'notification_title' => 'Rekkaa - Undangan Dari '.$current_user->wajibpajak->wajibpajak_name,
                'notification_type' => 'EMAIL',
                'notification_from' => env("MAIL_FROM_ADDRESS"),
                'notification_to' => $user->user_email,
                'ms_user_id' => $user->user_id,
                'ms_wajibpajak_id' => $wajibpajak_id,
                'notification_view' => 'email.email-undangan-akun',
                'notification_data' => json_encode([
                    'entity' => [
                        'name' => $current_user->wajibpajak->wajibpajak_name,
                        'user_name' => $request->input('user_name'),
                        'inviter_email' => session()->get('user_data')['user_email']
                    ]
                ])
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kelola Pengguna berhasil disimpan',
                // 'random_password' => (isset($random_password)) ? $random_password : ''
            ]);
            

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update($userId, Request $request)
    {
        // $user_id = request()->input('user_id');
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $usergroup_id = request()->input('usergroup_id');
        $usergroup = UserGroupModel::where([
            'usergroup_id' => $usergroup_id, 
            'usergroup_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])->first();
        if(!$usergroup) {
            return response()->json([
                'success' => false,
                'message' => 'User group tidak ditemukan!'
            ]); 
        }
        $where = [
            // 'ms_usergroup_id' => $usergroupId, 
            'usergroupmember_active' => 1,
            'mu.user_id' => $userId,
        ];
        // find deleted user exist in member
        $usergroup_member = UserGroupMemberModel::select('usergroupmember_id','usergroupmember_members', 'ms_usergroup_id')
        ->join("ms_user AS mu", "mu.user_id", '=', DB::raw("any(string_to_array(usergroupmember_members, ',')::bigint[])"))
        ->where($where)
        ->first();

        if(!$usergroup_member) {
            return response()->json([
                'success' => false,
                'message' => 'Member group tidak ditemukan!'
            ]); 
        }

        // check if different usergroup
        $newmembers = [];
        if($usergroup_member->ms_usergroup_id != $usergroup_id) {
            // update 
            $members = explode(',', $usergroup_member->usergroupmember_members);
            $newmembers = array_diff($members, [$userId]);

            // find usergroup
            $new_usergroup_member = UserGroupMemberModel::where([
                'ms_usergroup_id' => $usergroup_id,
                'usergroupmember_active' => 1
            ])->first();
            if(!$new_usergroup_member) {
                return response()->json([
                    'success' => false,
                    'message' => 'Group member tidak ditemukan',
                ]);
            }

            $explode_newmember = explode(',', $new_usergroup_member->usergroupmember_members);
            array_push($explode_newmember, $userId);
            $newmembersgroup = implode(',', $explode_newmember);
            $save_data = [
                'ms_usergroup_id' => $usergroup_id,
                'usergroupmember_members' => $newmembersgroup,
            ];
        }

        DB::beginTransaction();
        try {
            if(count($newmembers) > 0) {
                $usergroup_member->update([
                    'usergroupmember_members' => implode(',', $newmembers)
                ]);
            }

            if(isset($save_data) && $save_data) {
                $usergroup = UserGroupMemberModel::
                where('ms_usergroup_id', $usergroup_id)
                ->update($save_data);
            }

            // update user
            MrUserWajibPajakModel::where(['ms_user_id' => $userId, 'ms_wajibpajak_id' => $wajibpajak_id])->update([
                'userwajibpajak_name' => $request->input('user_name'),
                'userwajibpajak_phone' => $request->input('user_hp'),
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Anggota group berhasil dirubah',
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function destroy($usergroupId, Request $request)
    {
        $user_id = request()->input('user_id');
        $usergroup = UserGroupModel::with(['wajibpajak'])->where([
            'usergroup_id' => $usergroupId, 
            'usergroup_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ])->first();
        if(!$usergroup) {
            return response()->json([
                'success' => false,
                'message' => 'User group tidak ditemukan!'
            ]); 
        }
        // check if owner
        if($usergroup->wajibpajak->ms_user_id == $user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diperbolehkan menghapus pemilik entitas!'
            ]); 
        }
        // dd($usergroup);
        $where = [
            'ms_usergroup_id' => $usergroupId, 
            'usergroupmember_active' => 1,
            'mu.user_id' => $user_id,
        ];
        // find deleted user exist in member
        $usergroup_member = UserGroupMemberModel::select('usergroupmember_id','usergroupmember_members', 'ms_usergroup_id')
        ->join("ms_user AS mu", "mu.user_id", '=', DB::raw("any(string_to_array(usergroupmember_members, ',')::bigint[])"))
        ->where($where)
        ->first();

        if(!$usergroup_member) {
            return response()->json([
                'success' => false,
                'message' => 'Member group tidak ditemukan!'
            ]); 
        }

        $members = explode(',', $usergroup_member->usergroupmember_members);
        $newmembers = array_diff($members, [$user_id]);
        // var_dump($newmembers);
        // dd(implode(',', $newmembers));
        DB::beginTransaction();
        try {
            $usergroup_member->update([
                'usergroupmember_members' => implode(',', $newmembers)
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pengaturan Anggota group berhasil dihapus',
                // 'data' => $sttunjangankaryawan
            ]);

        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getkaryawan(Request $request) 
    {
        $email = $request->input('email');
        $this->validate($request, [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Format email tidak sesuai!',
        ]);

        // get karyawan according the session
        $karyawan = KaryawanModel::select('karyawan_id', 'karyawan_name', 'karyawan_nik', 'karyawan_npwp', 'karyawan_birthdate', 'karyawan_phone', 'karyawan_email')
        ->with(['masakerja' => function($q) {
            // return $q->select();
        }])->where([
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
            'karyawan_email' => $email, 
            'karyawan_active' => 1
        ])->first();

        if(!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Karyawan ditemukan',
            'data' => [
                'karyawan' => $karyawan
            ]
        ]);
    }

    public function storekaryawan(Request $request) 
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $user_id = intval($request->input('user_id'));
        $usergroup_id = intval($request->input('usergroup_id'));
        $email = $request->input('email');
        $this->validate($request, [
            'user_id' => 'required',
            'usergroup_id' => 'required',
            'email' => 'required|email',
        ], [
            'user_id.required' => 'User wajib diisi!',
            'usergroup_id.required' => 'User Group wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Format email tidak sesuai!',
        ]);

        // get karyawan according the session
        $karyawan = KaryawanModel::select('karyawan_id', 'karyawan_name', 'karyawan_nik', 'karyawan_npwp'
        , 'karyawan_birthdate', 'karyawan_phone', 'karyawan_email')
        ->with(['masakerja' => function($q) {
            // return $q->select();
        }])->where([
            'ms_wajibpajak_id' => $wajibpajak_id,
            'karyawan_email' => $email, 
            'karyawan_active' => 1
        ])->first();

        if(!$karyawan) {
            return response()->json([
                'success' => false,
                'message' => 'Karyawan tidak ditemukan'
            ]);
        }

        $where = [
            'user_id' => $user_id,
            'user_email' => $email,
            'usergroup_id' => $usergroup_id,
            'wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];
        $wajibpajak = WajibPajakModel::select('ms_wajib_pajak.*', 'mug.usergroup_id', 'mug.usergroup_name', 'tugm.usergroupmember_members'
        , 'muwp.userwajibpajak_name', 'muwp.userwajibpajak_phone', 'mu.user_id', 'mu.user_email', 'mk.karyawan_name')
        ->join('ms_user_group AS mug', 'mug.ms_wajibpajak_id', '=', 'ms_wajib_pajak.wajibpajak_id')
        ->join('tr_user_group_member AS tugm', 'tugm.ms_usergroup_id', '=', 'mug.usergroup_id')
        ->join("ms_user AS mu", "mu.user_id", '=', DB::raw("any(string_to_array(tugm.usergroupmember_members, ',')::bigint[])"))
        ->join('mr_user_wajib_pajak AS muwp', function($q) {
            $q->on('muwp.ms_user_id', '=', 'mu.user_id');
            $q->on('muwp.ms_wajibpajak_id', '=', 'wajibpajak_id');
        })
        ->leftJoin('ms_karyawan AS mk', 'mk.ms_user_id', '=', 'mu.user_id')
        ->where($where)->first();

        // dd($wajibpajak);
        if(!$wajibpajak) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ]);
        }

        DB::beginTransaction();
        try {
            // update ms_user_id in karyawan
            $karyawan->where(['karyawan_id' => $karyawan->karyawan_id])->update([
                'ms_user_id' => $wajibpajak->user_id
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil di tambahkan sebagai user',
                'data' => [
                    'karyawan' => $karyawan
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