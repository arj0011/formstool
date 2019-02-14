<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Form extends Model
{
    //
    use SoftDeletes;
    protected $dates=['deleted_at'];

     public function getNameAttribute($value)
    {
        return ucwords($value);
    }
  
}
