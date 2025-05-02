<?php

namespace App\Model\Master;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class KaryawanMasakerjaModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'tr_karyawan_masakerja';
    protected $primaryKey = 'karyawanmasakerja_id';
    public $timestamps = false;

    protected static $logName = 'karyawan_masakerja';
    protected static $logUnguarded = true;
    protected $labelTableName = 'ms_karyawan_masakerja';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
        
    // ];

    protected $guarded = [
        
    ];

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
            return "$defaultCauserName menambahkan masa kerja karyawan baru dengan ID ";
        } else {
            return "$defaultCauserName melakukan perubahan data masa kerja karyawan dengan ID ";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    public function getKaryawanmasakerjaBpjsAttribute($value) {
        // return $this->attributes['karyawanmasakerja_bpjs'] = json_decode($value);
        return json_decode($value);
    }

    public function getKaryawanmasakerjaTunjanganAttribute($value) {
        // return $this->attributes['karyawanmasakerja_bpjs'] = json_decode($value);
        return json_decode($value);
    }

    public function getKaryawankalkulasiBpjsAttribute($value) {
        // return $this->attributes['karyawanmasakerja_bpjs'] = json_decode($value);
        return json_decode($value);
    }

    public function getKaryawankalkulasiTunjanganAttribute($value) {
        // return $this->attributes['karyawanmasakerja_bpjs'] = json_decode($value);
        return json_decode($value);
    }

    // public function getKaryawankalkulasiTunjanganjabatanAttribute($value) {
    //     // return $this->attributes['karyawanmasakerja_bpjs'] = json_decode($value);
    //     return json_decode($value);
    // }

    function karyawan() {
        return $this->belongsTo('App\Model\Master\KaryawanModel', 'ms_karyawan_id', 'karyawan_id');
    }

    function ptkp() {
        return $this->hasOne('App\Model\Master\PtkpModel', 'ptkp_id', 'ms_ptkp_id');
    }

    function objekpajak() {
        return $this->hasOne('App\Model\Master\ObjekPajakModel', 'objekpajak_code', 'ms_objekpajak_code');
    }
    
    function wajibpajak() {
        return $this->belongsTo('App\Model\Master\WajibPajakModel', 'ms_wajibpajak_id', 'wajibpajak_id');
    }
}
