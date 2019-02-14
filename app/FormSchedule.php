<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormSchedule extends Model
{
    protected  $table = 'form_schedules';

    use SoftDeletes;

    public function getScheduleNameAttribute($value)
    {
        return ucwords($value);
    }
}
