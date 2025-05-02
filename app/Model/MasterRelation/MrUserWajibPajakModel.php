<?php

namespace App\Model\MasterRelation;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class MrUserWajibPajakModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'mr_user_wajib_pajak';
    protected $primaryKey = 'userwajibpajak_id';
    public $timestamps = false;

    protected static $logName = 'user_wajib_pajak';
    protected static $logUnguarded = true;
    protected $labelTableName = 'mr_user_wajib_pajak';

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
        // $defaultCauserName = isset(session()->get('user_data')['user_email']) ? session()->get('user_data')['user_email'] : session()->get('karyawan_data')['karyawan_email']; // Set the default causer_type here

        if(isset(session()->get('user_data')['user_email'])) {
            $defaultCauserName = session()->get('user_data')['user_email'];
        } else if(isset(session()->get('karyawan_data')['karyawan_email'])) {
            $defaultCauserName = session()->get('karyawan_data')['karyawan_email'];
        } else {
            $defaultCauserName = 'System';
        }

        if($eventName == 'created') {
            return "$defaultCauserName menambahkan user wajib pajak baru dengan ID ";
        } else {
            return "$defaultCauserName melakukan perubahan data user wajib pajak dengan ID ";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    function wajibpajak() {
        return $this->hasOne('App\Model\Master\WajibPajakModel', 'wajibpajak_id', 'ms_wajibpajak_id')->where(['wajibpajak_active' => 1])->orderBy('wajibpajak_id', 'DESC');
    }

    function user() {
        return $this->hasOne('App\User', 'user_id', 'ms_user_id');
    }
}
