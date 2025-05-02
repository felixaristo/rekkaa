<?php

namespace App\Model\Master;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class WajibPajakModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'ms_wajib_pajak';
    protected $primaryKey = 'wajibpajak_id';
    public $timestamps = false;
    
    protected static $logName = 'wajib_pajak';
    protected static $logUnguarded = true;
    protected $labelTableName = 'ms_wajib_pajak';

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

        $defaultCauserName = 'System';

        if($eventName == 'created') {
            return "$defaultCauserName menambahkan wajib pajak baru dengan ID ";
        } else {
            return "$defaultCauserName melakukan perubahan data wajib pajak dengan ID ";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    function wajibpajaksubscription() {
        return $this->hasOne('App\Model\Transaction\WajibPajakSubscriptionModel', 'ms_wajibpajak_id', 'wajibpajak_id')->where(['wajibpajaksubscription_active' => 1])->orderBy('wajibpajaksubscription_expired_at', 'DESC');
    }

    function userorder() {
        return $this->hasOne('App\Model\Transaction\UserOrderModel', 'ms_wajibpajak_id', 'wajibpajak_id')->where(['userorder_active' => 1])->orderBy('userorder_expired_at', 'DESC');
    }

    function paiduserorder() {
        return $this->hasOne('App\Model\Transaction\UserOrderModel', 'ms_wajibpajak_id', 'wajibpajak_id')->where(['userorder_active' => 1, 'userorder_status' => 'PAID'])->orderBy('userorder_expired_at', 'DESC');
    }

    function user() {
        return $this->hasOne('App\User', 'user_id', 'ms_user_id');
    }

    public function karyawans()
    {
        return $this->hasMany(KaryawanModel::class, 'ms_wajibpajak_id');
    }
    
    function userwajibpajak() {
        return $this->hasOne('App\Model\MasterRelation\MrUserWajibPajakModel', 'ms_wajibpajak_id', 'wajibpajak_id');
    }

    function regency() {
        return $this->hasOne('App\Model\Master\RegencyModel', 'regency_id', 'ms_regency_id');
    }

    function country() {
        return $this->hasOne('App\Model\Master\CountryModel', 'country_id', 'ms_country_id');
    }

    function klu() {
        return $this->hasOne('App\Model\Master\KluModel', 'klu_id', 'ms_klu_id');
    }

    function wpstpenggajian() {
        return $this->hasOne('App\Model\Setting\SettingPenggajianKaryawanModel', 'ms_wajibpajak_id', 'wajibpajak_id');
    }
}
