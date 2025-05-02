<?php

namespace App\Http\Middleware;

use App\Model\Master\KaryawanModel;
use App\Model\Master\UserGroupModel;
use App\Model\Setting\SettingLeaveModel;
use App\Model\Setting\SettingPenggajianKaryawanModel;
use App\Model\Setting\SettingPotonganKaryawanModel;
use App\Model\Setting\SettingTunjanganKaryawanModel;
use App\Model\Transaction\NotificationModel;
use Closure;
use Illuminate\Support\Facades\DB;

class AccessPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $code, $tableinfo=null)
    {
        if($request->segment(1) == 'kalkulator' && session('user_data') == null) {
            return $next($request);
        }
        $user_id = session('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $expcode = explode('-', $code);
        $exptable = explode(':',$tableinfo);
        $table = (count($exptable) > 0) ? $exptable[0] : null;
        // dd($exptable, $tableinfo);
        $tableflag = (count($exptable) > 1) ? explode('|', $exptable[1]) : null;
        // dd($expcode, $table);
        // dd($request->get('ms_wajibpajak_id'));
        $menu_id = $request->get('menu_id');
        $accesspermission = $request->get('accesspermission');
        // dd($usergrouppermissions);
        if(!$accesspermission) {
            if(request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'noaccess' => true,
                    'message' => 'access forbidden',
                ]);
            } else {
                die('access forbidden');
            }
        }
        
        $currentpermission = [];
        $currentpermission_codes = [];
        $permissionexist = false;
        foreach($accesspermission as $ugpermissions) {
            if($ugpermissions->ms_menu_id == $menu_id) {
                $currentpermission[] = $ugpermissions;
                $currentpermission_codes[] = $ugpermissions->permission_code;
            }
        }
        // dd($expcode, $currentpermission);
        // dd($expcode, $currentpermission_codes);
        $totalcount = 0;
        $quota = [
            'qty' => 0,
            'period' => 'PERIODE'
        ];

        
        if(strpos($code, '|') > -1) {
            $expcode = explode('|', $code);
            foreach($expcode as $cd) {
                if(in_array($cd, $currentpermission_codes)) {
                    $permissionexist = true;
                }
            }
        } 
        else {
            foreach($expcode as $cd) {
                if(in_array($cd, $currentpermission_codes)) {
                    $permissionexist = true;
                } else {
                    $permissionexist = false;
                    break;
                }
            }

            if(!$permissionexist) {
                if(request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'noaccess' => true,
                        'message' => 'access forbidden',
                    ]);
                } else {
                    die('access forbidden');
                }
            }
            
            $isquota = false;
            $emptytablename = false;
            // dd($expcode, $currentpermission);
            foreach($expcode as $code) {
                foreach($currentpermission as $cppermission) {
                    if($code == 'Q' && $cppermission->permission_code == 'Q') {
                        if (!$table) {
                            $emptytablename = true;
                            break;
                        }
                        $isquota = true;
                        $quota = [
                            'qty' => $cppermission->permission_value,
                            'period' => $cppermission->permission_period,
                        ];
                        break;
                    }
                    if($code == 'Q_SEND_EMAIL' && $cppermission->permission_code == 'Q_SEND_EMAIL') {
                        if (!$table) {
                            $emptytablename = true;
                            break;
                        }
                        $isquota = true;
                        $quota = [
                            'qty' => $cppermission->permission_value,
                            'period' => $cppermission->permission_period,
                        ];
                        break;
                    }
                }
            }
            if($emptytablename) {
                die('please provide table name');
            }
    
            // dd($isquota, $quota, $table, $tableflag[0]);
            if($isquota) {
                if($table == 'ms_karyawan') {
                    // dd($tableflag);
                    if($tableflag) {
                        if($tableflag[0] == 'KARYAWAN') {
                            // dd($tableflag[0]);
                            $totalcount = KaryawanModel::where([
                                'ms_wajibpajak_id' => $wajibpajak_id,
                            ])
                            ->whereNotIn('karyawan_status', ['NONKARYAWAN'])->count();
                            // dd($karyawancount);
                        } else {
                            $totalcount = KaryawanModel::where([
                                'ms_wajibpajak_id' => $wajibpajak_id,
                            ])
                            ->whereIn('karyawan_status', ['NONKARYAWAN'])->count();
                        }
                    } else {
                        $totalcount = KaryawanModel::where([
                            'ms_wajibpajak_id' => $wajibpajak_id,
                        ])->count();
                    };
    
                    if($totalcount >= $quota['qty']) {
                        // die('kuota habis');
                        return response()->json([
                            'success' => false,
                            'noquota' => true,
                            'message' => 'Kuota sudah habis!'
                        ]);
                    }
                }
                else if($table == 'st_tunjangan_karyawan') {
                    $totalcount = SettingTunjanganKaryawanModel::where([
                        'ms_wajibpajak_id' => $wajibpajak_id,
                    ])->count();
                    
                    if($totalcount >= $quota['qty']) {
                        // die('kuota habis');
                        return response()->json([
                            'success' => false,
                            'noquota' => true,
                            'message' => 'Kuota sudah habis!'
                        ]);
                    }
                }
                else if($table == 'st_potongan_karyawan') {
                    $totalcount = SettingPotonganKaryawanModel::where([
                        'ms_wajibpajak_id' => $wajibpajak_id,
                    ])->count();
                    
                    if($totalcount >= $quota['qty']) {
                        // dd($totalcount, $quota);
                        return response()->json([
                            'success' => false,
                            'noquota' => true,
                            'message' => 'Kuota sudah habis!'
                        ]);
                    }
                }
                else if($table == 'st_penggajian_karyawan') {
                    $totalcount = SettingPenggajianKaryawanModel::where([
                        'ms_wajibpajak_id' => $wajibpajak_id,
                    ])->count();
                    
                    if($totalcount >= $quota['qty']) {
                        // dd($totalcount, $quota);
                        return response()->json([
                            'success' => false,
                            'noquota' => true,
                            'message' => 'Kuota sudah habis!'
                        ]);
                    }
                }
                else if($table == 'ms_user_group') {
                    $totalcount = UserGroupModel::where([
                        'ms_wajibpajak_id' => $wajibpajak_id,
                    ])->count();
                    
                    // dd($totalcount, $quota);
                    if($totalcount >= $quota['qty']) {
                        return response()->json([
                            'success' => false,
                            'noquota' => true,
                            'message' => 'Kuota sudah habis!'
                        ]);
                    }
                }
                else if($table == 'st_leave') {
                    $totalcount = SettingLeaveModel::where([
                        'ms_wajibpajak_id' => $wajibpajak_id,
                    ])->count();
                    
                    // dd($totalcount, $quota);
                    if(!$request->input('isEdit')) {
                        if($totalcount >= $quota['qty']) {
                            return response()->json([
                                'success' => false,
                                'noquota' => true,
                                'message' => 'Kuota sudah habis!'
                            ]);
                        }
                    }
                }
                else if($table == 'tr_notification') {
                    // dd('tesst', $tableflag[0]);
                    if($tableflag[0] == 'CALCULATOR_PPH4a2_SEND_EMAIL') {
                        $totalcount = NotificationModel::where([
                            'ms_permission_code' => $tableflag[0],
                            'ms_wajibpajak_id' => $wajibpajak_id,
                        ])->where(DB::raw('DATE(notification_created_at)'), '=', date('Y-m-d'))->count();
                        // melebihi batas
                        if($totalcount >= $quota['qty']) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Kuota sudah habis!'
                            ]);
                        }
                    } else {
                        // dd($request->input('tipekalkulator'));
                        if($request->input('tipekalkulator') != 'nonkaryawan') {
                            
                            $totalcount = NotificationModel::where([
                                'ms_permission_code' => $tableflag[0],
                                'ms_wajibpajak_id' => $wajibpajak_id,
                            ])->where(DB::raw('DATE(notification_created_at)'), '=', date('Y-m-d'))->count();
                            // melebihi batas
                            if($totalcount >= $quota['qty']) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Kuota sudah habis!'
                                ]);
                            }
                            
                        } else {
                            $totalcount = NotificationModel::where([
                                'ms_permission_code' => $tableflag[1],
                                'ms_wajibpajak_id' => $wajibpajak_id,
                            ])->where(DB::raw('DATE(notification_created_at)'), '=', date('Y-m-d'))->count();
                            // dd($totalcount);
                            // melebihi batas
                            if($totalcount >= $quota['qty']) {
                                return response()->json([
                                    'success' => false,
                                    'message' => 'Kuota sudah habis!'
                                ]);
                            }
                        }
                    }
                }
            }
        }
        
        
        // dd('test', $table);
        // dd($quota, $totalcount);
        $request->attributes->set('permission_codes', $currentpermission_codes);
        $request->attributes->set('quota', [
            'totalquota' => $totalcount,
            'maxquota' => $quota['qty'],
            'period' => $quota['period']
        ]);
        // dd($totalcount);
        return $next($request);
    }
}
