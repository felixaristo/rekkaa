<?php
namespace App\Http\Controllers\User\Subscription;

use App\Http\Controllers\Controller;
use App\Model\Master\PermissionModel;
use App\Model\Master\SubscriptionModel;
use App\Model\Master\WajibPajakModel;
use App\Model\MasterRelation\MrUserWajibPajakModel;
use App\Model\Transaction\UserOrderModel;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubscriptionUpgradeController extends Controller
{
     /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    function upgrade(Request $request)
    {
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $subscription_id = $request->get('subscription_id');
        $wajibpajak = WajibPajakModel::with(['user', 'country', 'regency', 'wajibpajaksubscription', 'wajibpajaksubscription.subscription'])->where([
            'wajibpajak_id' => $wajibpajak_id
        ])->first();
        
        $subscription_order = $wajibpajak->wajibpajaksubscription->subscription->subscription_order;
        $subscription = SubscriptionModel::where(['subscription_id' => $subscription_id, 'subscription_active' => 1, 'subscription_entity_type' => $wajibpajak->wajibpajak_type])
        ->where('subscription_order', '>', $subscription_order)
        ->first();
        // dd($subscription);
        if(!$subscription) {
            abort(404);
        }
        // dd($wajibpajak->regency);

        $data = [
            'title' => 'Rekkaa - Upgrade Akun',
            'wajibpajak' => $wajibpajak
        ];
        // dd($subscription_id);
        
        $data['permission'] = PermissionModel::where(['permission_visibility' => 'PRICING_PAGE', 'permission_type' => 'PLATFORM', 'permission_active' => 1])
        ->whereIn('permission_entity_type', [$subscription->subscription_entity_type, 'SEMUA'])->get();
        $data['subscription'] = $subscription;
        // dd($data['permission']);
        return view('user.subscription.subscription.upgrade', $data);        
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function doupgrade(Request $request)
    {
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];

        // check if is owner
        $mruserwajibpajak = MrUserWajibPajakModel::where([
            'ms_user_id' => $user_id,
            'ms_wajibpajak_id' => $wajibpajak_id,
            'userwajibpajak_owner' => 1,
        ])->first();
        if(!$mruserwajibpajak->userwajibpajak_owner) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya owner yang bisa melakukan upgrade!'
            ]);
        }

        $subscription_id = $request->get('subscription_id');
        $wajibpajak = WajibPajakModel::with(['user', 'country', 'regency', 'wajibpajaksubscription', 'wajibpajaksubscription.subscription'])->where([
            'wajibpajak_id' => $wajibpajak_id
        ])->first();
        // dd($wajibpajak);
        if(!$wajibpajak) {
            return response()->json([
                'success' => false,
                'message' => 'Entity tidak ditemukan'
            ]);
        }
        
        $subscription_order = $wajibpajak->wajibpajaksubscription->subscription->subscription_order;
        $subscription = SubscriptionModel::with(['subscriptionpermission'])->where(['subscription_id' => $subscription_id, 'subscription_active' => 1, 'subscription_entity_type' => $wajibpajak->wajibpajak_type])
        ->where('subscription_order', '>', $subscription_order)
        ->first();
        if(!$subscription) {
            abort(404);
        }
        // dd($subscription->subscriptionpermission);
        $grouppermissions = [];
        $menu_ids = [];
        foreach($subscription->subscriptionpermission as $sub) {
            $decodepermission = json_decode($sub->subscriptionpermission_permissions);
            foreach($decodepermission as $pm) {
                array_push($menu_ids, $pm->menu_id);
            }
        }
        if($menu_ids) {
            $permissions = PermissionModel::select('permission_id', 'permission_code')->whereIn('ms_menu_id', $menu_ids)->get();
            if($permissions) {
                foreach($permissions as $permission) {
                    array_push($grouppermissions, $permission->permission_id);
                }
            }
        }
        

        DB::beginTransaction();
        try {
            
            // create order
            $subscription_period = 1;
            $order_total = $subscription->subscription_price * $subscription_period;
            $description = 'Rekkaa - Upgrade Subscription '. $subscription->subscription_title. ' (Upgrade)';
            $userorder_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' '.env('XENDIT_EXPIRED_INVOICE').' day'));
           
            $order = UserOrderModel::create([
                'ms_user_id' => $user_id,
                'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
                'ms_subscription_id' => $subscription->subscription_id,
                'userorder_qty' => 1,
                'userorder_price' => $subscription->subscription_price,
                'userorder_discount' => $wajibpajak->userorder->userorder_discount,
                'userorder_total' => $order_total,
                'userorder_subscriptiontype' => $subscription->subscription_type,
                'userorder_paymentperiode' => $subscription_period,
                'userorder_expired_at' => $userorder_expired_at,
                'userorder_description' => $description,
                'userorder_kind' => 'UPGRADE',
                'userorder_xenditurl' => null,
                'userorder_xenditdata' => null,
                'userorder_subscriptiondata' => '',
                'userorder_status' => 'PENDING',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Upgrade diproses.',
                'data' => [
                    // 'user_id' => $user->user_id
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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function doupgradeaddon(Request $request)
    {
        $subscription_id = $request->get('subscription_id');
        $discount = $request->input('discount');
        $period = ($discount) ? $discount['period'] : 1;
        $listaddon = $request->input('listaddon');
        $validation = [
            'subscription_periode' => 'required',
        ];
        $validation_message = [
            'subscription_periode.required' => 'Periode Langganan wajib diisi!',
        ];
        
        $this->validate($request, $validation, $validation_message);
        // dd($validation);
        // check subscription exist
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $wajibpajak = WajibPajakModel::with(['user', 'wajibpajaksubscription'
        , 'wajibpajaksubscription.subscription', 'country', 'regency'])->where([
            'wajibpajak_id' => $wajibpajak_id
        ])->first();
        // dd($wajibpajak);
        if(!$wajibpajak) {
            return response()->json([
                'success' => false,
                'message' => 'Entity tidak ditemukan'
            ]);
        }

        $subscription_period = $request->input('subscription_periode');
        $user = $wajibpajak->user;
        $subscription_order = $wajibpajak->wajibpajaksubscription->subscription->subscription_order;
        $subscription = SubscriptionModel::with(['subscriptionpermission'])->where(['subscription_id' => $subscription_id, 'subscription_active' => 1, 'subscription_entity_type' => $wajibpajak->wajibpajak_type])
        ->where('subscription_order', '>', $subscription_order)
        ->first();
        if(!$subscription) {
            abort(404);
        }

        $order = UserOrderModel::where([
            'ms_user_id' => $user->user_id,
            'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
            'ms_subscription_id' => $subscription->subscription_id,
            'userorder_status' => 'PENDING',
            'userorder_active' => 1,
        ])->orderBy('userorder_id', 'DESC')->first();
        // dd($order);
        if(!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan'
            ]);
        }
        // dd($order);
        // find addon price
        $reallistaddon = [];
        $addon_total = 0;
        if($listaddon) {
            $addon_ids = [];
            foreach($listaddon as $addon) {
                array_push($addon_ids, $addon['id']);
            }
            // dd($addon_ids);
            if($addon_ids) { // get real price add on
                $permissions = PermissionModel::select('permission_id', 'permission_price', 'permission_code_name')
                ->whereIn('permission_id', $addon_ids)
                ->where(['permission_visibility' => 'PRICING_PAGE', 'permission_active' => 1])->get();
                // dd($permissions);
                if($permissions) {
                    foreach($permissions as $pm) {
                        $addon_qty = 1;
                        foreach($listaddon as $addon) {
                            if($addon['id'] == $pm->permission_id) {
                                $addon_qty = $addon['qty'];
                                break;
                            }
                        }

                        array_push($reallistaddon, [
                            'id' => $pm->permission_id,
                            'qty' => $addon_qty,
                            'price' => $pm->permission_price,
                            'text' => $pm->permission_code_name,
                        ]);

                        $addon_total += $pm->permission_price * $addon_qty;
                    }
                }
            }
        }
        // var_dump($addon_total);
        // dd($reallistaddon);

        $subscription_discount = ($subscription->subscription_discount) ? json_decode($subscription->subscription_discount) : null;
        // dd($subscription_discount);
        $discount_total = 0;
        if($subscription_discount) {
            foreach($subscription_discount as $sdiscount) {
                if($sdiscount->period == $period) {
                    if($sdiscount->value_type == 'PERSEN') {
                        $discount_total = $subscription->subscription_price * $sdiscount->value / 100;
                    } else {
                        $discount_total = $sdiscount->value;
                    }
                    break;
                }
            }
        }
        // dd($discount_total);
        $userorder_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . env('XENDIT_EXPIRED_INVOICE').'  day'));
        $order_total = (($subscription->subscription_price - $discount_total) * $subscription_period) + $addon_total;
        
        DB::beginTransaction();
        try {
            $save_data_order = [
                'userorder_qty' => $period,
                'userorder_price' => $subscription->subscription_price,
                'userorder_discount' => $discount_total,
                'userorder_addon' => $addon_total,
                'userorder_total' => $order_total,
                'userorder_subscriptiontype' => $subscription->subscription_type,
                'userorder_paymentperiode' => $period,
                'userorder_expired_at' => $userorder_expired_at,
                'userorder_subscriptiondata' => json_encode([
                    'listaddon' => $reallistaddon
                ])
            ];
            $order->update($save_data_order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tambah Add on berhasil.',
                'data' => [
                    'order' => $save_data_order
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

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    function doupgradeconfirmation(Request $request)
    {
        // check subscription exist
        $subscription_id = $request->get('subscription_id');
        $user_id = session()->get('user_data')['user_id'];
        $wajibpajak_id = session()->get('wajibpajak_current')['wajibpajak_id'];
        $wajibpajak = WajibPajakModel::with(['user', 'wajibpajaksubscription'
        , 'wajibpajaksubscription.subscription', 'country', 'regency'])->where([
            'wajibpajak_id' => $wajibpajak_id
        ])->first();
        // dd($wajibpajak);
        if(!$wajibpajak) {
            return response()->json([
                'success' => false,
                'message' => 'Entity tidak ditemukan'
            ]);
        }
        
        $user = $wajibpajak->user;
        $subscription_order = $wajibpajak->wajibpajaksubscription->subscription->subscription_order;
        $subscription = SubscriptionModel::with(['subscriptionpermission'])->where(['subscription_id' => $subscription_id, 'subscription_active' => 1, 'subscription_entity_type' => $wajibpajak->wajibpajak_type])
        ->where('subscription_order', '>', $subscription_order)
        ->first();
        if(!$subscription) {
            abort(404);
        }

        $order = UserOrderModel::where([
            'ms_user_id' => $user->user_id,
            'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
            'ms_subscription_id' => $subscription->subscription_id,
            'userorder_status' => 'PENDING',
            'userorder_active' => 1,
        ])->orderBy('userorder_id', 'DESC')->first();
        // dd($order);

        // dd($subscription);
        $subscription->subscriptionpermission = ($subscription->spermission_json) ? json_decode($subscription->spermission_json) : [];
        $grouppermissions = [];
        $menu_ids = [];
        // var_dump($subscription->subscriptionpermission);die;
        foreach($subscription->subscriptionpermission as $sub) {
            $decodepermission = json_decode($sub->subscriptionpermission_permissions);
            foreach($decodepermission as $pm) {
                array_push($menu_ids, $pm->menu_id);
            }
        }
        if($menu_ids) {
            $permissions = PermissionModel::select('permission_id', 'permission_code')->whereIn('ms_menu_id', $menu_ids)->get();
            if($permissions) {
                foreach($permissions as $permission) {
                    array_push($grouppermissions, $permission->permission_id);
                }
            }
        }
        // dd(implode(',',$grouppermissions));
        DB::beginTransaction();
        try {
            $periode_pembayaran_label = $order->userorder_paymentperiode.' Bulan Sekali';
            
            $description = 'Rekkaa - Upgrade Subscription '. $subscription->subscription_title. ' for '.$wajibpajak->wajibpajak_name;

            $phone = $wajibpajak->wajibpajak_phone;

            $phone = "+62".substr($phone, -11, -7) . "" . substr($phone, -7, -4) . "" . substr($phone, -4);
            $xendit_url = '';
            $xendit_data = null;
            $status = 'PENDING';
            if($order->userorder_total > 0) {
                $params = [
                    'external_id' => $order->userorder_no,
                    'amount' => $order->userorder_total,
                    'description' => $description,
                    'invoice_duration' => (env('XENDIT_EXPIRED_INVOICE') * 60 * 60 * 24), // 15 hari
                    'name' => $wajibpajak->wajibpajak_name,
                    'surname' => $wajibpajak->wajibpajak_name,
                    'email' => $wajibpajak->user->user_email,
                    'phone' => $wajibpajak->wajibpajak_phone,
                    'city' => 'Jakarta',
                    'country' => 'Indonesia',
                    'postal_code' => '',
                    'address' => $wajibpajak->wajibpajak_address,
                    'items' => [
                        [
                            'name' => $description .' - '.$periode_pembayaran_label,
                            'quantity' => $order->userorder_qty,
                            'price' => $order->userorder_price,
                            'category' => $periode_pembayaran_label,
                            'url' => '#'
                        ]
                    ],
                    'fees' => [
                        [
                            'type' => 'Add on',
                            'value' => $order->userorder_addon,
                        ],
                        [
                            'type' => 'Diskon',
                            'value' => ($order->userorder_discount > 0) ? intval('-'.$order->userorder_discount) * $order->userorder_qty : 0, //$order->userorder_discount
                        ]
                    ]
                ];
                // echo json_encode($params);
                // die;
                $createInvoice = xendit_generate_invoice($params);
                $xendit_url = $createInvoice['invoice_url'];
                $xendit_data = json_encode($createInvoice);
            }

            // update user order
            $order->update([
                'userorder_status' => $status,
                'userorder_xenditurl' => $xendit_url,
                'userorder_xenditdata' => $xendit_data,
            ]);

            // if($order->userorder_total == 0) {
            //     // notif if free
            //     NotificationModel::insert([
            //         [
            //             'notification_title' => 'Selamat Datang Di REKKAA!',
            //             'notification_type' => 'EMAIL',
            //             'notification_from' => env("MAIL_FROM_ADDRESS"),
            //             'notification_to' => session()->get('user_data')['user_email'],
            //             'ms_user_id' => $user_id,
            //             'ms_wajibpajak_id' => $wajibpajak->wajibpajak_id,
            //             'notification_view' => 'email.email-berlangganan-free',
            //             'notification_data' => json_encode([
            //                 'subscription' => [
            //                     'title' => $subscription->subscription_title,
            //                     'qty' => $order->userorder_qty,
            //                     'price' => $order->userorder_price,
            //                     'discount' => $order->userorder_discount,
            //                     'total' => $order->userorder_total,
            //                     'periode' => $order->userorder_paymentperiode,
            //                     'activated_at' => $order->userorder_created_at,
            //                     'expired_at' => $order->userorder_expired_at,
            //                     'renewed_at' => null,
            //                 ],
            //             ])
            //         ]
            //     ]);
            // }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Konfirmasi Registrasi berhasil',
                'data' => [
                    'invoice_url' => $xendit_url
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