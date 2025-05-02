<?php

namespace App\Model\Master;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class UserGroupModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'ms_user_group';
    protected $primaryKey = 'usergroup_id';
    public $timestamps = false;
    
    protected static $logName = 'user_group';
    protected static $logUnguarded = true;
    protected $labelTableName = 'ms_user_group';

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
        // $defaultCauserType = (session()->get('user_data') != null && session()->get('user_data')['user_id']) ? 'ADMIN' : ""; // Set the default causer_id here, maybe a default user ID or similar
        // $defaultCauserName = (session()->get('user_data') != null && session()->get('user_data')['user_name']) ? session()->get('user_data')['user_name'] : ""; // Set the default causer_type here

        if(isset(session()->get('user_data')['user_email'])) {
            $defaultCauserName = session()->get('user_data')['user_email'];
        } else if(isset(session()->get('karyawan_data')['karyawan_email'])) {
            $defaultCauserName = session()->get('karyawan_data')['karyawan_email'];
        } else {
            $defaultCauserName = 'System';
        }
        
        if($eventName == 'created') {
            return "$defaultCauserName menambahkan akses group baru dengan ID UA";
        } else {
            return "$defaultCauserName melakukan perubahan data akses group dengan ID UA";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    function wajibpajak() {
        return $this->hasOne('App\Model\Master\WajibPajakModel', 'wajibpajak_id', 'ms_wajibpajak_id');
    }

    function usergroupaccess() {
        return $this->hasOne('App\Model\Master\UserGroupAccessModel', 'ms_usergroup_id', 'usergroup_id');
    }

    function usergroupmember() {
        return $this->hasOne('App\Model\Master\UserGroupMemberModel', 'ms_usergroup_id', 'usergroup_id');
    }
}
