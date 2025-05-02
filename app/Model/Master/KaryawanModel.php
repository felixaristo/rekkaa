<?php

namespace App\Model\Master;

use App\Model\Transaction\AttendanceKaryawanModel;
use App\Model\Transaction\LeaveKaryawanModel;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class KaryawanModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'ms_karyawan';
    protected $primaryKey = 'karyawan_id';
    public $timestamps = false;

    protected static $logName = 'karyawan';
    protected static $logUnguarded = true;
    protected $labelTableName = 'ms_karyawan';

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
            return "$defaultCauserName menambahkan karyawan baru dengan ID ";
        } else {
            return "$defaultCauserName melakukan perubahan data karyawan dengan ID ";
        }

        // return "[$defaultCauserType] $defaultCauserName, {$eventName} by: " . $defaultCauserName;
    }

    function masakerja() {
        return $this->hasOne('App\Model\Master\KaryawanMasakerjaModel', 'ms_karyawan_id', 'karyawan_id')
        ->with(['ptkp', 'objekpajak'])
        ->where(['karyawanmasakerja_active' => 1]);
    }

    function bank() {
        return $this->hasOne('App\Model\Master\BankModel', 'bank_id', 'ms_bank_id');
    }

    function penggajian() {
        return $this->hasOne('App\Model\Setting\SettingPenggajianKaryawanModel', 'stpenggajiankaryawan_id', 'st_penggajian_id');
    }

    function ptkp() {
        return $this->hasOne('App\Model\Master\PtkpModel', 'ptkp_id', 'ms_ptkp_id');
    }

    function manager() {
        return $this->belongsTo('App\Model\Master\KaryawanModel', 'karyawan_manager_id', 'karyawan_id');
    }

    function divisi() {
        return $this->hasOne('App\Model\Master\KaryawanDivisiModel', 'karyawandivisi_id', 'ms_karyawandivisi_id');
    }

    function jabatan() {
        return $this->hasOne('App\Model\Master\KaryawanJabatanModel', 'karyawanjabatan_id', 'ms_karyawanjabatan_id');
    }

    // function currentperiod_payroll() {
    //     $current_period = date('Y-m');
    //     return $this->hasOne('App\Model\Transaction\PayrollModel', 'ms_karyawan_id', 'karyawan_id')->whereRaw("(TO_CHAR(payroll_period, 'YYYY-MM') = '{$current_period}')");
    // }

    function attendance() {
        return $this->hasOne('App\Model\Setting\SettingAttendanceModel', 'attendance_id', 'st_attendance_id')->where(['attendance_status_active' => TRUE]);
    }

    function wajibpajak() {
        return $this->belongsTo('App\Model\Master\WajibPajakModel', 'ms_wajibpajak_id', 'wajibpajak_id');
    }

    // function tunjangan() {
        // return $this->hasMany('App\Model\Setting\SettingTunjanganKaryawanModel')->whereIn('sttunjangankaryawan_id', 'st_tunjangan_id');
    // }

    // function ptkp() {
    //     return $this->masakerja()->with(['ptkp', 'objekpajak']);
    // }

    public function attendanceKaryawans()
    {
        return $this->hasMany(AttendanceKaryawanModel::class, 'ms_karyawan_id');
    }

    public function leaveKaryawans()
    {
        return $this->hasMany(LeaveKaryawanModel::class, 'ms_karyawan_id');
    }

    function payroll() {
        return $this->hasMany('App\Model\Transaction\PayrollModel', 'ms_karyawan_id', 'karyawan_id');
    }

    function tunjangandetail() {
        return $this->hasMany('App\Model\Setting\SettingTunjanganKaryawanDetailModel', 'ms_karyawan_id', 'karyawan_id')->where(['sttunjangankaryawandet_active' => '1']);
    }

    function potongandetail() {
        return $this->hasMany('App\Model\Setting\SettingPotonganKaryawanDetailModel', 'ms_karyawan_id', 'karyawan_id')->where(['stpotongankaryawandet_active' => '1']);
    }

    function objekpajak() {
        return $this->hasOne('App\Model\Master\ObjekPajakModel', 'objekpajak_code', 'karyawan_code_objekpajak');
    }

    function country() {
        return $this->hasOne('App\Model\Master\CountryModel', 'country_id', 'ms_country_id');
    }

    function payrollExist() {
        return $this->hasOne('App\Model\Transaction\PayrollModel', 'ms_karyawan_id', 'karyawan_id')->where(['payroll_active' => '1', 'payroll_lock' => 1]);
    }

    function payrollExistCurrentYear() {
        return $this->hasOne('App\Model\Transaction\PayrollModel', 'ms_karyawan_id', 'karyawan_id')->where(['payroll_active' => '1', 'payroll_lock' => 1])->whereRaw("(TO_CHAR(payroll_period, 'YYYY') = ?)", [date('Y')]);
    }
}
