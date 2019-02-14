<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    
    public function users()
    {
    	return $this->belongsToMany('App\User');
    }

    public function permissions()
    {
    	return $this->belongsToMany('App\Permission');
    }

    public function getNameAttribute($value)
    {
        return ucwords($value);
    }
	
}
