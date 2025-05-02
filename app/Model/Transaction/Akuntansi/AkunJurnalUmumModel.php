<?php

namespace App\Model\Transaction\Akuntansi;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class AkunJurnalUmumModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'tr_akun_jurnal';
    protected $primaryKey = 'akunjurnal_id';
    public $timestamps = false;

    protected static $logName = 'akunjurnal';
    protected static $logUnguarded = true;
    protected $labelTableName = 'tr_akun_jurnal';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
        
    // ];

    protected $guarded = [
        
    ];

    public function getLogName()
    {
        return $this::$logName;
    }

    public function getCauserId()
    {
        if(isset(session()->get('user_data')['user_id'])) {
            $defaultCauserId = session()->get('user_data')['user_id'];
        } else {
            $defaultCauserId = 0;
        }

        return $defaultCauserId;
    }

    public function getCauserTypeModel()
    {
        if(isset(session()->get('user_data')['user_id'])) {
            $defaultCauserType = 'App\Model\MasterRelation\MrUserWajibPajakModel';
        } else {
            $defaultCauserType = 'System';
        }

        return $defaultCauserType;
    }

    public function getCauserType()
    {
        if(isset(session()->get('user_data')['user_id'])) {
            $defaultCauserType = 'ADMIN';
        } else {
            $defaultCauserType = 'System';
        }

        return $defaultCauserType;
    }

    public function getCauserName()
    {
        if(isset(session()->get('user_data')['user_email'])) {
            $defaultCauserName = session()->get('user_data')['user_email'];
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
            return "$defaultCauserName menambahkan jurnal baru dengan ID ";
        } else {
            return "$defaultCauserName melakukan perubahan data jurnal dengan ID ";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    // function payroll() {
    //     return $this->hasMany('App\Model\Transaction\PayrollModel', 'ms_karyawan_id', 'karyawan_id');
    // }
}
