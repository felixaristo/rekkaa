<?php

namespace App\Console\Commands;

use App\Mail\RekkaaMail;
use App\Model\Master\KaryawanModel;
use App\Model\Setting\SettingLeaveDetailModel;
use App\Model\Setting\SettingLeaveModel;
use App\Model\Transaction\NotificationModel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AutoUpdateRepeatLeave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekkaa:auto-updateleave';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rekkaa - Auto update repeat leave';

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
        $time = Carbon::now()->format('Y-m-d');
        $getLeaveByEnd = SettingLeaveModel::where('leave_active_end_date', $time)->get();

        Log::info('getLeave: ' . $getLeaveByEnd);

        foreach ($getLeaveByEnd as $leave) {
            $isContinue = true;
            // Check if leave_grace_period is null or not
            if ($leave->leave_grace_period === null) {
                $isContinue = false;
                // Update data
                $leave->update([
                    'leave_status_active' => false,
                    'leave_is_hide' => true,
                ]);

                // Create new data
                $createLeave = SettingLeaveModel::create([
                    'leave_description' => $leave->leave_description,
                    'leave_active_start_date' => Carbon::parse($leave->leave_active_start_date)->addYear()->format('Y-m-d'),
                    'leave_grace_period' => null,
                    'leave_active_end_date' => Carbon::parse($leave->leave_active_end_date)->addYear()->format('Y-m-d'),
                    'ms_wajibpajak_id' => $leave->ms_wajibpajak_id,
                    'leave_type' => $leave->leave_type,
                    'leave_quota' => $leave->leave_quota,
                    'leave_repeat_status' => $leave->leave_repeat_status,
                    'leave_base_month' => $leave->leave_base_month,
                    'leave_probation_status' => $leave->leave_probation_status,
                    'leave_status_active' => true,
                    'leave_is_hide' => false,
                    'leave_is_all' => $leave->leave_is_all,
                ]);
            } else {
                $isContinue = false;
                // Create new data with updated leave_grace_period
                $createLeave = SettingLeaveModel::create([
                    'leave_description' => $leave->leave_description,
                    'leave_active_start_date' => Carbon::parse($leave->leave_active_start_date)->addYear()->format('Y-m-d'),
                    'leave_grace_period' => Carbon::parse($leave->leave_grace_period)->addYear()->format('Y-m-d'),
                    'leave_active_end_date' => Carbon::parse($leave->leave_active_end_date)->addYear()->format('Y-m-d'),
                    'ms_wajibpajak_id' => $leave->ms_wajibpajak_id,
                    'leave_type' => $leave->leave_type,
                    'leave_quota' => $leave->leave_quota,
                    'leave_repeat_status' => $leave->leave_repeat_status,
                    'leave_base_month' => $leave->leave_base_month,
                    'leave_probation_status' => $leave->leave_probation_status,
                    'leave_status_active' => true,
                    'leave_is_hide' => false,
                    'leave_is_all' => $leave->leave_is_all,
                ]);
            }

            if($isContinue) {
                continue;
            } else {
                $getDetailSetting = SettingLeaveDetailModel::where('st_leave_id', $leave->leave_id)
                    ->where('leavedetail_active', true)
                    ->get();

                $leaveDetails = [];
                
                foreach($getDetailSetting as $detailSetting) {
                    $leaveDetails[] = [
                        'st_leave_id' => $detailSetting->st_leave_id,
                        'ms_karyawan_id' => $detailSetting->ms_karyawan_id,
                        'leavedetail_active' => true
                    ];
                }

                if(!empty($leaveDetails)) {
                    SettingLeaveDetailModel::insert($leaveDetails);
                }
            }
        }

        $getLeaveByGrace = SettingLeaveModel::where('leave_grace_period', $time)->get();

        Log::info('getByGrace: ' . $getLeaveByGrace);

        foreach ($getLeaveByGrace as $leave) {
            $isContinue = true;
            // Check if leave_grace_period is null or not
            
            if($leave->leave_repeat_status === true) {
                $leave->update([
                    'leave_status_active' => false,
                    'leave_is_hide' => true,
                ]);
            } else {
                $leave->update([
                    'leave_status_active' => false,
                    'leave_is_hide' => false,
                ]);
            }
        }
    }
}
