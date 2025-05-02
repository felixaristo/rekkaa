<?php

use App\Model\Master\SubscriptionMenuGroupModel;
use App\Model\Master\KaryawanMasakerjaModel;
use App\Model\Master\MenuModel;
use App\Model\Master\PermissionModel;
use App\Model\Master\UserGroupMemberModel;
use App\Model\Master\UserGroupModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Mview\VwMenuModel;
use App\Model\Mview\VwUsergroupModel;
use App\Model\Setting\SettingModel;
use App\Model\Transaction\WajibPajakSubscriptionModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function getWajibPajakGroup($wajibpajak_id = null) {
    $user_id = session('user_data')['user_id'];

    $where = [
        'usergroupmember_active' => 1,
        'usergroup_active' => 1,
    ];
    $method = 'get';
    if($wajibpajak_id) {
        $where['wajibpajak_id'] = $wajibpajak_id;
        $method = 'first';
    }
    
    $wajibpajak = UserGroupMemberModel::
    select('usergroup_name', 'wajibpajak_id', 'wajibpajak_name', 'wajibpajak_type', 'wajibpajak_npwp', 'wajibpajak_nik'
    , 'wajibpajak_email', 'wajibpajak_phone', 'subscription_title', 'subscription_type', 'userwajibpajak_name')
    ->join('ms_user_group', 'usergroup_id', '=', 'tr_user_group_member.ms_usergroup_id')
    ->join('ms_wajib_pajak', 'wajibpajak_id', '=', 'ms_user_group.ms_wajibpajak_id')
    ->join('mr_user_wajib_pajak', function($q) {
        $q->on('wajibpajak_id', '=', 'mr_user_wajib_pajak.ms_wajibpajak_id');
        $q->on('ms_wajib_pajak.ms_user_id', '=', 'mr_user_wajib_pajak.ms_user_id');
    })
    ->join('tr_wajib_pajak_subscription', function($q) {
        $q->on('wajibpajak_id', '=', 'tr_wajib_pajak_subscription.ms_wajibpajak_id');
        $q->where('wajibpajaksubscription_active', '=', '1');
    })
    ->join('ms_subscription', 'subscription_id', '=', 'tr_wajib_pajak_subscription.ms_subscription_id')
    ->where($where)
    ->whereRaw("($user_id = ANY(string_to_array(usergroupmember_members, ',')::bigint[]))")
    ->$method();

    return $wajibpajak;
}

