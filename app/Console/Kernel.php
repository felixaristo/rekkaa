<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        'App\Console\Commands\AutoGenerateNotification',
        'App\Console\Commands\AutoSendEmail',
        'App\Console\Commands\AutoSendEmailToAdmin',
        'App\Console\Commands\AutoDowngradePlan',
        'App\Console\Commands\AutoUpdateRepeatLeave',
        'App\Console\Commands\AutoCalculatePayroll',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        
        if(env('APP_ENV') == 'production') { // production only
            // calculate payroll 
            // $schedule->command('rekkaa:auto-calculatepayroll --periodtype=KALENDER')->monthlyOn(1)->timezone('Asia/Jakarta');
            // $schedule->command('rekkaa:auto-calculatepayroll --periodtype=TANGGAL')->dailyAt('01:00')->timezone('Asia/Jakarta');

            // generate data to tr_notification
            $schedule->command('rekkaa:auto-generatenotification')->dailyAt('04:01')->timezone('Asia/Jakarta');

            // downgrade plan account when expired
            $schedule->command('rekkaa:auto-downgradeplan')->dailyAt('03:00')->timezone('Asia/Jakarta');
            
            // send notification email
            $schedule->command('rekkaa:auto-sendemail')->everyMinute();
            
            // send notification email to the admin
            $schedule->command('rekkaa:auto-sendemailtoadmin')->dailyAt('06:00')->timezone('Asia/Jakarta');

            // update repeat leave
            $schedule->command('rekkaa:auto-updateleave')->dailyAt('05:00')->timezone('Asia/Jakarta');

        } else { // local or development or staging
            // generate data to tr_notification
            $schedule->command('rekkaa:auto-generatenotification')->everyMinute();

            // send notification email
            $schedule->command('rekkaa:auto-sendemail')->everyFiveMinutes();

            // send notification email to the admin
            $schedule->command('rekkaa:auto-sendemailtoadmin')->dailyAt('13:15')->timezone('Asia/Jakarta');

            // downgrade plan account when expired
            $schedule->command('rekkaa:auto-downgradeplan')->daily();

            // update repeat leave
            $schedule->command('rekkaa:auto-updateleave')->dailyAt('03:00')->timezone('Asia/Jakarta');

            // calculate payroll 
            // $schedule->command('rekkaa:auto-calculatepayroll --periodtype=TANGGAL')->dailyAt('01:00')->timezone('Asia/Jakarta');
            // // $schedule->command('rekkaa:auto-calculatepayroll --period=KALENDER')->everyFiveMinutes()->timezone('Asia/Jakarta');
            // $schedule->command('rekkaa:auto-calculatepayroll --periodtype=KALENDER')->monthlyOn(1)->timezone('Asia/Jakarta');
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
