<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserGroup extends Model
{
   protected $table = 'user_groups';

    use SoftDeletes;

   public function getGroupNameAttribute($value)
    {
        return ucwords($value);
    }

}
