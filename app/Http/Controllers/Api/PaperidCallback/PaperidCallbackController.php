<?php

namespace App\Http\Controllers\Api\PaperidCallback;

use App\Http\Controllers\Controller;
use App\Model\Master\SubscriptionPermissionModel;
use App\Model\Master\WajibPajakModel;
use App\Model\Transaction\NotificationModel;
use App\Model\Transaction\UserOrderModel;
use DateTime;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaperidCallbackController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function callback(Request $request)
    {
        // $xendiclbktoken = $request->header('X-CALLBACK-TOKEN');
        // dd($xendiclbktoken);
        // if($xendiclbktoken !== env('XENDIT_CALLBACK_TOKEN')) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Forbidden!'
        //     ], 400);
        // }
        $invoice = $request->input('invoice');
        if(!$invoice && !is_array($invoice)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorect Format!'
            ], 400);
        }
        $external_id = $invoice['number'];
        $status = $invoice['status'];
        $paid_amount = $invoice['amount'];
        $order = UserOrderModel::select('tr_user_order.*', 'user_email', 'ms_wajib_pajak.wajibpajak_name', DB::raw(" 
        (SELECT row_to_json(ptable) 
        FROM (
            SELECT a.userorder_expired_at, a.userorder_id, b.usersubscription_expired_at, c.user_email
            FROM tr_user_order as a
            JOIN ms_user as c ON c.user_id = a.ms_user_id
            WHERE a.ms_wajibpajak_id = tr_user_order.ms_wajibpajak_id AND a.userorder_status = 'PAID' 
            ORDER BY a.userorder_expired_at DESC LIMIT 1
        )
        as ptable) as ordersubs"))->where([
            'userorder_no' => $external_id,
            'userorder_status' => 'PENDING'
        ])
        ->join('ms_user', 'tr_user_order.ms_user_id', '=', 'ms_user.user_id')
        ->join('ms_wajib_pajak', 'tr_user_order.ms_wajibpajak_id', '=', 'ms_wajib_pajak.wajibpajak_id')
        ->orderBy('userorder_created_at', 'DESC')->first();
        if(!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order tidak ditemukan!'
            ], 404);
        }

        // dd($order->userorder_total.' '.$paid_amount. ' '. $status);
        // dd($order->user_email);
        $ordersubs = ($order->ordersubs) ? json_decode($order->ordersubs) : null;
        
        if(strtoupper($status) == 'PAID' && $order->userorder_total == $paid_amount) {
            $subscription = json_decode($order->userorder_subscriptiondata);
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Order tidak ditemukan!',
            //     'data' => $subscription
            // ], 404);
            DB::beginTransaction();
            try {
                $order->update([
                    'userorder_status' => 'PAID'
                ]);
                $subscription_expired_at = null;
                if($order->userorder_paymentperiode == 'MONTHLY') {
                    $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +1 month'));
                } else if($order->userorder_paymentperiode == 'QUARTELY') {
                    $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +4 month'));
                } else if($order->userorder_paymentperiode == 'SEMI_ANNUAL') {
                    $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +6 month'));
                } else if($order->userorder_paymentperiode == 'YEARLY') {
                    $subscription_expired_at = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +12 month'));
                }

                // if(!$ordersubs || $order->userorder_kind == 'UPGRADE') { // create
                    // // if($order->userorder_kind == 'UPGRADE') {
                    //     // UserSubscriptionModel::where([
                    //     //     'ms_user_id' => $order->ms_user_id,
                    //     //     'ms_wajibpajak_id' => $order->ms_wajibpajak_id,
                    //     //     'usersubscription_active' => 1,
                    //     // ])->update(['usersubscription_active' => 0]);
                    // // };
                    // $usersubscription = UserSubscriptionModel::create([
                    //     'ms_user_id' => $order->ms_user_id,
                    //     'ms_wajibpajak_id' => $order->ms_wajibpajak_id,
                    //     'ms_userorder_id' => $order->userorder_id,
                    //     'ms_subscription_id' => $subscription->subscription_id,
                    //     'usersubscription_qty' => 1,
                    //     'usersubscription_price' => $order->userorder_price,
                    //     'usersubscription_discount' => $order->userorder_discount,
                    //     'usersubscription_total' => $order->userorder_total,
                    //     'usersubscription_subscriptiontype' => $order->userorder_subscriptiontype,
                    //     'usersubscription_paymentperiode' => $order->userorder_paymentperiode,
                    //     'usersubscription_activated_at' => date('Y-m-d H:i:s'),
                    //     'usersubscription_expired_at' => $subscription_expired_at,
                    //     'usersubscription_renewed_at' => null,
                    //     'usersubscription_subscriptiondata' => json_encode($subscription),
                    // ]);

                    // $subscriptionpermission_dt = SubscriptionPermissionModel::where([
                    //     'ms_subscription_id' => $subscription->subscription_id,
                    //     'subscriptionpermission_active' => 1,
                    // ])->get();
                    // DB::rollBack();
                    // dd($subscriptionpermission_dt);
                    // Subscription Permission
                    // $subscriptionpermission = [];
                    // foreach($subscriptionpermission_dt as $sp) {
                    //     array_push($subscriptionpermission, [
                    //         'tr_usersubscription_id' => $usersubscription->usersubscription_id,
                    //         'ms_permission_code' => $sp->ms_permission_code,
                    //         'usersubscriptionpermission_value' => $sp->subscriptionpermission_value,
                    //     ]);
                    // }
                    // // dd($subscriptionpermission);
                    // UserSubscriptionPermissionModel::insert($subscriptionpermission);

                    $email_template_view = 'email.email-berlangganan-premium';
                    $email_template_title = 'Selamat Datang Di REKKAA!';
                    if($order->userorder_kind == 'UPGRADE') {
                        $email_template_view = 'email.email-upgrade-plan';
                        $email_template_title = 'Selamat Bergabung Di Paket '.$subscription->subscription_title;
                    }
                    // insert tr_notification
                    NotificationModel::insert(
                        [
                            'notification_title' => $email_template_title,
                            'notification_type' => 'EMAIL',
                            'notification_from' => env("MAIL_FROM_ADDRESS"),
                            'notification_to' => $order->user_email,
                            'ms_user_id' => $order->ms_user_id,
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

                // } else { // update existing
                //     $dt_now = date('Y-m-d H:i:s');
                //     $now = new DateTime($dt_now);
                //     $then = new DateTime($ordersubs->usersubscription_expired_at);
                //     $days  = $then->diff($now)->format('%a');
                //     $subscription_expired_at_new = $subscription_expired_at;
                //     // echo $subscription_expired_at_new;
                //     if($days > 0) {
                //         $subscription_expired_at_new = date('Y-m-d H:i:s', strtotime($subscription_expired_at . "+{$days} day"));
                //     }
                //     // echo '-dyas'.$days;
                //     // echo '-'.$subscription_expired_at_new;
                //     // DB::rollBack();
                //     // exit;
                //     UserSubscriptionModel::where([
                //         'ms_user_id' => $order->ms_user_id,
                //         'ms_wajibpajak_id' => $order->ms_wajibpajak_id,
                //     ])
                //     ->update([
                //         'ms_userorder_id' => $order->userorder_id,
                //         'ms_subscription_id' => $subscription->subscription_id,
                //         'usersubscription_qty' => 1,
                //         'usersubscription_price' => $order->userorder_price,
                //         'usersubscription_discount' => $order->userorder_discount,
                //         'usersubscription_total' => $order->userorder_total,
                //         'usersubscription_subscriptiontype' => $subscription->subscription_type,
                //         'usersubscription_paymentperiode' => $subscription->subscription_paymentperiode,
                //         'usersubscription_expired_at' => $subscription_expired_at_new,
                //         'usersubscription_renewed_at' => date('Y-m-d H:i:s'),
                //         'usersubscription_subscriptiondata' => json_encode($subscription),
                //     ]);
                //     UserOrderModel::where([
                //         'ms_user_id' => $order->ms_user_id,
                //         'ms_wajibpajak_id' => $order->ms_wajibpajak_id,
                //         'userorder_id' => $ordersubs->userorder_id
                //     ])->update([
                //         'userorder_active' => 0
                //     ]);

                //     // insert tr_notification
                //     NotificationModel::insert(
                //         [
                //             'notification_title' => 'Masa Berlangganan Diperpanjang',
                //             'notification_type' => 'EMAIL',
                //             'notification_from' => env("MAIL_FROM_ADDRESS"),
                //             'notification_to' => $order->user_email,
                //             'ms_user_id' => $order->ms_user_id,
                //             'notification_view' => 'email.email-extend-plan',
                //             'notification_data' => json_encode([
                //                 'subscription' => [
                //                     'title' => $subscription->subscription_title,
                //                     'wajibpajak_name' => $order->wajibpajak_name,
                //                     'qty' => 1,
                //                     'price' => $order->userorder_price,
                //                     'discount' => $order->userorder_discount,
                //                     'total' => $order->userorder_total,
                //                     'periode' => $subscription->subscription_paymentperiode,
                //                     'activated_at' =>date('Y-m-d H:i:s'),
                //                     'expired_at' => $subscription_expired_at,
                //                     'renewed_at' => null,
                //                 ],
                //             ])
                //         ]
                //     );
                // }
                
                WajibPajakModel::where(['wajibpajak_id' =>$order->ms_wajibpajak_id ])->update([
                    'wajibpajak_active' => 1,
                ]);
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'Order lunas!'
                ]);
            } catch(Error $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Order belum lunas!'
            ], 404);
        }
    }
}
