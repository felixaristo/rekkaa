<?php

namespace App\Model\Transaction;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class PPh21Model extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'tr_pph21';
    protected $primaryKey = 'pph21_id';
    public $timestamps = false;

    protected static $logName = 'pph21';
    protected static $logUnguarded = true;
    protected $labelTableName = 'tr_pph21';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function getLogName()
    {
        return $this::$logName;
    }

    public function getCauserId()
    {
        if(isset(session()->get('user_data')['user_id'])) {
            $defaultCauserId = session()->get('user_data')['user_id'];
        } else if(isset(session()->get('karyawan_data')['karyawan_id'])) {
            $defaultCauserId = session()->get('karyawan_data')['karyawan_id'];
        } else {
            $defaultCauserId = 0;
        }

        return $defaultCauserId;
    }

    public function getCauserTypeModel()
    {
        if(isset(session()->get('user_data')['user_id'])) {
            $defaultCauserType = 'App\Model\MasterRelation\MrUserWajibPajakModel';
        } else if(isset(session()->get('karyawan_data')['karyawan_id'])) {
            $defaultCauserType = 'App\Model\Master\KaryawanModel';
        } else {
            $defaultCauserType = 'System';
        }

        return $defaultCauserType;
    }

    public function getCauserType()
    {
        if(isset(session()->get('user_data')['user_id'])) {
            $defaultCauserType = 'ADMIN';
        } else if(isset(session()->get('karyawan_data')['karyawan_id'])) {
            $defaultCauserType = 'KARYAWAN';
        } else {
            $defaultCauserType = 'System';
        }

        return $defaultCauserType;
    }

    public function getCauserName()
    {
        if(isset(session()->get('user_data')['user_email'])) {
            $defaultCauserName = session()->get('user_data')['user_email'];
        } else if(isset(session()->get('karyawan_data')['karyawan_email'])) {
            $defaultCauserName = session()->get('karyawan_data')['karyawan_email'];
        } else {
            $defaultCauserName = 'System';
        }
        
        return $defaultCauserName;
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $defaultCauserType = $this->getCauserTypeModel();
        $defaultCauserId = $this->getCauserId();
        
        $wajibpajak_id = $this->wajibpajak ? $this->wajibpajak->wajibpajak_id : null;
    
        $activity->ms_wajibpajak_id = $wajibpajak_id;
        $activity->causer_id = $defaultCauserId;
        $activity->causer_type = $defaultCauserType;
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        // $defaultCauserType = $this->getCauserType();
        $defaultCauserName = $this->getCauserName();

        if($eventName == 'created') {
            return "$defaultCauserName menambahkan pph21 baru dengan ID ";
        } else {
            return "$defaultCauserName melakukan perubahan data pph21 dengan ID ";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    function user() {
        return $this->hasOne('App\User', 'user_id', 'ms_user_id');
    }

    function wajibpajak() {
        return $this->hasOne('App\Model\Master\WajibPajakModel', 'wajibpajak_id', 'ms_wajibpajak_id');
    }

    function karyawan() {
        return $this->hasOne('App\Model\Master\KaryawanModel', 'karyawan_id', 'ms_karyawan_id');
    }

    function payroll() {
        return $this->belongsTo('App\Model\Transaction\PayrollModel', 'tr_payroll_uuid', 'payroll_uuid');
    }
}
