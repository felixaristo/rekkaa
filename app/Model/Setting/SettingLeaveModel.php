<?php

namespace App\Model\Setting;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class SettingLeaveModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'st_leave';
    protected $primaryKey = 'leave_id';
    public $timestamps = false;
    
    protected static $logName = 'leave';
    protected static $logUnguarded = true;
    protected $labelTableName = 'st_leave';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function tapActivity(Activity $activity, string $eventName)
    {
       if(isset(session()->get('user_data')['user_id'])) {
            $defaultCauserType = 'App\Model\MasterRelation\MrUserWajibPajakModel';
            $defaultCauserId = session()->get('user_data')['user_id'];
        } else if(isset(session()->get('karyawan_data')['karyawan_id'])) {
            $defaultCauserType = 'App\Model\Master\KaryawanModel';
            $defaultCauserId = session()->get('karyawan_data')['karyawan_id'];
        } else {
            $defaultCauserType = 'System';
            $defaultCauserId = 0;
        }

        $wajibpajak_id = $this->wajibpajak ? $this->wajibpajak->wajibpajak_id : null;
    
        $activity->ms_wajibpajak_id = $wajibpajak_id;
        $activity->causer_id = $defaultCauserId;
        $activity->causer_type = $defaultCauserType;
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        // $defaultCauserType = isset(session()->get('user_data')['user_id']) ? 'ADMIN' : "KARYAWAN"; // Set the default causer_id here, maybe a default user ID or similar
        $defaultCauserName = isset(session()->get('user_data')['user_email']) ? session()->get('user_data')['user_email'] : session()->get('karyawan_data')['karyawan_email']; // Set the default causer_type here

        if($eventName == 'created') {
            return "$defaultCauserName menambahkan pengaturan cuti baru dengan ID CU";
        } else {
            return "$defaultCauserName melakukan perubahan data pengaturan cuti dengan ID CU";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    public function leaveDetails()
    {
        return $this->hasMany(SettingLeaveDetailModel::class, 'st_leave_id', 'leave_id');
    }

    public function leaveDetailsActive()
    {
        return $this->hasMany(SettingLeaveDetailModel::class, 'st_leave_id', 'leave_id')->where('leavedetail_active', true);
    }

    function wajibpajak() {
        return $this->belongsTo('App\Model\Master\WajibPajakModel', 'ms_wajibpajak_id', 'wajibpajak_id');
    }
}
