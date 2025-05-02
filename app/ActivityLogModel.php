<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class ActivityLogModel extends Model
{
    use LogsActivity;
    protected static $logOnlyDirty = true;

    protected $table = 'activity_log';
    protected $primaryKey = 'id';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [

    // ];

    /**
     * The attributes that are mass guard.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
