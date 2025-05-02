<?php

namespace App\Model\Setting;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class SettingPotonganKaryawanDetailModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'st_potongan_karyawan_detail';
    protected $primaryKey = 'stpotongankaryawandet_id';
    public $timestamps = false;

    protected static $logName = 'potongan_karyawan_detail';
    protected static $logUnguarded = true;
    protected $labelTableName = 'st_potongan_karyawan_detail';

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

        // $wajibpajak_id = $this->wajibpajak ? $this->wajibpajak->wajibpajak_id : null;
    
        // $activity->ms_wajibpajak_id = $wajibpajak_id;
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
            return "$defaultCauserName menambahkan pengaturan detail potongan baru dengan ID ";
        } else {
            return "$defaultCauserName melakukan perubahan data pengaturan detail potongan dengan ID ";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    function stpotongan() {
        return $this->belongsTo('App\Model\Setting\SettingPotonganKaryawanModel', 'st_potongankaryawan_id', 'stpotongankaryawan_id');
    }

    function karyawan() {
        return $this->hasOne('App\Model\Master\KaryawanModel', 'karyawan_id', 'ms_karyawan_id')->where(['karyawan_active' => '1']);
    }
}
