<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormGroup extends Model
{
	use SoftDeletes;
    
    protected $table = 'form_groups';

    public function getGroupNameAttribute($value)
    {
        return ucwords($value);
    }
}
