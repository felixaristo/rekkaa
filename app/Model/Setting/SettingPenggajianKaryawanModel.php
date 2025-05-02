<?php

namespace App\Model\Setting;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class SettingPenggajianKaryawanModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'st_penggajian_karyawan';
    protected $primaryKey = 'stpenggajiankaryawan_id';
    public $timestamps = false;

    protected static $logName = 'penggajian_karyawan';
    protected static $logUnguarded = true;
    protected $labelTableName = 'st_penggajian_karyawan';

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
            return "$defaultCauserName menambahkan pengaturan penggajian baru dengan ID PE";
        } else {
            return "$defaultCauserName melakukan perubahan data pengaturan penggajian dengan ID PE";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    function karyawan() {
        return $this->hasMany('App\Model\Master\KaryawanModel', 'st_penggajian_id', 'stpenggajiankaryawan_id');
    }

    function vwkaryawanpayroll() {
        return $this->hasMany('App\Model\Mview\VwKaryawanPayrollModel', 'st_penggajian_id', 'stpenggajiankaryawan_id');
    }

    function vwkaryawanpayrolltetap() {
        return $this->hasMany('App\Model\Mview\VwKaryawanPayrollModel', 'st_penggajian_id', 'stpenggajiankaryawan_id')->where(['karyawan_status' => 'TETAP']);
    }

    function vwkaryawanpayrollnontetap() {
        return $this->hasMany('App\Model\Mview\VwKaryawanPayrollModel', 'st_penggajian_id', 'stpenggajiankaryawan_id')->whereIn('karyawan_status', ['KONTRAK', 'PERCOBAAN']);
    }

    function wajibpajak() {
        return $this->belongsTo('App\Model\Master\WajibPajakModel', 'ms_wajibpajak_id', 'wajibpajak_id');
    }
}
