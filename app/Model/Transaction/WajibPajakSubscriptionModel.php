<?php

namespace App\Model\Transaction;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class WajibPajakSubscriptionModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'tr_wajib_pajak_subscription';
    protected $primaryKey = 'wajibpajaksubscription_id';
    public $timestamps = false;

    protected static $logName = 'wajib_pajak_subscription';
    protected static $logUnguarded = true;
    protected $labelTableName = 'tr_wajib_pajak_subscription';

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
            return "$defaultCauserName menambahkan wajib pajak subscription baru dengan ID ";
        } else {
            return "$defaultCauserName melakukan perubahan data wajib pajak subscription dengan ID ";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    function user() {
        return $this->hasOne('App\User', 'user_id', 'ms_user_id');
    }

    function wajibpajak() {
        return $this->hasOne('App\Model\Master\WajibPajakModel', 'wajibpajak_id', 'ms_wajibpajak_id');
    }

    function subscription() {
        return $this->hasOne('App\Model\Master\SubscriptionModel', 'subscription_id', 'ms_subscription_id');
    }

    function subscriptionmenu() {
        return $this->hasOne('App\Model\MasterRelation\MrSubscriptionMenuModel', 'tr_wajib_pajak_subscription.ms_subscription_id', 'mr_subscription_menu.ms_subscription_id');
    }


    function userorder() {
        return $this->hasOne('App\Model\Transaction\UserOrderModel', 'userorder_id', 'tr_userorder_id');
    }
}
