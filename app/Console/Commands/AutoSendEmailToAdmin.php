<?php

namespace App\Console\Commands;

use App\Mail\RekkaaMail;
use App\Model\Transaction\NotificationModel;
use Carbon\Carbon;
use DateTime;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class AutoSendEmailToAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekkaa:auto-sendemailtoadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rekkaa - Auto send email notification to the admin';

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
        // $get_stnotifications = getSetting(['NOTIFICATION_BEFORE_EXPIRED']);
        $notificationdata = NotificationModel::with(['user', 'wajibpajak', 'user.userwajibpajak' => function($q) {
            $q->where(['userwajibpajak_owner' => 1]);
        }])->where([
            'notification_type' => 'EMAIL'
        ])
        ->whereRaw("TO_CHAR(notification_created_at, 'YYYY-MM-DD') = '".date('Y-m-d')."'")
        ->whereIn('notification_text', ['NOTIFICATION_EXPIRED', 'NOTIFICATION_BEFORE_EXPIRED'])
        ->get();

        if(count($notificationdata) > 0) {
            $before_expired_data7 = [];
            $before_expired_data14 = [];
            $expired_data = [];
            foreach($notificationdata as $notif) {
                if($notif->notification_text == 'NOTIFICATION_EXPIRED') {
                    array_push($expired_data, $notif);
                }
                if($notif->notification_text == 'NOTIFICATION_BEFORE_EXPIRED') {
                    $subscription_data = ($notif->notification_data) ? json_decode($notif->notification_data) : null;
                    $expired_date = ($subscription_data) ? $subscription_data->subscription->expired_at : null;
                
                    $now = new DateTime(date('Y-m-d'));
                    $then = new DateTime($expired_date);
                    $days  = $now->diff($then)->format('%a');
                    if($days == 14) {
                        array_push($before_expired_data14, $notif);   
                    }
                    if($days == 7) {
                        array_push($before_expired_data7, $notif);   
                    }
                }
            }
            
            // Send before expired data
            if($before_expired_data14) {
                $subscription_data = ($before_expired_data14[0]->notification_data) ? json_decode($before_expired_data14[0]->notification_data) : null;
                $expired_date = ($subscription_data) ? $subscription_data->subscription->expired_at : null;
                $exp_date_format = Carbon::parse($expired_date)->format('d-m-Y');
                $title = 'data-akun-segera-berakhir-14'.date('dmY');
                $htmlString = view('email-attachment-template.attachment-notifikasi-admin', ['notificationdata' => $before_expired_data14])->render();
                    // echo $htmlString;
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
                $spreadsheet = $reader->loadFromString($htmlString);
                
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);
                
                $writer = new Xls($spreadsheet);

                $filename = $title.'.xlsx';
                $dir = 'assets/export/';
                $location = public_path($dir);
                ob_start();
                $writer->save($location.$filename);
                ob_end_clean();

                $options = [
                    'from' => env("MAIL_FROM_ADDRESS"),
                    'subject' => '[Segera Berakhir] Daftar Klien Periode '.$exp_date_format,
                    'data' => [
                        'days_left' => 14
                    ],
                    'view' => 'email.email-notifikasi-before-expired-admin',
                    'attachment' => [
                        'file' => url($dir.$filename),
                        'title' => $filename,
                        'isfile' => true,
                    ]
                ];
                Mail::to(env("MAIL_ADMIN_ADDRESS"))->send(new RekkaaMail($options));
            }
            // Send before expired data
            if($before_expired_data7) {
                $subscription_data = ($before_expired_data7[0]->notification_data) ? json_decode($before_expired_data7[0]->notification_data) : null;
                $expired_date = ($subscription_data) ? $subscription_data->subscription->expired_at : null;
                $exp_date_format = Carbon::parse($expired_date)->format('d-m-Y');
                $title = 'data-akun-segera-berakhir-7'.date('dmY');
                $htmlString = view('email-attachment-template.attachment-notifikasi-admin', ['notificationdata' => $before_expired_data7])->render();
                    // echo $htmlString;
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
                $spreadsheet = $reader->loadFromString($htmlString);
                
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);
                
                $writer = new Xls($spreadsheet);

                $filename = $title.'.xlsx';
                $dir = 'assets/export/';
                $location = public_path($dir);
                ob_start();
                $writer->save($location.$filename);
                ob_end_clean();

                $options = [
                    'from' => env("MAIL_FROM_ADDRESS"),
                    'subject' => '[Segera Berakhir] Daftar Klien Periode '.$exp_date_format,
                    'data' => [
                        'days_left' => 7
                    ],
                    'view' => 'email.email-notifikasi-before-expired-admin',
                    'attachment' => [
                        'file' => url($dir.$filename),
                        'title' => $filename,
                        'isfile' => true,
                    ]
                ];
                Mail::to(env("MAIL_ADMIN_ADDRESS"))->send(new RekkaaMail($options));
            }
            // Send expired data
            if($expired_data) {
                $title = 'data-akun-berakhir-'.date('dmY');
                $htmlString = view('email-attachment-template.attachment-notifikasi-admin', ['notificationdata' => $expired_data])->render();
                    // echo $htmlString;
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
                $spreadsheet = $reader->loadFromString($htmlString);
                
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);
                $spreadsheet->getActiveSheet()->getDefaultColumnDimension()->setAutoSize(true);
                
                $writer = new Xls($spreadsheet);

                $filename = $title.'.xlsx';
                $dir = 'assets/export/';
                $location = public_path($dir);
                ob_start();
                $writer->save($location.$filename);
                ob_end_clean();
                
                $subscription_data = ($expired_data[0]->notification_data) ? json_decode($expired_data[0]->notification_data) : null;
                $expired_date = ($subscription_data) ? $subscription_data->subscription->expired_at : null;
                $options = [
                    'from' => env("MAIL_FROM_ADDRESS"),
                    'subject' => '[Langganan Berakhir] Daftar Klien Periode '.$expired_date,
                    'data' => [],
                    'view' => 'email.email-notifikasi-expired-admin',
                    'attachment' => [
                        'file' => url($dir.$filename),
                        'title' => $filename,
                        'isfile' => true,
                    ]
                ];
                Mail::to(env("MAIL_ADMIN_ADDRESS"))->send(new RekkaaMail($options));
            }
        }
    }
}
