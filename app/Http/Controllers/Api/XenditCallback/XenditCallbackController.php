<?php

namespace App\Http\Controllers\Api\XenditCallback;

use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Model\Master\PermissionModel;
use App\Model\Master\SubscriptionModel;
use App\Model\Master\UserGroupAccessModel;
use App\Model\Master\UserGroupMemberModel;
use App\Model\Master\UserGroupModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Transaction\NotificationModel;
use App\Model\Transaction\UserOrderModel;
use App\Model\Transaction\WajibPajakSubscriptionModel;
use App\User;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class XenditCallbackController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function callback(Request $request)
    {   
        $external_id = $request->input('external_id');
        $status = $request->input('status');
        // $paid_amount = $request->input('amount');
        $payment_method = $request->input('payment_method');
        $payment_channel = $request->input('payment_channel');
        $order = UserOrderModel::select('tr_user_order.*'
        , 'user_id', 'user_email', 'ms_wajib_pajak.wajibpajak_id'
        , 'ms_wajib_pajak.wajibpajak_name', 'ms_wajib_pajak.wajibpajak_type'
        , 'ms_wajib_pajak.wajibpajak_phone'
        , DB::raw("(SELECT wajibpajaksubscription_expired_at FROM tr_wajib_pajak_subscription WHERE 
        tr_wajib_pajak_subscription.ms_wajibpajak_id = tr_user_order.ms_wajibpajak_id
        AND tr_wajib_pajak_subscription.ms_subscription_id = tr_user_order.ms_subscription_id
        AND TO_CHAR(tr_wajib_pajak_subscription.wajibpajaksubscription_expired_at, 'YYYY-MM-DD') >= '".date('Y-m-d')."'
        AND tr_wajib_pajak_subscription.wajibpajaksubscription_active='1') as prevwajibpajaksubcription_expired_at"))->where([
            'userorder_no' => $external_id,
            'userorder_status' => 'PENDING'
        ])
        ->join('ms_user', 'tr_user_order.ms_user_id', '=', 'ms_user.user_id')
        ->join('ms_wajib_pajak', 'tr_user_order.ms_wajibpajak_id', '=', 'ms_wajib_pajak.wajibpajak_id')
        ->orderBy('userorder_created_at', 'DESC')->first();
        // dd($order);
        if(!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan!'
            ], 404);
        }
        // dd($order);
        // dd($status);
        $addon = ($order->userorder_subscriptiondata) ? json_decode($order->userorder_subscriptiondata) : null;
        // dd($addon);
        $addonids = [];
        $addonsavedata = [];
        if($addon) {
            if(isset($addon->listaddon) && $addon->listaddon) {
                foreach($addon->listaddon as $ad) {
                    array_push($addonids, $ad->id);
                }
                $addon_permissions = PermissionModel::where([
                    'permission_active' => 1
                ])
                ->whereIn('permission_id', $addonids)->get();
                
                // dd($addon_permissions);
                if($addon_permissions) {
                    foreach($addon_permissions as $adp) {
                        foreach($addon->listaddon as $ad) {
                            if($ad->id == $adp->permission_id) {
                                if(count($addonsavedata) > 0) { //update
                                    $ismenuexist = false;
                                    foreach($addonsavedata as &$adsvdt) {
                                        if($adsvdt['menu_id'] == $adp->ms_menu_id) {
                                            $adsvdt[$adp->permission_code] = $ad->qty;
                                            $ismenuexist = true;
                                            break;
                                        }
                                    }
                                    if($ismenuexist == false) {
                                        array_push($addonsavedata, [
                                            $adp->permission_code => $ad->qty,
                                            'menu_id' =>  $adp->ms_menu_id
                                        ]);
                                        break;
                                    }
                                } else {
                                    array_push($addonsavedata, [
                                        $adp->permission_code => $ad->qty,
                                        'menu_id' =>  $adp->ms_menu_id
                                    ]);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
        $npwp_type = $order->wajibpajak_type;
        $subscription = SubscriptionModel::select('ms_subscription.*', DB::raw("(SELECT regency_name
        FROM ms_regencies WHERE regency_id = ".intval($request->input('wajibpajak_city')).") as regency_name"))
        ->where([
            'subscription_id' => $order->ms_subscription_id,
            'subscription_active' => 1
        ])
        ->with(['subscriptionpermission' => function($q) use ($npwp_type) {
            return $q->where(['subscriptionpermission_type' => $npwp_type]);
        }])->first();
        // dd($subscription->subscriptionpermission);
        if(!$subscription) {
            abort(404);
        }
        $grouppermissions = [];
        $grouppermissions_manajer = [];
        $grouppermissions_karyawan = [];
        $menu_ids = [];
        $decodepermission = [];
        foreach($subscription->subscriptionpermission as $sub) {
            $decodepermission = json_decode($sub->subscriptionpermission_permissions);
            foreach($decodepermission as $pm) {
                array_push($menu_ids, $pm->menu_id);
            }
            
            $addonsavedata = array_merge($addonsavedata, $decodepermission);
        }
        // dd($addonsavedata);
        $newaddonsavedata = [];
        $temp = [];
        
        foreach($addonsavedata as $dp) {
            $dparr = (array) $dp;
            foreach($dparr as $key => $val) {
                if($key == 'menu_id') {
                    if(isset($temp[$val]) && $temp[$val]) {
                        $dparr['duplicate'] = $temp[$val];
                        $temp[$val] = $dparr;
                    } else {
                        $temp[$val] = $dparr;
                    }
                    break;
                }
            }
        }
        // dd($temp);
        // $newtemp = [];
        if($temp) {
            foreach($temp as &$tmp) {
                // var_dump($tmp);
                if(isset($tmp['duplicate']) && $tmp['duplicate']) {
                    $duplicate = $tmp['duplicate'];
                    foreach($duplicate as $dpkey => $dpval) {
                        if($dpkey != 'menu_id') {
                            if(isset($tmp[$dpkey])) {
                                $tmp[$dpkey] = intval($dpval + $tmp[$dpkey]);
                            } else {
                                $tmp[$dpkey] = intval($dpval);
                            }
                        }
                    }
                    unset($tmp['duplicate']);

                }
                array_push($newaddonsavedata, $tmp);
            }
        }

        // dd($newaddonsavedata);
        
        if($menu_ids) {
            $permissions = PermissionModel::select('permission_id', 'permission_code', 'permission_default_group')->whereIn('ms_menu_id', $menu_ids)->get();
            if($permissions) {
                foreach($permissions as $permission) {
                    // $exp_default_group = ($permission->permission_default_group) ? explode(',', $permission->permission_default_group) : null;
                    array_push($grouppermissions, $permission->permission_id);

                    // if($exp_default_group) {
                    //     if(in_array('MANAJER', $exp_default_group)) {
                    //         array_push($grouppermissions_manajer, $permission->permission_id);
                    //     }
                    //     if(in_array('KARYAWAN', $exp_default_group)) {
                    //         array_push($grouppermissions_karyawan, $permission->permission_id);
                    //     }
                    // }
                }
            }
        }
        // dd($grouppermissions);
        // $newaddonsavedata
        // dd($grouppermissions_karyawan);
        // $ordersubs = ($order->ordersubs) ? json_decode($order->ordersubs) : null;
        // dd($ordersubs);
        
            // $subscription_data = json_decode($order->userorder_subscriptiondata);
            
        DB::beginTransaction();
        try {
            if($status == 'PAID') {
                $order->update([
                    'userorder_status' => 'PAID',
                    'userorder_paid_at' => date('Y-m-d H:i:s'),
                    'userorder_paymentmethod' => $payment_method,
                    'userorder_paymentchannel' => $payment_channel,
                    'userorder_xenditcalbackdata' => $request->input()
                ]);
                
                $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " +{$order->userorder_paymentperiode} month"));
                // dd($subscription_expired_at);
                if($order->userorder_kind == 'EXTEND') {
                    // Admin Group
                    $usergroup = UserGroupModel::where([
                        'usergroup_name' => 'Admin',
                        'usergroup_code' => 'REKKAA',
                        'usergroup_active' => 1,
                        'ms_wajibpajak_id' => $order->wajibpajak_id,
                    ])->first();

                    // $usergroupmember = UserGroupMemberModel::create([
                    //     'ms_usergroup_id' => $usergroup->usergroup_id,
                    //     'usergroupmember_members' => $order->user_id,
                    // ]);

                    $userggroupaccess = UserGroupAccessModel::where([
                        'ms_usergroup_id' => $usergroup->usergroup_id,
                    ])->update([
                        'usergroupaccess_permissions' => implode(',',$grouppermissions)
                    ]);

                    WajibPajakSubscriptionModel::where([
                        'ms_wajibpajak_id' => $order->wajibpajak_id,
                        // 'ms_subscription_id' => $subscription->subscription_id,
                    ])->update([
                        'wajibpajaksubscription_active' => 0,
                    ]);
                    if($order->prevwajibpajaksubcription_expired_at) {
                        $subscription_expired_at = date('Y-m-d H:i:s', strtotime($order->prevwajibpajaksubcription_expired_at . " +{$order->userorder_paymentperiode} month"));
                    }
                } else {
                    if($order->userorder_kind == 'UPGRADE') {
                        WajibPajakSubscriptionModel::where([
                            'ms_wajibpajak_id' => $order->wajibpajak_id,
                            // 'ms_subscription_id' => $subscription->subscription_id,
                        ])->update([
                            'wajibpajaksubscription_active' => 0,
                        ]);

                        // Admin Group
                        $usergroup = UserGroupModel::where([
                            'usergroup_name' => 'Admin',
                            'usergroup_code' => 'REKKAA',
                            'usergroup_active' => 1,
                            'ms_wajibpajak_id' => $order->wajibpajak_id,
                        ])->first();

                        // dd($grouppermissions);
                        // $usergroupmember = UserGroupMemberModel::create([
                        //     'ms_usergroup_id' => $usergroup->usergroup_id,
                        //     'usergroupmember_members' => $order->user_id,
                        // ]);

                        $userggroupaccess = UserGroupAccessModel::where([
                            'ms_usergroup_id' => $usergroup->usergroup_id,
                        ])->update([
                            'usergroupaccess_permissions' => implode(',',$grouppermissions)
                        ]);
                        
                    } else {
                    
                        WajibPajakModel::where(['wajibpajak_id' =>$order->ms_wajibpajak_id ])->update([
                            'wajibpajak_active' => 1,
                        ]);
        
                        User::where(['user_id' =>$order->ms_user_id ])->update([
                            'user_active' => '1'
                        ]);

                        // Admin Group
                        $usergroup = UserGroupModel::create([
                            'usergroup_name' => 'Admin',
                            'usergroup_code' => 'REKKAA',
                            'ms_wajibpajak_id' => $order->wajibpajak_id,
                        ]);

                        $usergroupmember = UserGroupMemberModel::create([
                            'ms_usergroup_id' => $usergroup->usergroup_id,
                            'usergroupmember_members' => $order->user_id,
                        ]);

                        $userggroupaccess = UserGroupAccessModel::create([
                            'ms_usergroup_id' => $usergroup->usergroup_id,
                            'usergroupaccess_permissions' => implode(',',$grouppermissions)
                        ]);
                    }
                }
                // dd($subscription_expired_at);
                // Wajib Pajak Subscription
                $wajibpajaksubs = WajibPajakSubscriptionModel::create([
                    'ms_wajibpajak_id' => $order->ms_wajibpajak_id,
                    'ms_user_id' => $order->ms_user_id,
                    'ms_subscription_id' => $subscription->subscription_id,
                    'wajibpajaksubscription_activated_at' => date('Y-m-d H:i:s'),
                    'wajibpajaksubscription_expired_at' => $subscription_expired_at,
                    'wajibpajaksubscription_permission' => ($newaddonsavedata) ? json_encode($newaddonsavedata) : null,
                    'tr_userorder_id' => $order->userorder_id
                ]);

                if($order->userorder_subscriptiontype == 'FREE') {
                    $email_template_view = 'email.email-berlangganan';
                } else {
                    $email_template_view = 'email.email-berlangganan-premium';
                }
                $email_template_title = 'Selamat Datang Di REKKAA!';
                if($order->userorder_kind == 'EXTEND') {
                    $email_template_view = 'email.email-extend-plan';
                    $email_template_title = 'Masa Berlangganan Paket '.$subscription->subscription_title. ' Diperpanjang.';
                } else if($order->userorder_kind == 'UPGRADE') {
                    $email_template_view = 'email.email-upgrade-plan';
                    $email_template_title = 'Selamat Bergabung Di Paket '.$subscription->subscription_title;
                }
                // insert tr_notification welcome
                NotificationModel::insert(
                    [
                        'notification_title' => $email_template_title,
                        'notification_type' => 'EMAIL',
                        'notification_from' => env("MAIL_FROM_ADDRESS"),
                        'notification_to' => $order->user_email,
                        'ms_user_id' => $order->ms_user_id,
                        'ms_wajibpajak_id' => $order->ms_wajibpajak_id,
                        'notification_view' => $email_template_view,
                        'notification_data' => json_encode([
                            'subscription' => [
                                'title' => $subscription->subscription_title,
                                'wajibpajak_name' => $order->wajibpajak_name,
                                'qty' => 1,
                                'price' => $order->userorder_price,
                                'discount' => $order->userorder_discount,
                                'total' => $order->userorder_total,
                                'periode' => $order->userorder_paymentperiode,
                                'activated_at' =>date('Y-m-d H:i:s'),
                                'expired_at' => $subscription_expired_at,
                                'renewed_at' => null,
                            ],
                        ])
                    ]
                );

                // insert tr_notification verification
                $notification = NotificationModel::create([
                    'notification_title' => 'Verifikasi Akun',
                    'notification_type' => 'EMAIL',
                    'notification_text' => 'EMAIL_VERIFICATION',
                    'notification_from' => env("MAIL_FROM_ADDRESS"),
                    'notification_to' => $order->user_email,
                    'ms_user_id' => $order->ms_user_id,
                    'notification_view' => 'email.email-verifikasi',
                ]);
                dispatch(new SendMailJob($notification->notification_id));
                
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Order lunas!'
                ]);
                
            } else {
                // dd($status);
                $order->update([
                    'userorder_status' => $status,
                    // 'userorder_paid_at' => date('Y-m-d H:i:s'),
                    'userorder_paymentmethod' => $request->input('payment_method'),
                    'userorder_paymentchannel' => $request->input('payment_channel'),
                    'userorder_xenditcalbackdata' => $request->input()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Order '.$status.'!'
                ], 404);
            }
        } catch(Error $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
