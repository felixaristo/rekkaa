<?php

use App\Model\Master\KaryawanMasakerjaModel;
use App\Model\Mview\VwUsergroupModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

function check_perimission($menu_id, $code = '') {
    $decodewajibpajaksubpermissions = request()->get('wajibpajaksubpermissions');

    $currentpermission = null;
    foreach($decodewajibpajaksubpermissions as $wppermissions) {
        if($wppermissions->menu_id == $menu_id) {
            $currentpermission = $wppermissions;
            break;
        }
    }

    if(!$currentpermission) {
        return [
            'success' => false,
            'message' => 'no access',
        ];
    }
    // dd(!isset($currentpermission->$code));
    if(!isset($currentpermission->$code)) {
        die('no permission access');
    }

    // if($code == 'Q') {

    // }
    // return [
    //     'success' => true,
    //     'message' => 'has access',
    //     'data' => [
    //         'permission' => $currentpermission
    //     ]
    // ];
}