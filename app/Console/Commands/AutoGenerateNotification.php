<?php

namespace App\Console\Commands;

use App\Model\Transaction\NotificationModel;
use App\Model\Transaction\WajibPajakSubscriptionModel;
use DateTime;
use Illuminate\Console\Command;

class AutoGenerateNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekkaa:auto-generatenotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rekkaa - Auto generate notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $get_stnotifications = getSetting(['NOTIFICATION_BEFORE_EXPIRED', 'NOTIFICATION_EXPIRED', 'MAX_NOTIFICATION_DATA']);
        $max_data = 100;
        $arr_estimates = [];
        // $estimate_key = [];
        foreach($get_stnotifications as $stnotif) {
            if($stnotif->setting_key == 'MAX_NOTIFICATION_DATA') {
                $decode_value = json_decode($stnotif->setting_value);
                $max_data = $decode_value->value;
            } else {
                array_push($arr_estimates, [
                    'key' => $stnotif->setting_key,
                    'value' => json_decode($stnotif->setting_value),
                ]);
                // array_push($estimate_key, $stnotif->setting_key);
            }
        }
        
        // dd($arr_estimates);
        // $estimate_key_string = sprintf("'%s'", implode("','", $estimate_key ) );

        $wpsubscription = WajibPajakSubscriptionModel::select('tr_wajib_pajak_subscription.*', 'ms_user.*', 'ms_wajib_pajak.wajibpajak_name', 'ms_subscription.subscription_type', 'tr_notification.notification_id')
        ->join("ms_user", 'ms_user.user_id', 'tr_wajib_pajak_subscription.ms_user_id')
        ->join("ms_subscription", 'ms_subscription.subscription_id', 'tr_wajib_pajak_subscription.ms_subscription_id')
        ->join("ms_wajib_pajak", 'ms_wajib_pajak.wajibpajak_id', 'tr_wajib_pajak_subscription.ms_wajibpajak_id')
        ->leftJoin("tr_notification", function($join) use ($arr_estimates)
        { // BUG
            $join->on('tr_notification.ms_user_id', '=', 'tr_wajib_pajak_subscription.ms_user_id');
            $join->on('tr_notification.ms_wajibpajak_id', '=', 'tr_wajib_pajak_subscription.ms_wajibpajak_id');
            $join->whereRaw("(TO_CHAR(notification_created_at, 'YYYY-MM-DD') >= TO_CHAR(NOW(), 'YYYY-MM-DD'))");
            $join->whereRaw("(notification_text = 'NOTIFICATION_BEFORE_EXPIRED' OR notification_text = 'NOTIFICATION_EXPIRED')");

            // $estimate_key = [];
            // foreach($arr_estimates as $estimate) {
            //     array_push($estimate_key, "'".$estimate['key']."'");
            // }
            // if($estimate_key)
            //     $join->on('notification_text', '=', DB::raw('ANY(array['.implode(',', $estimate_key).'])'));
        })
        ->whereIn('wajibpajaksubscription_active', [1,2])
        ->whereNull('notification_id')
        ->whereNotIn('ms_subscription.subscription_type', ['FREE'])
        ->where(function ($q) use ($arr_estimates){
            $i = 0;
            foreach($arr_estimates as $estimate) {
                $val = $estimate['value']->value;
                if($i == 0) {
                    // $q->where(DB::raw("(wajibpajaksubscription_expired_at::date - NOW()::date)"), "=", $val);
                    if($estimate['key'] == 'NOTIFICATION_EXPIRED') {
                        // dd($val);
                        $q->whereRaw("(TO_CHAR(wajibpajaksubscription_expired_at, 'YYYY-MM-DD')::date - (NOW()::date + 1)) = $val");
                    } else {
                        $q->whereRaw("(TO_CHAR(wajibpajaksubscription_expired_at, 'YYYY-MM-DD')::date - NOW()::date) = $val");
                    }
                } else {
                    // $q->orWhere(DB::raw("(wajibpajaksubscription_expired_at::date - NOW()::date)", "=", $val));
                    if($estimate['key'] == 'NOTIFICATION_EXPIRED') {
                        $q->orWhereRaw("(TO_CHAR(wajibpajaksubscription_expired_at, 'YYYY-MM-DD')::date - (NOW()::date + 1)) = $val");
                    } else {
                        $q->orWhereRaw("(TO_CHAR(wajibpajaksubscription_expired_at, 'YYYY-MM-DD')::date - NOW()::date) = $val");
                    }
                }
                $i++;
            }
        })
        ->limit($max_data)
        ->get();

        // dd($wpsubscription);
        // dd(count($wpsubscription));
        // die;

        $notificationdata = [];
        if(count($wpsubscription) > 0) {
            $wpsubscription_id_exists = [];
            foreach($wpsubscription as $wpsub) {
                $qty = $wpsub->userorder->userorder_qty;
                $price = $wpsub->userorder->userorder_price;
                $discount = 0;
                $total = $qty * $price;
                $periode = $wpsub->userorder->userorder_paymentperiode;
                $activated_at = $wpsub->wajibpajaksubscription_activated_at;
                $expired_at = $wpsub->wajibpajaksubscription_expired_at;
                $activated_at = $wpsub->wajibpajaksubscription_activated_at;
                $renewed_at = null;
                $wajibpajak_id = $wpsub->ms_wajibpajak_id;
                $wajibpajak_name = $wpsub->wajibpajak->wajibpajak_name;
                $user_id = $wpsub->ms_user_id;
                $user_email = $wpsub->user->user_email;
                // dd($wpsub);
                // $i = 0;
                // dd($arr_estimates);
                foreach($arr_estimates as $estimate) {
                    $key = $estimate['key'];
                    $val = $estimate['value']->value;
                    $email_title = $estimate['value']->title;
                    $mail_template = $estimate['value']->template;
                    $now = new DateTime(date('Y-m-d'));
                    $then = new DateTime($wpsub->wajibpajaksubscription_expired_at);
                    $days  = $now->diff($then)->format('%a');
                    // dd($days, $val);
                    if($days == 1) {
                        $days = 0;
                    }
                    if($days == $val && !in_array($wpsub->wajibpajaksubscription_id, $wpsubscription_id_exists)) {
                    // if($days == $val) {
                        $subscription = json_decode($wpsub->userorder->subscription);
                        // dd($subscription);
                        $notificationdata[] = 
                        [
                            'notification_title' => $email_title,
                            'notification_text' => $key,
                            'notification_type' => 'EMAIL',
                            'notification_from' => env("MAIL_FROM_ADDRESS"),
                            'notification_to' => $user_email,
                            'ms_user_id' => $user_id,
                            'ms_wajibpajak_id' => $wajibpajak_id,
                            'notification_view' => $mail_template,
                            'notification_data' => json_encode([
                                'subscription' => [
                                    'title' => $subscription->subscription_title,
                                    'wajibpajak_name' => $wajibpajak_name,
                                    'qty' => $qty,
                                    'price' => $price,
                                    'discount' => $discount,
                                    'total' => $total,
                                    'periode' => $periode,
                                    'activated_at' => $activated_at,
                                    'expired_at' => $expired_at,
                                    'renewed_at' => $renewed_at,
                                ],
                            ])
                        ];

                        array_push($wpsubscription_id_exists, $wpsub->wajibpajaksubscription_id);
                        break;
                    }
                }
            }
            // dd($notificationdata);
            // insert tr_notification
            if(count($notificationdata) > 0)
                NotificationModel::insert($notificationdata);
        }
    }
}
