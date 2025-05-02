<?php

namespace App\Model\Transaction;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class LogActivityModel extends SpatieActivity
{
    protected $table = 'activity_log';
    protected $primaryKey = 'id';
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $guarded = [];
}