function getCurrentAccessGroup() {
    // dd( session()->get('wajibpajak_current'));
    $user_id = session('user_data')['user_id'];
    $wajibpajak_id = (session()->get('wajibpajak_current')) ? session()->get('wajibpajak_current')['wajibpajak_id'] : 0;

    $where = [
        'wajibpajak_id' => $wajibpajak_id
    ];
    
    $wajibpajak = VwUsergroupModel::
    select('*'
    , DB::raw("(SELECT json_agg(ptable) 
    FROM (
        SELECT DISTINCT * FROM vw_menu vm
            WHERE vm.menu_id IN (
            SELECT UNNEST(ancestry) AS menu_id FROM public.vw_menu
            WHERE menu_id = ANY(ARRAY[menus])
            AND menu_active = '1'
        ) ORDER BY menu_parent ASC, menu_position ASC
    )
    as ptable) as menu_json"))
    ->where($where)
    ->whereRaw("($user_id = ANY(string_to_array(members, ',')::bigint[]))")
    ->first();

    // dd($wajibpajak->wajibpajaksubscription_permission);

    $menuids = [];
    $tempmenu = [];
    $activemenu_ids = [];
    if($wajibpajak) {
        $subpermissions = ($wajibpajak->wajibpajaksubscription_permission) ? json_decode($wajibpajak->wajibpajaksubscription_permission) : [];
        if($subpermissions) {
            foreach($subpermissions as $subper) {
                if(isset($subper->is_active)) {
                    if($subper->is_active == '1') {
                        array_push($activemenu_ids, $subper->menu_id);
                    }
                }
            }
        }
        // dd($subpermissions);

        
        if($wajibpajak) {
            $menus = ($wajibpajak['menu_json']) ? json_decode($wajibpajak['menu_json']) : [];
            // dd($menus);
            if(count($menus) > 0) {
                foreach($menus as $menu) {
                    if($menu->menu_parent == 0) {
                        $menu->children = [];
                        array_push($tempmenu, $menu);
                        array_push($menuids, $menu->menu_id);
                    }
                }
                foreach($tempmenu as &$tmenu) {
                    
                    foreach($menus as $menu) {
                            if($tmenu->menu_id == $menu->menu_parent) {
                                $tmenu->children[] = $menu;
                            }
                            foreach($tmenu->children as $tchild) {
                                if($tchild->menu_id == $menu->menu_parent) {
                                    $tchild->children[] = $menu;
                                }
                            }
                    }
                }
            }
        }
        $newmenu = [];
        $i = 0;
        foreach($tempmenu as &$tmp) {
            $tmp = (array) $tmp;
            if(!isset($tmp['children'])) {
                if(!in_array($tmp['menu_id'], $activemenu_ids)) {
                    unset($tempmenu[$i]);
                }
            } else {
                $j=0;
                foreach($tmp['children'] as &$tchild) {
                    $tchild = (array) $tchild;
                    if(!isset($tchild['children'])) {
                        if(!in_array($tchild['menu_id'], $activemenu_ids)) {
                            unset($tempmenu[$i]['children'][$j]);
                            if(count($tempmenu[$i]['children']) == 0) {
                                unset($tempmenu[$i]);
                            }
                        }
                        
                    } else {
                        $k = 0;
                        foreach($tchild['children'] as &$grandchild) {
                            $grandchild = (array) $grandchild;
                            if(!isset($grandchild['children'])) {
                                if(!in_array($grandchild['menu_id'], $activemenu_ids)) {
                                    unset($tempmenu[$i]['children'][$j]['children'][$k]);
                                    if(count($tempmenu[$i]['children'][$j]['children']) == 0) {
                                        unset($tempmenu[$i]['children'][$j]);
                                    }
                                    if(count($tempmenu[$i]['children']) == 0) {
                                        unset($tempmenu[$i]);
                                    }
                                }
                                
                            }
                            $k++;
                        }
                    }
                    $j++;
                }
            }
            $i++;
        }
    }
    // var_dump($activemenu_ids);
    // $tempmenu
    // dd($tempmenu);

    return $tempmenu;
}

function getSubscriptionPermission() {
    $user_id = session('user_data')['user_id'];
    $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

    $where = [
        'wajibpajak_id' => $wajibpajak_id
    ];
    
    $wajibpajak = VwUsergroupModel::
    select('*'
    , DB::raw("(SELECT json_agg(ptable) 
    FROM (
        SELECT vm.menu_id, vm.menu_title, vm.menu_parent
        , (SELECT json_agg(permissiontable) 
        FROM (
            SELECT msp.*
            FROM ms_permission as msp
            WHERE msp.ms_menu_id = vm.menu_id
        )
        as permissiontable) as permission_jsonxxx
        FROM vw_menu vm
        WHERE vm.menu_id IN (
            SELECT UNNEST(ancestry) AS menu_id FROM public.vw_menu
            WHERE menu_id = ANY(ARRAY[menus])
        ) ORDER BY menu_parent ASC
    )
    as ptable) as menu_json"))
    ->where($where)
    ->whereRaw("($user_id = ANY(string_to_array(members, ',')::bigint[]))")
    ->first();

    $permission_data = [];
    // dd($wajibpajak->menu_json);
    // BUG, SHOULD SHOW THE DATA FROM mr_subscription_permission FIRST
    $activemenu_ids = [];
    $subpermissions = ($wajibpajak->wajibpajaksubscription_permission) ? json_decode($wajibpajak->wajibpajaksubscription_permission) : [];
    if($subpermissions) {
        foreach($subpermissions as $subper) {
            if(isset($subper->is_active)) {
                if($subper->is_active == '1') {
                    array_push($activemenu_ids, $subper->menu_id);
                }
            }
        }
    }
    // dd($subpermissions);
    $permissions = PermissionModel::select('ms_permission.*', 'ms_menu.menu_id', 'ms_menu.menu_title', 'ms_menu.menu_parent')
    ->join('ms_menu', 'menu_id','=','ms_menu_id')
    ->where(['permission_active' => '1'])->whereIn('ms_menu_id', $activemenu_ids)->get()->toArray();
    // return $permissions;
    // dd($permissions);
    foreach($permissions as $permit) {
        foreach($subpermissions as $subper) {
            $subperarray = (array) $subper;
            // if(in_array($key))
            // dd($subper);
            if($subperarray['menu_id'] == $permit['ms_menu_id']) {
                // dd($key);
                // in_array($val, $activemenu_ids)
                if(isset($subperarray[$permit['permission_code']])) {
                    array_push($permission_data, $permit);
                }
            }
        }
    }
    // dd('assd',$permission_data);
    // dd($permission_json[0]);
    // dd(json_decode($wajibpajak['menu_json']));

    $menuids = [];
    $tempmenu = [];
    if($wajibpajak) {
        $menus = ($wajibpajak['menu_json']) ? json_decode($wajibpajak['menu_json']) : [];
        $menuidss = [];
        foreach($menus as $mn) {
            array_push($menuidss, $mn->menu_id);
        }
        // dd($menus);
        foreach($permission_data as $per) {
            $im = count($menus) - 1;
            // foreach($menus as $mn) {
                
                if(!in_array($per['ms_menu_id'], $menuidss)) {
                    // var_dump($per);
                    $menus[$im]->menu_id = $per['ms_menu_id'];
                    $menus[$im]->menu_title = $per['menu_title'];
                    $menus[$im]->menu_parent = $per['menu_parent'];

                    
                    $im++;
                }
            // }
        }
        // $menus = (object)$permissions;
        // dd('end');
        usort($menus, function ($item1, $item2) {
            return $item1->menu_parent <=> $item2->menu_parent;
        });
        // dd($menus);
        if(count($menus) > 0) {
            foreach($menus as &$menu) {
                if($menu->menu_parent == 0) {
                    $menu->children = [];

                    foreach($permission_data as $permitdt) {
                        if($permitdt['ms_menu_id'] == $menu->menu_id) {
                            $menu->permission_json[] = $permitdt;
                        }
                    }

                    array_push($tempmenu, $menu);
                    array_push($menuids, $menu->menu_id);
                }
            }
            foreach($tempmenu as &$tmenu) {
                
                foreach($menus as &$menu) {
                    if($tmenu->menu_id == $menu->menu_parent) {
                        foreach($permission_data as $permitdt) {
                            if($permitdt['ms_menu_id'] == $menu->menu_id) {
                                $menu->permission_json[] = $permitdt;
                            }
                        }

                        $tmenu->children[] = $menu;
                    }
                    foreach($tmenu->children as $tchild) {
                        if($tchild->menu_id == $menu->menu_parent) {
                            foreach($permission_data as $permitdt) {
                                if($permitdt['ms_menu_id'] == $menu->menu_id) {
                                    $menu->permission_json[] = $permitdt;
                                }
                            }

                            $tchild->children[] = $menu;
                        }
                    }
                }
            }
        }
    }
    $newmenu = [];
    $i = 0;
    foreach($tempmenu as &$tmp) {
        $tmp = (array) $tmp;
        if(!isset($tmp['children'])) {
            if(!in_array($tmp['menu_id'], $activemenu_ids)) {
                unset($tempmenu[$i]);
            }
        } else {
            $j=0;
            foreach($tmp['children'] as &$tchild) {
                $tchild = (array) $tchild;
                if(!isset($tchild['children'])) {
                    if(!in_array($tchild['menu_id'], $activemenu_ids)) {
                        // unset($tempmenu[$i]['children'][$j]);
                        if(count($tempmenu[$i]['children']) == 0) {
                            unset($tempmenu[$i]);
                        }
                    }
                    
                } else {
                    $k = 0;
                    foreach($tchild['children'] as &$grandchild) {
                        $grandchild = (array) $grandchild;
                        if(!isset($grandchild['children'])) {
                            if(!in_array($grandchild['menu_id'], $activemenu_ids)) {
                                // unset($tempmenu[$i]['children'][$j]['children'][$k]);
                                if(count($tempmenu[$i]['children'][$j]['children']) == 0) {
                                    unset($tempmenu[$i]['children'][$j]);
                                }
                                if(count($tempmenu[$i]['children']) == 0) {
                                    unset($tempmenu[$i]);
                                }
                            }
                            
                        }
                        $k++;
                    }
                }
                $j++;
            }
        }
        $i++;
    }
    // var_dump($activemenu_ids);
    // $tempmenu
    // dd($tempmenu);

    return $tempmenu;
}

function getMenu() {
    
    $menus = DB::select("SELECT *,
    (SELECT json_agg(ptable) 
    FROM (
        SELECT * FROM ms_permission pm
            WHERE pm.ms_menu_id = vm.menu_id
            AND pm.permission_active = '1'
            ORDER BY pm.permission_order ASC, pm.permission_code_name ASC
    ) as ptable) as permission_json FROM vw_menu vm
            WHERE vm.menu_id IN (
            SELECT UNNEST(ancestry) AS menu_id FROM public.vw_menu
            WHERE menu_active = '1'
        ) ORDER BY menu_parent ASC, menu_position ASC");
    // dd($menus);

    // dd($wajibpajak->wajibpajaksubscription_permission);
    $menuids = [];
    $tempmenu = [];
    if(count($menus) > 0) {
        foreach($menus as $menu) {
            if($menu->menu_parent == 0) {
                $menu->children = [];
                array_push($tempmenu, $menu);
                array_push($menuids, $menu->menu_id);
            }
        }
        foreach($tempmenu as &$tmenu) {
            
            foreach($menus as $menu) {
                    if($tmenu->menu_id == $menu->menu_parent) {
                        $tmenu->children[] = $menu;
                    }
                    foreach($tmenu->children as $tchild) {
                        if($tchild->menu_id == $menu->menu_parent) {
                            $tchild->children[] = $menu;
                        }
                    }
            }
        }
    }

    return $tempmenu;
}