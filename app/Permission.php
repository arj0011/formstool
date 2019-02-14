<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    
    /**
     * Get the module record associated with the permission.
     */

     public function modules()
    {
        return $this->belongsTo('App\Module');
    }

     public function getNameAttribute($value)
    {
        return ucwords($value);
    }
}
