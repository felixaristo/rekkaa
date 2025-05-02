<?php

namespace App\Console\Commands;

use App\Mail\RekkaaMail;
use App\Model\Transaction\NotificationModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AutoSendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekkaa:auto-sendemail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rekkaa - Auto send email notification';

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
        //
        $max_data = getSetting('MAX_NOTIFICATION_DATA');
        // dd($max_data);
        $decode_maxdata = (isset($max_data->setting_value) && $max_data->setting_value) ? json_decode($max_data->setting_value) : 10;
        // $decode_value = json_decode($stnotif->setting_value);
        $limit = $decode_maxdata->value;
        $notification = NotificationModel::with(['user','wajibpajak','user.userwajibpajak' => function($q) {
            $q->where(['userwajibpajak_owner' => 1]);
        }])->where(['notification_status' => 'PENDING', 'notification_type' => 'EMAIL'])
        ->limit($limit)
        ->get();

        //echo "oioi";
        // echo (count($notification) > 0);die;
        if (count($notification) > 0) {
            foreach($notification as $notif) {
                // dd($notif->user->userwajibpajak);
                if($notif->user && $notif->user->userwajibpajak) {
                    $notif->user->user_name = $notif->user->userwajibpajak->userwajibpajak_name;
                } else {
                    $notif->user->user_name = '-';
                }
                $options = [
                    'from' => env("MAIL_FROM_ADDRESS"),
                    'subject' => $notif->notification_title,
                    'data' => [
                        'user' => $notif->user,
                        'wajibpajak' => $notif->wajibpajak,
                    ],
                    'view' => $notif->notification_view // 'email.email-lupa-password'
                ];
                if($notif->notification_data) {
                    $decode_data = json_decode($notif->notification_data);
                    foreach($decode_data as $key => $val) {
                        $options['data'][$key] = $val;
                    }
                }
                if($notif->notification_attachment) {
                    $decode_attachment = json_decode($notif->notification_attachment);
                    // dd($decode_attachment);
                    $fn = $decode_attachment->fnlocation;
                    $method = $decode_attachment->fnmethod;
                    $params = $decode_attachment->params;
                    $title = $decode_attachment->title;
                    $options['attachment'] = [
                        'file' => app($fn)->$method($params),
                        'title' => $title,
                    ];
                }
                // dd($options);
                Mail::to($notif->notification_to)->send(new RekkaaMail($options));

                // Update status notification
                $notif->notification_status = 'SEND';
                $notif->notification_updated_at = date('Y-m-d H:i:s');
                $notif->save();
            }
        }
    }
}
