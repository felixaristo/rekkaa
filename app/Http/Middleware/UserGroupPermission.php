<?php

namespace App\Http\Middleware;

use App\Model\Mview\VwUsergroupModel;
use App\Model\Transaction\WajibPajakSubscriptionModel;
use Closure;
use Illuminate\Support\Facades\DB;

class UserGroupPermission
{
    var $wajibpajaksubpermissions = null;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->segment(1) == 'kalkulator' && session('user_data') == null) {
            return $next($request);
        }
        // $request->attributes->set('test', ['oo', 'aa', 'ss']);
        $menu_id = $request->get('menu_id');
        // dd($menu_id);
    // if(session('user_data') && $request->segment(3) !== 'kehadiran') {
        $user_id = session('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        // dd($wajibpajak_id);
        $usergroup = VwUsergroupModel::
        select('*'
        ,DB::raw("(ARRAY_TO_STRING(menus, ',') ) as menus_string")
        ,DB::raw("(SELECT json_agg(ptable) 
        FROM (
            SELECT permission_code, ms_menu_id, permission_period FROM ms_permission
            WHERE permission_active = '1'
            AND permission_id = ANY(string_to_array(usergroupaccess_permissions, ',')::bigint[])
        ) as ptable) as userpermission_json"))
        ->where([
            'wajibpajak_id' => $wajibpajak_id
        ])
        ->whereRaw("($user_id = ANY(string_to_array(members, ',')::bigint[]))")
        ->first();
        // dd($usergroup);  
        if(!$usergroup) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'noaccess' => true,
                    'message' => 'access forbidden',
                ]);
            } else {
                die('access forbidden');
            }
        }
        $menus_arr = ($usergroup->menus_string) ? explode(',', $usergroup->menus_string) : [];
        // Check if user has access to the menu
        if(!in_array($menu_id, $menus_arr)) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'noaccess' => true,
                    'message' => 'access forbidden',
                ]);
            } else {
                die('access forbidden');
            }
        }

        // check if permission menu is active or not. lookup to the mr_subscription_permission
        $decodesubpermissions = ($usergroup->subscriptionpermission_permissions) ? json_decode($usergroup->subscriptionpermission_permissions) : [];
        // dd($decodesubpermissions);
        if(count($decodesubpermissions) < 1) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'noaccess' => true,
                    'message' => 'access forbidden',
                ]);
            } else {
                die('access forbidden');
            }
        }

        $isactive = true;
        foreach($decodesubpermissions as $subpermissions) {
            if($menu_id == $subpermissions->menu_id) {
                if($subpermissions->is_active != 1) {
                    $isactive = false;
                    break;
                }
            }
        }
        if(!$isactive) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'noaccess' => true,
                    'message' => 'access forbidden',
                ]);
            } else {
                die('access forbidden');
            }
        }

        // dd($usergroup->wajibpajaksubscription_permission);
        // check permission from tr_wajibpajak_subscription
        $decodewajibpajakpermissions = ($usergroup->wajibpajaksubscription_permission) ? json_decode($usergroup->wajibpajaksubscription_permission) : [];
        if(count($decodewajibpajakpermissions) < 1) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'noaccess' => true,
                    'message' => 'access forbidden',
                ]);
            } else {
                die('access forbidden');
            }
        }

        // check permission code from ms_permission
        $decodeusergrouppermissions = ($usergroup->userpermission_json) ? json_decode($usergroup->userpermission_json) : [];
        if(count($decodeusergrouppermissions) < 1) {
            if($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'noaccess' => true,
                    'message' => 'access forbidden',
                ]);
            } else {
                die('access forbidden');
            }
        }
        
        // dd($decodeusergrouppermissions);
        foreach($decodeusergrouppermissions as &$ugroup) {
           foreach($decodewajibpajakpermissions as $wppermit) {
            
            if($ugroup->ms_menu_id == $wppermit->menu_id) {
                foreach($wppermit as $wpkey => $wpval) {
                    // dd($wpkey, $ugroup->permission_code);
                    $ugroup->permission_value = 0;
                    if($wpkey == $ugroup->permission_code) {
                        $ugroup->permission_value = $wpval;
                        break;
                    }
                }
                // if($ugroup->permission_code == $val) {

                // }
            }
           } 
        }
        $accesspermission = [];
        foreach($decodewajibpajakpermissions as $wpermission) {
            $wpermissionarray = (array) $wpermission;
            foreach($decodeusergrouppermissions as $ugpermission) {
                if($ugpermission->ms_menu_id == $wpermissionarray['menu_id']) {
                    if(isset($wpermissionarray[$ugpermission->permission_code])) {
                        array_push($accesspermission, (object) [
                            "permission_code" => $ugpermission->permission_code,
                            "ms_menu_id" => $ugpermission->ms_menu_id,
                            "permission_period" => $ugpermission->permission_period,
                            "permission_value" => $ugpermission->permission_value,
                        ]);
                    }
                }
            }
        }
        // dd($accesspermission);
        // dd($decodewajibpajakpermissions, $decodeusergrouppermissions);

        $request->attributes->set('accesspermission', $accesspermission);
        // $request->attributes->set('wajibpajakpermissions', $decodewajibpajakpermissions);
        return $next($request);
    }
}
