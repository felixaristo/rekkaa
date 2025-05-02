<?php
namespace App\Http\Controllers\User\Pengaturan;

use App\Http\Controllers\Controller;
use App\Model\Master\PermissionModel;
use App\Model\Master\UserGroupAccessModel;
use App\Model\Master\UserGroupMemberModel;
use App\Model\Master\UserGroupModel;
use App\Model\Master\WajibPajakModel;
use App\Model\MasterRelation\MrSubscriptionPermissionModel;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\Model\Setting\SettingBpjsKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use App\Model\Transaction\NotificationModel;
use App\Model\Transaction\WajibPajakSubscriptionModel;
use App\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PengaturanGroupAksesController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd(getSubscriptionPermission());
        // return response()->json(getSubscriptionPermission());
        $data = [
            'title' => 'Kelola User Akses',
            'content' => 'user.pengaturan.group-akses.index',
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
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $limit = ($request->input('length')) ? intval($request->input('length')) : 10;
        $offset = ($request->input('start')) ? intval($request->input('start')) : 0;
        $search = (isset($request->input('search')['value']) && $request->input('search')['value']) ? $request->input('search')['value'] : null;
        $order_columns = ['usergroup_name'];
        $order_col = $order_columns[0];
        $order_type = 'asc';
        if(isset($request->input('order')[0])) {
            $colidx = (isset($request->input('order')[0]['column'])) ? $request->input('order')[0]['column'] : 0;
            $order_col = (isset($order_columns[$colidx])) ? $order_columns[$colidx] : $order_columns[0];
            $order_type = (isset($request->input('order')[0]['dir'])) ? $request->input('order')[0]['dir'] : 'asc';
        }

        $where = [
            'usergroup_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ];
        $list = UserGroupModel::select("*"
            , DB::raw("(SELECT (SELECT json_agg(utable) 
            FROM (
                SELECT user_id, user_email
                FROM ms_user
                WHERE
                user_id = ANY(string_to_array(
                    (SELECT usergroupmember_members FROM tr_user_group_member WHERE ms_usergroup_id = usergroup_id)    
                , ',')::int[])
                AND user_active = '1'
                ORDER BY user_email ASC
            )
            as utable)) as user_json")
        )->when($search, function ($q, $search) {
            $lowercaseSearch = strtolower($search);
            return $q->where(function ($query) use ($lowercaseSearch) {
                $query->where(DB::raw('LOWER(usergroup_name)'), 'like', '%' . $lowercaseSearch . '%');
            });
        })
        ->with(['usergroupaccess'])
        ->where($where)
        ->take($limit)->skip($offset)
        ->orderBy($order_col, $order_type)
        ->get();

        foreach($list as &$ls) {
            $ls->user = ($ls->user_json) ? json_decode($ls->user_json) : null;
            unset($ls->user_json);
        }
        $data['draw'] = $request->input('draw');
		$data['recordsTotal'] = UserGroupModel::where($where)->count();
		$data['recordsFiltered'] = $data['recordsTotal'];
        $data['data'] = $list;
        
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $permission_ids = $request->input('permission_ids');
        $usergroup_email = $request->input('usergroup_email');

        $this->validate($request, [
            'usergroup_name' => ['required',
                Rule::unique('ms_user_group')->where(function ($query) use ($wajibpajak_id) {
                    return $query->where([
                        'ms_wajibpajak_id' => $wajibpajak_id
                    ]);
                })
            ],
            'permission_ids' => 'required',
            'usergroup_email' => 'required|array|min:1',
            'usergroup_email.*' => 'required|email',
        ], [
            'usergroup_name.required' => 'Nama Group Akses wajib diisi!',
            'usergroup_name.unique' => 'Nama Group Akses sudah digunakan!',
            'permission_ids.required' => 'Hak Akses wajib diisi!',
            'usergroup_email.required' => 'Email User wajib diisi!',
            'usergroup_email.*.email' => 'Format Email tidak sesuai!',
        ]);

        // check if email already set on other entity or other access
        $user = User::whereIn('user_email', $usergroup_email)->get();
        if(count($user) > 0) {
            $user_exist = [];
            foreach($user as $us) {
                if(in_array($us->user_email, $usergroup_email)) {
                    // array_push($user_exist, $us->user_id);
                    $groupmember = DB::select(DB::raw(" 
                        SELECT usergroupmember_id
                        FROM tr_user_group_member WHERE {$us->user_id} = ANY(string_to_array(usergroupmember_members, ',')::bigint[])
                        AND usergroupmember_active = '1' "
                    ));
                    if(count($groupmember) > 0) {
                        array_push($user_exist, $us->user_email);
                    }
                }
            }
            if($user_exist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email '.implode(',', $user_exist).' sudah terdaftar di hak akses lain atau entitas lain!',
                ]);
            }
        }

        $wajibpajak = WajibPajakModel::where(['wajibpajak_id' => $wajibpajak_id, 'wajibpajak_active' => '1'])->first();

        $save_data = [
            'usergroup_name' => ucwords($request->input('usergroup_name')),
            'ms_wajibpajak_id' => $wajibpajak_id,
        ];

        DB::beginTransaction();
        try {
            $usergroup = UserGroupModel::create($save_data);

            // insert ke user group access
            $usergroupaccess = UserGroupAccessModel::create([
                'ms_usergroup_id' => $usergroup->usergroup_id,
                'usergroupaccess_permissions' => implode(',', $permission_ids)
            ]);

            $userwajibpajak_data = [];
            $member_data = [];
            $notification_data = [];
            foreach($usergroup_email as $email) {
                $random_password = Str::random(6);
                $generate_token = Hash::make('VRA'.$email.uniqid());
                $generate_forgottoken = Hash::make('FGP'.$email.uniqid());
                $user = User::create([
                    'user_email' => $email,
                    'user_password' => Hash::make($random_password),
                    'user_verified_token' => null,
                    'user_email_verified_at' => date('Y-m-d H:i:s'),
                    'user_forgot_token' => $generate_forgottoken,
                ]);
                
                $name = substr($email, 0, strpos($email, '@'));
                array_push($userwajibpajak_data, [
                    'ms_user_id' => $user->user_id,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'userwajibpajak_name' => $name,
                    'userwajibpajak_phone' => null,
                    'userwajibpajak_owner' => 0,
                ]);

                array_push($member_data, $user->user_id);

                array_push($notification_data, [
                    'notification_title' => 'Rekkaa - Undangan Dari '.$wajibpajak->wajibpajak_name,
                    'notification_type' => 'EMAIL',
                    'notification_from' => env("MAIL_FROM_ADDRESS"),
                    'notification_to' => $user->user_email,
                    'ms_user_id' => $user->user_id,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'notification_view' => 'email.email-undangan-akun',
                    'notification_data' => json_encode([
                        'entity' => [
                            'name' => $wajibpajak->wajibpajak_name,
                            'user_name' => $name,
                            'inviter_email' => session()->get('user_data')['user_email']
                        ]
                    ])
                ]);
            }
            if($userwajibpajak_data) {
                MrUserWajibPajakModel::insert($userwajibpajak_data);
            }

            if($member_data) {
                UserGroupMemberModel::create([
                    'ms_usergroup_id' => $usergroup->usergroup_id,
                    'usergroupmember_members' => implode(',', $member_data),
                ]);
            }

            // insert tr_notification
            if($notification_data) {
                NotificationModel::insert($notification_data);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kelola User Akses berhasil disimpan',
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

    /**
     * Display a select of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function select(Request $request)
    {
        $q = $request->get('q');
        // dd($q);
        $limit = $request->get('limit') ? $request->get('limit') : 20;

        $where = [
            'usergroup_active' => 1,
            'ms_wajibpajak_id' => session()->get('wajibpajak_current')['wajibpajak_id'],
        ];

        $data = UserGroupModel::select('usergroup_id', 'usergroup_name')
        ->when($q, function ($query, $q) {
            return $query->where('usergroup_name', 'ilike', '%'.$q.'%');
        })
        ->where($where)
        ->limit($limit)->get();
        
        return response()->json([
            'success' => 200,
            'data' => $data
        ]);
    }

    public function update($usergroupId, Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $permission_ids = $request->input('permission_ids');
        $usergroup_email = $request->input('usergroup_email');

        // dd($permission_ids);
        $this->validate($request, [
            // 'usergroup_name' => 'required',
            'permission_ids' => 'required',
            'usergroup_email' => 'required|array|min:1',
            'usergroup_email.*' => 'required|email',
        ], [
            // 'usergroup_name.required' => 'Nama Group Akses wajib diisi!',
            'permission_ids.required' => 'Hak Akses wajib diisi!',
            'usergroup_email.required' => 'Email User wajib diisi!',
            'usergroup_email.*.email' => 'Format Email tidak sesuai!',
        ]);

        // check if email already set on other entity or other access
        $user = User::whereIn('user_email', $usergroup_email)->get();
        if(count($user) > 0) {
            $user_exist = [];
            foreach($user as $us) {
                if(in_array($us->user_email, $usergroup_email)) {
                    // array_push($user_exist, $us->user_id);
                    $groupmember = DB::select(DB::raw(" 
                        SELECT usergroupmember_id
                        FROM tr_user_group_member WHERE {$us->user_id} = ANY(string_to_array(usergroupmember_members, ',')::bigint[])
                        AND usergroupmember_active = '1' 
                        AND ms_usergroup_id <> :usergroup_id"
                    ), ['usergroup_id' => $usergroupId]);
                    if(count($groupmember) > 0) {
                        array_push($user_exist, $us->user_email);
                    }
                }
            }
            if($user_exist) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email '.implode(',', $user_exist).' sudah terdaftar di hak akses lain atau entitas lain!',
                ]);
            }
        }

        $usergroup = UserGroupModel::select('*'
        , DB::raw("(SELECT (SELECT json_agg(utable) 
            FROM (
                SELECT user_id, user_email
                FROM ms_user
                WHERE
                user_id = ANY(string_to_array(
                    (SELECT usergroupmember_members FROM tr_user_group_member WHERE ms_usergroup_id = usergroup_id)    
                , ',')::int[])
                AND user_active = '1'
                ORDER BY user_email ASC
            )
            as utable)) as user_json"))->with(['wajibpajak'])->where([
            'usergroup_id' => $usergroupId, 
            'usergroup_active' => 1,
            'ms_wajibpajak_id' => $wajibpajak_id,
        ])->first();
        if(!$usergroup) {
            return response()->json([
                'success' => false,
                'message' => 'User Group tidak ditemukan!'
            ]); 
        }
        
        // find existing user
        // dd($usergroup);
        $user = ($usergroup->user_json) ? json_decode($usergroup->user_json) : null;
        $owner_exist = false;
        $new_useremail = [];
        $old_useremail = [];
        $member_data = [];
        if($user) {
            foreach($user as $us) {
                array_push($old_useremail, $us->user_email);
                array_push($member_data, $us->user_id);
            }
        }

        foreach($usergroup_email as $email) {
            if(!in_array($email, $old_useremail)) {
                array_push($new_useremail, $email);
            }
        }

        $deleted_emails = array_diff($old_useremail, $usergroup_email);
        // dd($deleted_emails);
        foreach($user as $us) {
            if(in_array($us->user_email, $deleted_emails)) {
                // array_push($new_useremail, $email);
                $key = array_search($us->user_id, $member_data);
                if(isset($member_data[$key])) {
                    unset($member_data[$key]);
                }
            }
        }
        if(in_array($usergroup->wajibpajak->ms_user_id, $member_data)) {
            $owner_exist = true;
        }
        // dd('popo', $member_data);
        if($owner_exist == false) {
            if($usergroup->usergroup_code == 'REKKAA' && $usergroup->usergroup_name == 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pemilik tidak bisa dihapus!'
                ]); 
            }
        }

        $permissions = PermissionModel::where(['permission_active' => '1'])->whereIn('permission_id', $permission_ids)->get();
        // check the subscription permission
        $wppermission = WajibPajakSubscriptionModel::where([
            'wajibpajaksubscription_active' => '1',
            'ms_wajibpajak_id' => $wajibpajak_id
        ])->orderBy('wajibpajaksubscription_id', 'DESC')->first();

        $subscriptionpermission = json_decode($wppermission->wajibpajaksubscription_permission);
        // dd($subscriptionpermission, $permissions[0]);
        $newpermission_ids = [];
        foreach($subscriptionpermission as $subcriptionpermit) {
            $subcriptionpermitarray = (array) $subcriptionpermit;
            foreach($permissions as $permit) {
                if($subcriptionpermitarray['menu_id'] == $permit->ms_menu_id) {
                    if(isset($subcriptionpermitarray[$permit->permission_code])) {
                        array_push($newpermission_ids, $permit->permission_id);
                    }
                }
            }
        }

        // dd($newpermission_ids, $permission_ids);
        DB::beginTransaction();
        try {
            // update ke user group access
            $usergroupaccess = UserGroupAccessModel::where([
                'ms_usergroup_id' => $usergroup->usergroup_id,
            ])->update([
                'usergroupaccess_permissions' => implode(',', $newpermission_ids)
            ]);

            $userwajibpajak_data = [];
            $notification_data = [];
            foreach($new_useremail as $email) {
                $random_password = Str::random(6);
                $generate_token = Hash::make('VRA'.$email.uniqid());
                $generate_forgottoken = Hash::make('FGP'.$email.uniqid());
                $user = User::create([
                    'user_email' => $email,
                    'user_password' => Hash::make($random_password),
                    'user_verified_token' => $generate_token,
                    'user_forgot_token' => $generate_forgottoken,
                ]);
                
                $name = substr($email, 0, strpos($email, '@'));
                array_push($userwajibpajak_data, [
                    'ms_user_id' => $user->user_id,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'userwajibpajak_name' => $name,
                    'userwajibpajak_phone' => null,
                    'userwajibpajak_owner' => 0,
                ]);

                array_push($member_data, $user->user_id);

                array_push($notification_data, [
                    'notification_title' => 'Rekkaa - Undangan Dari '.$usergroup->wajibpajak->wajibpajak_name,
                    'notification_type' => 'EMAIL',
                    'notification_from' => env("MAIL_FROM_ADDRESS"),
                    'notification_to' => $user->user_email,
                    'ms_user_id' => $user->user_id,
                    'ms_wajibpajak_id' => $wajibpajak_id,
                    'notification_view' => 'email.email-undangan-akun',
                    'notification_data' => json_encode([
                        'entity' => [
                            'name' => $usergroup->wajibpajak->wajibpajak_name,
                            'user_name' => $name,
                            'inviter_email' => session()->get('user_data')['user_email']
                        ]
                    ])
                ]);
            }
            if($userwajibpajak_data) {
                MrUserWajibPajakModel::insert($userwajibpajak_data);
            }

            UserGroupMemberModel::where([
                'ms_usergroup_id' => $usergroup->usergroup_id,
            ])
            ->update([
                'usergroupmember_members' => implode(',', $member_data),
            ]);

            // insert tr_notification
            if($notification_data) {
                NotificationModel::insert($notification_data);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Kelola User Akses berhasil dirubah.',
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
        // dd(request()->input());
        // $user_id = request()->input('user_id');
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

        if($usergroup->usergroup_code == 'REKKAA' && $usergroup->usergroup_name == 'Admin') {
            return response()->json([
                'success' => false,
                'message' => 'User Group Admin tidak bisa dihapus!'
            ]); 
        }

        DB::beginTransaction();
        try {
            $usergroup->update(['usergroup_active' => '0']);

            UserGroupMemberModel::where([
                'ms_usergroup_id' => $usergroup->usergroup_id,
            ])
            ->update([
                'usergroupmember_active' => 't', 
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Grup Akses berhasil dihapus',
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