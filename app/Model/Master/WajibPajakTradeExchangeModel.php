<?php

namespace App\Model\Master;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class WajibPajakTradeExchangeModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'ms_wajib_pajak_trade_exchange';
    protected $primaryKey = 'wajibpajaktradeexchange_id';
    public $timestamps = false;

    protected static $logName = 'wajib_pajak_trade_exchange';
    protected static $logUnguarded = true;
    protected $labelTableName = 'ms_wajib_pajak_trade_exchange';

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
            return "$defaultCauserName menambahkan Customer baru dengan ID ";
        } else {
            return "$defaultCauserName melakukan perubahan data Customer dengan ID ";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    public function billing_country()
    {
        return $this->belongsTo(CountryModel::class, 'wajibpajaktradeexchange_billing_countries', 'country_id');
    }

    public function billing_province()
    {
        return $this->belongsTo(ProvinceModel::class, 'wajibpajaktradeexchange_billing_province', 'province_id');
    }

    public function billing_regency()
    {
        return $this->belongsTo(RegencyModel::class, 'wajibpajaktradeexchange_billing_city', 'regency_id');
    }

    public function shipping_country()
    {
        return $this->belongsTo(CountryModel::class, 'wajibpajaktradeexchange_shipping_countries', 'country_id');
    }

    public function shipping_province()
    {
        return $this->belongsTo(ProvinceModel::class, 'wajibpajaktradeexchange_shipping_province', 'province_id');
    }

    public function shipping_regency()
    {
        return $this->belongsTo(RegencyModel::class, 'wajibpajaktradeexchange_shipping_city', 'regency_id');
    }

    public function bank()
    {
        return $this->belongsTo(BankModel::class, 'ms_bank_id', 'bank_id');
    }
}
