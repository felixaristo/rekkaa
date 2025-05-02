<?php

namespace App\Jobs;

use App\Mail\RekkaaMail;
use App\Model\Transaction\NotificationModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notification_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($notification_id)
    {
        //
        $this->notification_id = $notification_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $notification = NotificationModel::with(['user','wajibpajak','user.userwajibpajak'])->where(['notification_status' => 'PENDING', 'notification_type' => 'EMAIL', 'notification_id' => $this->notification_id])
        ->first();

        if($notification) {
            $notification->user->user_name = $notification->user->userwajibpajak->userwajibpajak_name;
            
            $options = [
                'from' => env("MAIL_FROM_ADDRESS"),
                'subject' => $notification->notification_title,
                'data' => [
                    'user' => $notification->user,
                    'wajibpajak' => $notification->user->userwajibpajak->wajibpajak,
                ],
                'view' => $notification->notification_view // 'email.email-lupa-password'
            ];
            if($notification->notification_data) {
                $decode_data = json_decode($notification->notification_data);
                foreach($decode_data as $key => $val) {
                    $options['data'][$key] = $val;
                }
            }
            if($notification->notification_attachment) {
                $decode_attachment = json_decode($notification->notification_attachment);
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
            Mail::to($notification->notification_to)->send(new RekkaaMail($options));

            // Update status notification
            $notification->notification_status = 'SEND';
            $notification->notification_updated_at = date('Y-m-d H:i:s');
            $notification->save();
        }
    }
}
