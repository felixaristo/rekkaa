<?php

namespace App\Model\Setting;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class SettingTunjanganKaryawanModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'st_tunjangan_karyawan';
    protected $primaryKey = 'sttunjangankaryawan_id';
    public $timestamps = false;

    protected static $logName = 'tunjangan_karyawan';
    protected static $logUnguarded = true;
    protected $labelTableName = 'st_tunjangan_karyawan';

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
            return "$defaultCauserName menambahkan pengaturan tunjangan baru dengan ID TU";
        } else {
            return "$defaultCauserName melakukan perubahan data pengaturan tunjangan dengan ID TU";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    function stgrouptunjangan() {
        return $this->belongsTo('App\Model\Setting\SettingGroupTunjanganKaryawanModel', 'st_grouptunjangankaryawan_id', 'stgrouptunjangankaryawan_id');
    }

    function sttunjangandetail() {
        return $this->hasMany('App\Model\Setting\SettingTunjanganKaryawanDetailModel', 'st_tunjangankaryawan_id', 'sttunjangankaryawan_id');
    }

    function sttunjangandetailaktif() {
        return $this->hasMany('App\Model\Setting\SettingTunjanganKaryawanDetailModel', 'st_tunjangankaryawan_id', 'sttunjangankaryawan_id')->where(['sttunjangankaryawandet_active' => '1']);
    }

    function wajibpajak() {
        return $this->belongsTo('App\Model\Master\WajibPajakModel', 'ms_wajibpajak_id', 'wajibpajak_id');
    }
}
