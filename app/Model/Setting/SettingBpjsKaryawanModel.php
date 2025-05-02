<?php

namespace App\Model\Setting;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class SettingBpjsKaryawanModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'st_bpjs_karyawan';
    protected $primaryKey = 'stbpjskaryawan_id';
    public $timestamps = false;

    protected static $logName = 'bpjs_karyawan';
    protected static $logUnguarded = true;
    protected $labelTableName = 'st_bpjs_karyawan';

    /**
     * The attributes that are not mass assignable.
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
            return "$defaultCauserName menambahkan pengaturan BPJS baru";
        } else {
            return "$defaultCauserName melakukan perubahan data pengaturan BPJS";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    function wajibpajak() {
        return $this->belongsTo('App\Model\Master\WajibPajakModel', 'ms_wajibpajak_id', 'wajibpajak_id');
    }
}
