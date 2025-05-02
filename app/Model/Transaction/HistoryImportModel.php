<?php

namespace App\Model\Transaction;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class HistoryImportModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'tr_history_import';
    protected $primaryKey = 'historyimport_id';
    public $timestamps = false;

    protected static $logName = 'history_import';
    protected static $logUnguarded = true;
    protected $labelTableName = 'tr_history_import';

    protected static $defaultCauserId = null;
    
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

    public function setCauserId($defaultCauserId)
    {
        $this::$defaultCauserId = $defaultCauserId;
    }

    public function getCauserId()
    {
        if($this::$defaultCauserId) {
            $defaultCauserId = $this::$defaultCauserId;
        } else {
            $defaultCauserId = isset(session()->get('user_data')['user_id']) ? session()->get('user_data')['user_id'] : session()->get('karyawan_data')['karyawan_id']; // Set the default causer_type here
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
            return "$defaultCauserName menambahkan import data ";
        } else {
            return "$defaultCauserName melakukan perubahan import data ";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    function wajibpajak() {
        return $this->belongsTo('App\Model\Master\WajibPajakModel', 'ms_wajibpajak_id', 'wajibpajak_id');
    }

    function user() {
        return $this->belongsTo('App\User', 'ms_user_id', 'user_id');
    }
}
